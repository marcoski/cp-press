<?php
namespace CpPress\Application\WP\Hook;

use CpPress\Application\FrontEndApplication;
use CpPress\Application\Widgets\CpWidgetBase;
use CpPress\Application\WP\Asset\Scripts;
use CpPress\Application\WP\Asset\Styles;

class FrontEndHook extends Hook{
	
	public function __construct(FrontEndApplication $app){
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
                    /** @var CpWidgetBase $w */
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
		    /** @var Scripts $scripts */
			$scripts = $this->app->getScripts();
            /** @var Styles $styles */
			$styles = $this->app->getStyles();
			$styles->enqueue('cp-press-lightbox');
			$scripts->enqueue('cp-press-jquery', array('jquery'), false, true);
			$scripts->enqueue('cp-press-lightbox', array('jquery', 'bootstrap'), false, true);
			$scripts->enqueue('cp-social-share-kit', array(), false, true);
			$scripts->enqueue('cp-press-search', array('jquery', 'backbone'), false, true);
			$scripts->enqueue('cp-press-paginator', array('jquery', 'backbone'), false, true);
			$this->app->loadCpPressFont();
			foreach(CpWidgetBase::getWidgets() as $widget){
				$container = $this->app->getContainer();
                /** @var CpWidgetBase $wObj */
				$wObj = $container->query($widget);
				$wObj->enqueueFrontScripts();
				$wObj->localizeFrontScripts();
				$wObj->enqueueFrontStyles();
			}
		});

		$this->register('wp_head', function(){
			echo '<script type="text/javascript">var ajaxurl="'.admin_url('admin-ajax.php').'";</script>';
		});
	}
	
}