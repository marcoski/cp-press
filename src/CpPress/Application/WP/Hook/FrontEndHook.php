<?php
namespace CpPress\Application\WP\Hook;

use Closure;
use CpPress\Application\CpPressApplication;
use CpPress\Application\Widgets\CpWidgetBase;

class FrontEndHook extends Hook{
	
	public function __construct(CpPressApplication $app){
		parent::__construct($app);
	}

	public function massRegister(){
		$this->register('wp_enqueue_scripts', function(){
			$scripts = $this->app->getScripts();
			$styles = $this->app->getStyles();
		});
		
		$this->register('init', function(){
			$this->app->setup();
			$this->app->registerFrontEndAjax();
			$container = $this->app->getContainer();
			foreach(CpWidgetBase::getWidgets() as $widget){
				$container->registerService($widget, function($c) use ($widget){
					$w = new $widget();
					$w->setContainer($c);
					$w->setFilter($c->query('FrontEndFilter'));
					$w->setUri($this->app->getThemeUri());
					$w->setScriptsObj($this->app->getScripts());
					$w->setStylesObj($this->app->getStyles());
					return $w;
				});
			}
		});
		
		$this->register('the_post', function($post){
			$this->app->setWPPost($post);
		});
		
		$this->register('wp_enqueue_scripts', function(){
			$scripts = $this->app->getScripts();
			$styles = $this->app->getStyles();
			$styles->enqueue('cp-press-lightbox');
			$scripts->enqueue('cp-press-lightbox', array('jquery', 'bootstrap'), false, true);
			$this->app->loadCpPressFont();
			foreach(CpWidgetBase::getWidgets() as $widget){
				$container = $this->app->getContainer();
				$wObj = $container->query($widget);
				$wObj->enqueueFrontScripts();
				$wObj->localizeFrontScripts();
				$wObj->enqueueFrontStyles();
			}
		});
	}
	
}