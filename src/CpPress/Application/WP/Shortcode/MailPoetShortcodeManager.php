<?php
namespace CpPress\Application\WP\Shortcode;

use Commonhelp\Util\Shortcode;
use Commonhelp\WP\WPContainer;
use CpPress\Application\FrontEndApplication;

class MailPoetShortcodeManager extends Shortcode{

	private $shortcode = 'cppress_addmailpoet_form';
	private $container;
	
	public function __construct(WPContainer $container){
		$this->container = $container;
		parent::__construct();
	}
	
	public function register(){
		$this->addShortcode($this->shortcode, function($atts){
			return FrontEndApplication::part('MailPoet', 'doShortcode', $this->container, array($atts));
		});
	}
	
}