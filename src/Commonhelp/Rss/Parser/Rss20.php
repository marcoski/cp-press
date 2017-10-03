<?php

namespace Commonhelp\Rss\Parser;

use SimpleXMLElement;
use Commonhelp\Rss\Filter\Filter;
use Commonhelp\Client\Url;
use Commonhelp\Util\XmlParser;

/**
 * RSS 2.0 Parser.
 *
 */
class Rss20 extends Parser{
    /**
     * Supported namespaces.
     */
    protected $namespaces = array(
        'dc' => 'http://purl.org/dc/elements/1.1/',
        'content' => 'http://purl.org/rss/1.0/modules/content/',
        'feedburner' => 'http://rssnamespace.org/feedburner/ext/1.0',
        'atom' => 'http://www.w3.org/2005/Atom',
    );

    /**
     * Get the path to the items XML tree.
     *
     * @param SimpleXMLElement $xml Feed xml
     *
     * @return SimpleXMLElement
     */
    public function getItemsTree(SimpleXMLElement $xml){
        return XmlParser::getXPathResult($xml, 'channel/item');
    }

    /**
     * Find the feed url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    public function findFeedUrl(SimpleXMLElement $xml, Feed $feed){
        $feed->feedUrl = '';
    }

    /**
     * Find the site url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    public function findSiteUrl(SimpleXMLElement $xml, Feed $feed){
        $siteUrl = XmlParser::getXPathResult($xml, 'channel/link');
        $feed->siteUrl = (string) current($siteUrl);
    }

    /**
     * Find the feed description.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    public function findFeedDescription(SimpleXMLElement $xml, Feed $feed){
        $description = XmlParser::getXPathResult($xml, 'channel/description');
        $feed->description = (string) current($description);
    }

    /**
     * Find the feed logo url.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    public function findFeedLogo(SimpleXMLElement $xml, Feed $feed){
        $logo = XmlParser::getXPathResult($xml, 'channel/image/url');
        $feed->logo = (string) current($logo);
    }

    /**
     * Find the feed icon.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    public function findFeedIcon(SimpleXMLElement $xml, Feed $feed){
        $feed->icon = '';
    }

    /**
     * Find the feed title.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    public function findFeedTitle(SimpleXMLElement $xml, Feed $feed){
        $title = XmlParser::getXPathResult($xml, 'channel/title');
        $feed->title = Filter::stripWhiteSpace((string) current($title)) ?: $feed->getSiteUrl();
    }

    /**
     * Find the feed language.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    public function findFeedLanguage(SimpleXMLElement $xml, Feed $feed){
        $language = XmlParser::getXPathResult($xml, 'channel/language');
        $feed->language = (string) current($language);
    }

    /**
     * Find the feed id.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    public function findFeedId(SimpleXMLElement $xml, Feed $feed){
        $feed->id = $feed->getFeedUrl() ?: $feed->getSiteUrl();
    }

    /**
     * Find the feed date.
     *
     * @param SimpleXMLElement      $xml  Feed xml
     * @param \Commonhelp\Rss\Parser\Feed $feed Feed object
     */
    public function findFeedDate(SimpleXMLElement $xml, Feed $feed){
        $publishDate = XmlParser::getXPathResult($xml, 'channel/pubDate');
        $updateDate = XmlParser::getXPathResult($xml, 'channel/lastBuildDate');

        $published = !empty($publish_date) ? $this->date->getDateTime((string) current($publishDate)) : null;
        $updated = !empty($update_date) ? $this->date->getDateTime((string) current($updateDate)) : null;

        if ($published === null && $updated === null) {
            $feed->date = $this->date->getCurrentDateTime(); // We use the current date if there is no date for the feed
        } elseif ($published !== null && $updated !== null) {
            $feed->date = max($published, $updated); // We use the most recent date between published and updated
        } else {
            $feed->date = $updated ?: $published;
        }
    }

    /**
     * Find the item date.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param Item                  $item  Item object
     * @param \Commonhelp\Rss\Parser\Feed $feed  Feed object
     */
    public function findItemDate(SimpleXMLElement $entry, Item $item, Feed $feed){
        $date = XmlParser::getXPathResult($entry, 'pubDate');

        $item->date = empty($date) ? $feed->getDate() : $this->date->getDateTime((string) current($date));
    }

    /**
     * Find the item title.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     */
    public function findItemTitle(SimpleXMLElement $entry, Item $item){
        $title = XmlParser::getXPathResult($entry, 'title');
        $item->title = Filter::stripWhiteSpace((string) current($title)) ?: $item->url;
    }

    /**
     * Find the item author.
     *
     * @param SimpleXMLElement      $xml   Feed
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     */
    public function findItemAuthor(SimpleXMLElement $xml, SimpleXMLElement $entry, Item $item){
        $author = XmlParser::getXPathResult($entry, 'dc:creator', $this->namespaces)
                  ?: XmlParser::getXPathResult($entry, 'author')
                  ?: XmlParser::getXPathResult($xml, 'channel/dc:creator', $this->namespaces)
                  ?: XmlParser::getXPathResult($xml, 'channel/managingEditor');

        $item->author = (string) current($author);
    }

    /**
     * Find the item content.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     */
    public function findItemContent(SimpleXMLElement $entry, Item $item){
        $content = XmlParser::getXPathResult($entry, 'content:encoded', $this->namespaces);

        if (trim((string) current($content)) === '') {
            $content = XmlParser::getXPathResult($entry, 'description');
        }

        $item->content = (string) current($content);
    }

    /**
     * Find the item URL.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     */
    public function findItemUrl(SimpleXMLElement $entry, Item $item){
        $link = XmlParser::getXPathResult($entry, 'feedburner:origLink', $this->namespaces)
                 ?: XmlParser::getXPathResult($entry, 'link')
                 ?: XmlParser::getXPathResult($entry, 'atom:link/@href', $this->namespaces);

        if (!empty($link)) {
            $item->url = trim((string) current($link));
        } else {
            $link = XmlParser::getXPathResult($entry, 'guid');
            $link = trim((string) current($link));

            if (filter_var($link, FILTER_VALIDATE_URL) !== false) {
                $item->url = $link;
            }
        }
    }

    /**
     * Genereate the item id.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     * @param \Commonhelp\Rss\Parser\Feed $feed  Feed object
     */
    public function findItemId(SimpleXMLElement $entry, Item $item, Feed $feed){
        $id = (string) current(XmlParser::getXPathResult($entry, 'guid'));

        if ($id) {
            $item->id = $this->generateId($id);
        } else {
            $item->id = $this->generateId(
                $item->getTitle(), $item->getUrl(), $item->getContent()
            );
        }
    }

    /**
     * Find the item enclosure.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     * @param \Commonhelp\Rss\Parser\Feed $feed  Feed object
     */
    public function findItemEnclosure(SimpleXMLElement $entry, Item $item, Feed $feed){
        if (isset($entry->enclosure)) {
            $enclosureUrl = XmlParser::getXPathResult($entry, 'feedburner:origEnclosureLink', $this->namespaces)
                             ?: XmlParser::getXPathResult($entry, 'enclosure/@url');

            $enclosureType = XmlParser::getXPathResult($entry, 'enclosure/@type');

            $item->enclosureUrl = Url::resolve((string) current($enclosureUrl), $feed->getSiteUrl());
            $item->enclosureType = (string) current($enclosureType);
        }
    }

    /**
     * Find the item language.
     *
     * @param SimpleXMLElement      $entry Feed item
     * @param \Commonhelp\Rss\Parser\Item $item  Item object
     * @param \Commonhelp\Rss\Parser\Feed $feed  Feed object
     */
    public function findItemLanguage(SimpleXMLElement $entry, Item $item, Feed $feed){
        $language = XmlParser::getXPathResult($entry, 'dc:language', $this->namespaces);

        $item->language = (string) current($language) ?: $feed->language;
    }
}