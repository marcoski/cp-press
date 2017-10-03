<?php

namespace Commonhelp\Rss\Parser;

use SimpleXMLElement;
use Commonhelp\Client\Url;
use Commonhelp\Rss\Encoding;
use Commonhelp\Rss\Filter\Filter;
use Commonhelp\Rss\Scraper\Scraper;
use Commonhelp\Util\XmlParser;

/**
 * Base parser class.
 *
 * @author  Frederic Guillot
 */
abstract class Parser{

    /**
     * DateParser object.
     *
     * @var \Commonhelp\Rss\Parser\DateParser
     */
    protected $date;

    /**
     * Hash algorithm used to generate item id, any value supported by PHP, see hashAlgos().
     *
     * @var string
     */
    private $hashAlgo = 'sha256';

    /**
     * Feed content (XML data).
     *
     * @var string
     */
    protected $content = '';

    /**
     * Fallback url.
     *
     * @var string
     */
    protected $fallbackUrl = '';

    /**
     * XML namespaces supported by parser.
     *
     * @var array
     */
    protected $namespaces = array();

    /**
     * XML namespaces used in document.
     *
     * @var array
     */
    protected $usedNamespaces = array();

    /**
     * Enable the content filtering.
     *
     * @var bool
     */
    private $enableFilter = true;

    /**
     * Enable the content grabber.
     *
     * @var bool
     */
    private $enableGrabber = false;

    /**
     * Enable the content grabber on all pages.
     *
     * @var bool
     */
    private $grabberNeedsRuleFile = false;

    /**
     * Ignore those urls for the content scraper.
     *
     * @var array
     */
    private $grabberIgnoreUrls = array();

    private $config;
    
    /**
     * Constructor.
     *
     * @param string $content       Feed content
     * @param string $http_encoding HTTP encoding (headers)
     * @param string $fallbackUrl  Fallback url when the feed provide relative or broken url
     */
    public function __construct($content, $http_encoding = '', $fallbackUrl = ''){
        $this->date = new DateParser();
        $this->fallbackUrl = $fallbackUrl;
        $xml_encoding = XmlParser::getEncodingFromXmlTag($content);

        // Strip XML tag to avoid multiple encoding/decoding in the next XML processing
        $this->content = Filter::stripXmlTag($content);

        // Encode everything in UTF-8
        $this->content = Encoding::convert($this->content, $xml_encoding ?: $http_encoding);
    }

    /**
     * Parse the document.
     *
     * @return \PicoFeed\Parser\Feed
     */
    public function execute(){
        $xml = XmlParser::getSimpleXml($this->content);

        if ($xml === false) {
            $this->content = Filter::normalizeData($this->content);
            $xml = XmlParser::getSimpleXml($this->content);

            if ($xml === false) {
                throw new MalformedXmlException('XML parsing error');
            }
        }

        $this->usedNamespaces = $xml->getNamespaces(true);
        $xml = $this->registerSupportedNamespaces($xml);

        $feed = new Feed();

        $this->findFeedUrl($xml, $feed);
        $this->checkFeedUrl($feed);

        $this->findSiteUrl($xml, $feed);
        $this->checkSiteUrl($feed);

        $this->findFeedTitle($xml, $feed);
        $this->findFeedDescription($xml, $feed);
        $this->findFeedLanguage($xml, $feed);
        $this->findFeedId($xml, $feed);
        $this->findFeedDate($xml, $feed);
        $this->findFeedLogo($xml, $feed);
        $this->findFeedIcon($xml, $feed);

        foreach ($this->getItemsTree($xml) as $entry) {
            $entry = $this->registerSupportedNamespaces($entry);

            $item = new Item();
            $item->xml = $entry;
            $item->namespaces = $this->usedNamespaces;

            $this->findItemAuthor($xml, $entry, $item);

            $this->findItemUrl($entry, $item);
            $this->checkItemUrl($feed, $item);

            $this->findItemTitle($entry, $item);
            $this->findItemContent($entry, $item);

            // Id generation can use the item url/title/content (order is important)
            $this->findItemId($entry, $item, $feed);

            $this->findItemDate($entry, $item, $feed);
            $this->findItemEnclosure($entry, $item, $feed);
            $this->findItemLanguage($entry, $item, $feed);

            // Order is important (avoid double filtering)
            $this->filterItemContent($feed, $item);
            $this->scrapWebsite($item);

            $feed->items[] = $item;
        }

        return $feed;
    }

    /**
     * Check if the feed url is correct.
     *
     * @param Feed $feed Feed object
     */
    public function checkFeedUrl(Feed $feed){
        if ($feed->getFeedUrl() === '') {
            $feed->feed_url = $this->fallbackUrl;
        } else {
            $feed->feed_url = Url::resolve($feed->getFeedUrl(), $this->fallbackUrl);
        }
    }

    /**
     * Check if the site url is correct.
     *
     * @param Feed $feed Feed object
     */
    public function checkSiteUrl(Feed $feed){
        if ($feed->getSiteUrl() === '') {
            $feed->site_url = Url::base($feed->getFeedUrl());
        } else {
            $feed->site_url = Url::resolve($feed->getSiteUrl(), $this->fallbackUrl);
        }
    }

    /**
     * Check if the item url is correct.
     *
     * @param Feed $feed Feed object
     * @param Item $item Item object
     */
    public function checkItemUrl(Feed $feed, Item $item){
        $item->url = Url::resolve($item->getUrl(), $feed->getSiteUrl());
    }

    /**
     * Fetch item content with the content grabber.
     *
     * @param Item $item Item object
     */
    public function scrapWebsite(Item $item){
        if ($this->enableGrabber && !in_array($item->getUrl(), $this->grabberIgnoreUrls)) {
            $grabber = new Scraper($this->config);
            $grabber->setUrl($item->getUrl());

            if ($this->grabberNeedsRuleFile) {
                $grabber->disableCandidateParser();
            }

            $grabber->execute();

            if ($grabber->hasRelevantContent()) {
                $item->content = $grabber->getFilteredContent();
            }
        }
    }

    /**
     * Filter HTML for entry content.
     *
     * @param Feed $feed Feed object
     * @param Item $item Item object
     */
    public function filterItemContent(Feed $feed, Item $item){
        if ($this->isFilteringEnabled()) {
            $filter = Filter::html($item->getContent(), $feed->getSiteUrl());
            $filter->setConfig($this->config);
            $item->content = $filter->execute();
        } else {
            Logger::setMessage(get_called_class().': Content filtering disabled');
        }
    }

    /**
     * Generate a unique id for an entry (hash all arguments).
     *
     * @return string
     */
    public function generateId(){
        return hash($this->hashAlgo, implode(func_get_args()));
    }

    /**
     * Return true if the given language is "Right to Left".
     *
     * @static
     *
     * @param string $language Language: fr-FR, en-US
     *
     * @return bool
     */
    public static function isLanguageRTL($language){
        $language = strtolower($language);

        $rtl_languages = array(
            'ar', // Arabic (ar-**)
            'fa', // Farsi (fa-**)
            'ur', // Urdu (ur-**)
            'ps', // Pashtu (ps-**)
            'syr', // Syriac (syr-**)
            'dv', // Divehi (dv-**)
            'he', // Hebrew (he-**)
            'yi', // Yiddish (yi-**)
        );

        foreach ($rtl_languages as $prefix) {
            if (strpos($language, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set Hash algorithm used for id generation.
     *
     * @param string $algo Algorithm name
     *
     * @return \Commonhelp\Rss\Parser\Parser
     */
    public function setHashAlgo($algo){
        $this->hashAlgo = $algo ?: $this->hashAlgo;

        return $this;
    }

    /**
     * Set a different timezone.
     *
     * @see    http://php.net/manual/en/timezones.php
     *
     * @param string $timezone Timezone
     *
     * @return \Commonhelp\Rss\Parser\Parser
     */
    public function setTimezone($timezone){
        if ($timezone) {
            $this->date->timezone = $timezone;
        }

        return $this;
    }
    
    /**
     * Set config object.
     *
     * @param \Commonhelp\Config\Config $config Config instance
     *
     * @return \Commonhelp\Rss\Parser\Parser
     */
    public function setConfig($config){
    	$this->config = $config;
    	return $this;
    }

    /**
     * Enable the content grabber.
     *
     * @return \Commonhelp\Rss\Parser\Parser
     */
    public function disableContentFiltering(){
        $this->enableFilter = false;
    }

    /**
     * Return true if the content filtering is enabled.
     *
     * @return bool
     */
    public function isFilteringEnabled(){
        if ($this->config === null) {
            return $this->enableFilter;
        }
        return $this->config->getContentFiltering($this->enableFilter);
    }

    /**
     * Enable the content grabber.
     *
     * @param bool $needs_rule_file true if only pages with rule files should be
     *                              scraped
     *
     * @return \Commonhelp\Rss\Parser\Parser
     */
    public function enableContentGrabber($needsRuleFile = false){
        $this->enableGrabber = true;
        $this->grabberNeedsRuleFile = $needsRuleFile;
    }

    /**
     * Set ignored URLs for the content grabber.
     *
     * @param array $urls URLs
     *
     * @return \Commonhelp\Rss\Parser\Parser
     */
    public function setGrabberIgnoreUrls(array $urls){
        $this->grabberIgnoreUrls = $urls;
    }

    /**
     * Register all supported namespaces to be used within an xpath query.
     *
     * @param SimpleXMLElement $xml Feed xml
     *
     * @return SimpleXMLElement
     */
    public function registerSupportedNamespaces(SimpleXMLElement $xml){
        foreach ($this->namespaces as $prefix => $ns) {
            $xml->registerXPathNamespace($prefix, $ns);
        }

        return $xml;
    }

    /**
     * Find the feed url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    abstract public function findFeedUrl(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the site url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    abstract public function findSiteUrl(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed title.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    abstract public function findFeedTitle(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed description.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    abstract public function findFeedDescription(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed language.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    abstract public function findFeedLanguage(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed id.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    abstract public function findFeedId(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed date.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    abstract public function findFeedDate(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed logo url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    abstract public function findFeedLogo(SimpleXMLElement $xml, Feed $feed);

    /**
     * Find the feed icon.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    abstract public function findFeedIcon(SimpleXMLElement $xml, Feed $feed);

    /**
     * Get the path to the items XML tree.
     *
     * @param SimpleXMLElement $xml Feed xml
     *
     * @return SimpleXMLElement
     */
    abstract public function getItemsTree(SimpleXMLElement $xml);

    /**
     * Find the item author.
     *
     * @param SimpleXMLElement      $xml   Feed
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     */
    abstract public function findItemAuthor(SimpleXMLElement $xml, SimpleXMLElement $entry, Item $item);

    /**
     * Find the item URL.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     */
    abstract public function findItemUrl(SimpleXMLElement $entry, Item $item);

    /**
     * Find the item title.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     */
    abstract public function findItemTitle(SimpleXMLElement $entry, Item $item);

    /**
     * Genereate the item id.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     * @param \Commonhelp\Rss\Parser\Feed $feed  Feed object
     */
    abstract public function findItemId(SimpleXMLElement $entry, Item $item, Feed $feed);

    /**
     * Find the item date.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item                  $item  Item object
     * @param \Commonhelp\Rss\Parser\Feed $feed  Feed object
     */
    abstract public function findItemDate(SimpleXMLElement $entry, Item $item, Feed $feed);

    /**
     * Find the item content.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     */
    abstract public function findItemContent(SimpleXMLElement $entry, Item $item);

    /**
     * Find the item enclosure.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     * @param \Commonhelp\Rss\Parser\Feed $feed  Feed object
     */
    abstract public function findItemEnclosure(SimpleXMLElement $entry, Item $item, Feed $feed);

    /**
     * Find the item language.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     * @param \Commonhelp\Rss\Parser\Feed $feed  Feed object
     */
    abstract public function findItemLanguage(SimpleXMLElement $entry, Item $item, Feed $feed);
}