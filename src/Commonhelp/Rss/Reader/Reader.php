<?php

namespace Commonhelp\Rss\Reader;

use DOMXPath;

use Commonhelp\Client\Client;
use Commonhelp\Client\Url;

use Commonhelp\Rss\Exception\UnsupportedFeedFormatException;
use Commonhelp\Rss\Exception\SubscriptionNotFoundException;
use Commonhelp\Rss\RssConfig;
use Commonhelp\Util\XmlParser;

/**
 * Reader class.
 *
 */
class Reader{
    /**
     * Feed formats for detection.
     *
     * @var array
     */
    private $formats = array(
        'Atom' => '//feed',
        'Rss20' => '//rss[@version="2.0"]',
        'Rss92' => '//rss[@version="0.92"]',
        'Rss91' => '//rss[@version="0.91"]',
        'Rss10' => '//rdf',
    );

    
    private $config;
    
    public function __construct(RssConfig $config = null){
    	$this->config = $config ?: new RssConfig();
    }
   

    /**
     * Download a feed (no discovery).
     *
     * @param string $url           Feed url
     * @param string $lastModified Last modified HTTP header
     * @param string $etag          Etag HTTP header
     * @param string $username      HTTP basic auth username
     * @param string $password      HTTP basic auth password
     *
     * @return \Commonhelp\Client\Client
     */
    public function download($url, $lastModified = '', $etag = '', $username = '', $password = ''){
        $url = $this->prependScheme($url);

        return Client::getInstance()
       					->setConfig($this->config)
                        ->setLastModified($lastModified)
                        ->setEtag($etag)
                        ->setUsername($username)
                        ->setPassword($password)
                        ->execute($url);
    }

    /**
     * Discover and download a feed.
     *
     * @param string $url           Feed or website url
     * @param string $lastModified Last modified HTTP header
     * @param string $etag          Etag HTTP header
     * @param string $username      HTTP basic auth username
     * @param string $password      HTTP basic auth password
     *
     * @return \Commonhelp\Client\Client
     */
    public function discover($url, $lastModified = '', $etag = '', $username = '', $password = ''){
        $client = $this->download($url, $last_modified, $etag, $username, $password);

        // It's already a feed or the feed was not modified
        if (!$client->isModified() || $this->detectFormat($client->getContent())) {
            return $client;
        }

        // Try to find a subscription
        $links = $this->find($client->getUrl(), $client->getContent());

        if (empty($links)) {
            throw new SubscriptionNotFoundException('Unable to find a subscription');
        }

        return $this->download($links[0], $lastModified, $etag, $username, $password);
    }

    /**
     * Find feed urls inside a HTML document.
     *
     * @param string $url  Website url
     * @param string $html HTML content
     *
     * @return array List of feed links
     */
    public function find($url, $html){ 
    	$dom = XmlParser::getHtmlDocument($html);
        $xpath = new DOMXPath($dom);
        $links = array();

        $queries = array(
            '//link[@type="application/rss+xml"]',
            '//link[@type="application/atom+xml"]',
        );

        foreach ($queries as $query) {
            $nodes = $xpath->query($query);

            foreach ($nodes as $node) {
                $link = $node->getAttribute('href');

                if (!empty($link)) {
                    $feedUrl = new Url($link);
                    $siteUrl = new Url($url);

                    $links[] = $feedUrl->getAbsoluteUrl($feedUrl->isRelativeUrl() ? $siteUrl->getBaseUrl() : '');
                }
            }
        }

        return $links;
    }

    /**
     * Get a parser instance.
     *
     * @param string $url      Site url
     * @param string $content  Feed content
     * @param string $encoding HTTP encoding
     *
     * @return \PicoFeed\Parser\Parser
     */
    public function getParser($url, $content, $encoding){
        $format = $this->detectFormat($content);

        if (empty($format)) {
            throw new UnsupportedFeedFormatException('Unable to detect feed format');
        }

        $className = '\Commonhelp\Rss\Parser\\'.$format;

        $parser = new $className($content, $encoding, $url);
        $parser->setHashAlgo($this->config->getParserHashAlgo());
        $parser->setTimezone($this->config->getTimezone());
        $parser->setConfig($this->config);
        
        return $parser;
    }

    /**
     * Detect the feed format.
     *
     * @param string $content Feed content
     *
     * @return string
     */
    public function detectFormat($content){
        $dom = XmlParser::getHtmlDocument($content);
        $xpath = new DOMXPath($dom);

        foreach ($this->formats as $parser_name => $query) {
            $nodes = $xpath->query($query);

            if ($nodes->length === 1) {
                return $parser_name;
            }
        }

        return '';
    }

    /**
     * Add the prefix "http://" if the end-user just enter a domain name.
     *
     * @param string $url Url
     * @retunr string
     */
    public function prependScheme($url){
        if (!preg_match('%^https?://%', $url)) {
            $url = 'http://'.$url;
        }

        return $url;
    }
}