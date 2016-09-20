<?php

namespace Commonhelp\Rss\Parser;

/**
 * Feed.
 *
 */
class Feed{
    /**
     * Feed items.
     *
     * @var array
     */
    public $items = array();

    /**
     * Feed id.
     *
     * @var string
     */
    public $id = '';

    /**
     * Feed title.
     *
     * @var string
     */
    public $title = '';

    /**
     * Feed description.
     *
     * @var string
     */
    public $description = '';

    /**
     * Feed url.
     *
     * @var string
     */
    public $feedUrl = '';

    /**
     * Site url.
     *
     * @var string
     */
    public $siteUrl = '';

    /**
     * Feed date.
     *
     * @var \DateTime
     */
    public $date = null;

    /**
     * Feed language.
     *
     * @var string
     */
    public $language = '';

    /**
     * Feed logo URL.
     *
     * @var string
     */
    public $logo = '';

    /**
     * Feed icon URL.
     *
     * @var string
     */
    public $icon = '';

    /**
     * Return feed information.
     */
    public function __toString(){
        $output = '';

        foreach (array('id', 'title', 'feedUrl', 'siteUrl', 'language', 'description', 'logo') as $property) {
            $output .= 'Feed::'.$property.' = '.$this->$property.PHP_EOL;
        }

        $output .= 'Feed::date = '.$this->date->format(DATE_RFC822).PHP_EOL;
        $output .= 'Feed::isRTL() = '.($this->isRTL() ? 'true' : 'false').PHP_EOL;
        $output .= 'Feed::items = '.count($this->items).' items'.PHP_EOL;

        foreach ($this->items as $item) {
            $output .= '----'.PHP_EOL;
            $output .= $item;
        }

        return $output;
    }

    /**
     * Get title.
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * Get description.
     */
    public function getDescription(){
        return $this->description;
    }

    /**
     * Get the logo url.
     */
    public function getLogo(){
        return $this->logo;
    }

    /**
     * Get the icon url.
     */
    public function getIcon(){
        return $this->icon;
    }

    /**
     * Get feed url.
     */
    public function getFeedUrl(){
        return $this->feedUrl;
    }

    /**
     * Get site url.
     */
    public function getSiteUrl(){
        return $this->siteUrl;
    }

    /**
     * Get date.
     */
    public function getDate(){
        return $this->date;
    }

    /**
     * Get language.
     */
    public function getLanguage(){
        return $this->language;
    }

    /**
     * Get id.
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Get feed items.
     */
    public function getItems(){
        return $this->items;
    }

    /**
     * Return true if the feed is "Right to Left".
     *
     * @return bool
     */
    public function isRTL(){
        return Parser::isLanguageRTL($this->language);
    }
}