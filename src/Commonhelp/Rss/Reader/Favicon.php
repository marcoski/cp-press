<?php

namespace Commonhelp\Rss\Reader;

use DOMXpath;
use Commonhelp\Client\Client;
use Commonhelp\Client\Exception\ClientException;
use Commonelp\Client\Url;
use Commonelp\Util\XmlParser;
use Commonhelp\Rss\RssConfig;

/**
 * Favicon class.
 *
 * https://en.wikipedia.org/wiki/Favicon
 *
 */
class Favicon{
    /**
     * Valid types for favicon (supported by browsers).
     *
     * @var array
     */
    private $types = array(
        'image/png',
        'image/gif',
        'image/x-icon',
        'image/jpeg',
        'image/jpg',
    );

    
    /**
     * Icon binary content.
     *
     * @var string
     */
    private $content = '';

    /**
     * Icon content type.
     *
     * @var string
     */
    private $contentType = '';

    private $config;
    
    public function __construct(RssConfig $config = null){
    	$this->config = $config ?: new RssConfig();
    }
    
    /**
     * Get the icon file content (available only after the download).
     *
     * @return string
     */
    public function getContent(){
        return $this->content;
    }

    /**
     * Get the icon file type (available only after the download).
     *
     * @return string
     */
    public function getType(){
        foreach ($this->types as $type) {
            if (strpos($this->contentType, $type) === 0) {
                return $type;
            }
        }

        return 'image/x-icon';
    }

    /**
     * Get data URI (http://en.wikipedia.org/wiki/Data_URI_scheme).
     *
     * @return string
     */
    public function getDataUri(){
        if (empty($this->content)) {
            return '';
        }

        return sprintf(
            'data:%s;base64,%s',
            $this->getType(),
            base64_encode($this->content)
        );
    }

    /**
     * Download and check if a resource exists.
     *
     * @param string $url URL
     *
     * @return \PicoFeed\Client Client instance
     */
    public function download($url){
        $client = Client::getInstance();
        $client->setConfig($this->config);

        try {
            $client->execute($url);
        } catch (ClientException $e) {
            trigger_error(get_called_class().' Download Failed => '.$e->getMessage());
        }

        return $client;
    }

    /**
     * Check if a remote file exists.
     *
     * @param string $url URL
     *
     * @return bool
     */
    public function exists($url){
        return $this->download($url)->getContent() !== '';
    }

    /**
     * Get the icon link for a website.
     *
     * @param string $website_link URL
     * @param string $favicon_link optional URL
     *
     * @return string
     */
    public function find($website_link, $favicon_link = ''){
        $website = new Url($website_link);

        if ($favicon_link !== '') {
            $icons = array($favicon_link);
        } else {
            $icons = $this->extract($this->download($website->getBaseUrl('/'))->getContent());
            $icons[] = $website->getBaseUrl('/favicon.ico');
        }

        foreach ($icons as $icon_link) {
            $icon_link = Url::resolve($icon_link, $website);
            $resource = $this->download($icon_link);
            $this->content = $resource->getContent();
            $this->contentType = $resource->getContentType();

            if ($this->content !== '') {
                return $icon_link;
            } elseif ($favicon_link !== '') {
                return $this->find($website_link);
            }
        }

        return '';
    }

    /**
     * Extract the icon links from the HTML.
     *
     * @param string $html HTML
     *
     * @return array
     */
    public function extract($html){
        $icons = array();

        if (empty($html)) {
            return $icons;
        }

        $dom = XmlParser::getHtmlDocument($html);

        $xpath = new DOMXpath($dom);
        $elements = $xpath->query("//link[contains(@rel, 'icon') and not(contains(@rel, 'apple'))]");

        for ($i = 0; $i < $elements->length; ++$i) {
            $icons[] = $elements->item($i)->getAttribute('href');
        }

        return $icons;
    }
}