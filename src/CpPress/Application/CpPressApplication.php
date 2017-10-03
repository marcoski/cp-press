<?php
namespace CpPress\Application;

use Closure;
use \Commonhelp\WP\WPApplication;
use CpPress\Application\WP\Theme\Sidebar;
use CpPress\Application\WP\Theme\Menu;
use Commonhelp\Util\Inflector;
use CpPress\Application\WP\Theme\ThemeSupport;
use CpPress\Application\WP\Asset\Scripts;
use CpPress\Application\WP\Asset\Styles;
use CpPress\Application\WP\Hook\AjaxHook;
use CpPress\Application\WP\MetaType\EventPostType;
use CpPress\Application\WP\MetaType\CalendarTaxonomy;
use CpPress\Application\WP\MetaType\NewsTaxonomy;
use CpPress\Application\WP\MetaType\EventtagsTaxonomy;
use CpPress\Application\WP\MetaType\NewstagsTaxonomy;
use CpPress\Application\WP\MetaType\PagePostType;
use CpPress\Application\WP\MetaType\NewsPostType;
use CpPress\Application\WP\Shortcode\ContactFormShortcode;
use CpPress\Application\WP\Shortcode\ContactFormShortcodeManager;
use CpPress\Application\WP\Shortcode\MailPoetShortcodeManager;
use CpPress\Application\WP\MetaType\PostPostType;

abstract class CpPressApplication extends WPApplication{
	
	
	public function __construct($appName, $urlParams=array(), array $themePathUri){
		parent::__construct($appName, $urlParams, $themePathUri);
		$dbDsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=UTF8';
		
		$container = $this->getContainer();
		$container->registerService('ThemeSupport', function($c){
			return new ThemeSupport();
		});
		$container->registerService('WPStyles', function($c){
			return new Styles(
				array($this->themeRoot, $this->themeUri),
				array($this->childRoot, $this->themeRoot)
			);
		});
		$container->registerService('WPScripts', function($c){
			return new Scripts(
				array($this->themeRoot, $this->themeUri),
				array($this->childRoot, $this->themeRoot)
			);
		});
		$container->registerService('AjaxHook', function($c){
			return new AjaxHook($this);
		});
		
		$container->registerService('ContactFormShortcodeManager', function($c){
			ContactFormShortcodeManager::init();
			return new ContactFormShortcodeManager($c);
		});
		$container->registerService('MailPoetShortcodeManager', function($c){
			if(class_exists('WYSIJA')){
				return new MailPoetShortcodeManager($c);
			}
			
			return null;
		});
		$this->registerPostTypes();
	}
	

	
	public function registerSidebar($id, $name, array $args=array()){
		$sidebarName = Inflector::camelize($name, '-'); 
		$container = $this->getContainer();
		$container->registerService($sidebarName, function($c) use($id, $name, $args){
			$sidebar = new Sidebar($id, $name);
			foreach($args as $argsName => $value){
				$setter ='set'.$argsName;
				$sidebar->$setter($value);
			}
			$sidebar->register();
			return $sidebar;
		});
		
		return $container->query($sidebarName);
	}
	
	public function registerNavMenu($id, $slug){
		$menu = Inflector::camelize($id, '-');
		$container = $this->getContainer();
		$container->registerService($menu, function($c) use ($id, $slug){
			$menu = new Menu($id, $slug);
			$menu->register();
			
			return $menu;
		});
		
		return $container->query($menu);
	}
	
	public function getThemeSupport(){
		return $this->getContainer()->query('ThemeSupport');
	}
	
	public function getStyles(){
		return $this->getContainer()->query('WPStyles');
	}
	
	public function getScripts(){
		return $this->getContainer()->query('WPScripts');
	}
	
	public function registerAjax($hook, Closure $closure, $priority=10, $acceptedArgs=1){
		$hookObj = $this->getContainer()->query('AjaxHook');
		$hookObj->register($hook, $closure, $priority, $acceptedArgs);
	}
	
	public function setup(){
	/*	global $wp_scripts, $wp_styles;
		//init styles and scripts global
		$wp_styles = $this->getStyles();
		$wp_scripts = $this->getScripts();*/
		$container = $this->getContainer();
		$container->query('EventPostType')->register();
		$container->query('CalendarTaxonomy')->register();
		$container->query('EventtagsTaxonomy')->register();
		$container->query('NewsPostType')->register();
		$container->query('NewsTaxonomy')->register();
		$container->query('NewstagsTaxonomy')->register();
		$container->query('ContactFormShortcodeManager')->register();
		$mpShortcode = $container->query('MailPoetShortcodeManager');
		if(!is_null($mpShortcode)){
			$mpShortcode->register();
		}
	}
	
	protected function registerPostTypes(){
		$container = $this->getContainer();
		$container->registerService('PagePostType', function($c){
			return new PagePostType();
		});
		$container->registerService('EventPostType', function($c){
			return new EventPostType();
		});
		$container->registerService('NewsPostType', function($c){
			return new NewsPostType();
		});
		$container->registerService('CalendarTaxonomy', function($c){
			return new CalendarTaxonomy();
		});
		$container->registerService('EventtagsTaxonomy', function($c){
			return new EventtagsTaxonomy();
		});
		$container->registerService('NewsTaxonomy', function($c){
			return new NewsTaxonomy();
		});
		$container->registerService('NewstagsTaxonomy', function($c){
			return new NewstagsTaxonomy();
		});
		$container->registerService('PostPostType', function($c){
			return new PostPostType();
		});
	}
	
	abstract public function registerHooks();
	abstract public function registerHook($hook, Closure $closure, $priority=10, $acceptedArgs=1);
	abstract public function registerFilters();
	abstract public function registerFilter($filter, Closure $closure, $priority=10, $acceptedArgs=1);
	abstract public function execHooks();
	abstract public function execFilters();
	
}