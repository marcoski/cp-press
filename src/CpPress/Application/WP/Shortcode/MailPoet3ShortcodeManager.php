<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\WP\Shortcode;


use Commonhelp\Util\Shortcode;
use Commonhelp\WP\WPContainer;
use CpPress\Application\FrontEndApplication;

class MailPoet3ShortcodeManager extends Shortcode
{
    private $shortcode = 'cppress_addmailpoet_form';
    private $container;

    public function __construct(WPContainer $container){
        $this->container = $container;
        parent::__construct();
    }

    public function register(){
        $this->addShortcode($this->shortcode, function($atts){
            return FrontEndApplication::part('MailPoet3', 'doShortcode', $this->container, array($atts));
        });
    }

}