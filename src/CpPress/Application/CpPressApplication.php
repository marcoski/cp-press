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
	
	abstract public function registerHooks();
	abstract public function registerHook($hook, Closure $closure, $priority=10, $acceptedArgs=1);
	abstract public function registerFilters();
	abstract public function registerFilter($filter, Closure $closure, $priority=10, $acceptedArgs=1);
	abstract public function execHooks();
	abstract public function execFilters();
	abstract public function setup();
	
}