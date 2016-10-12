<?php
namespace CpPress\Application;

use Closure;
use \Commonhelp\WP\WPApplication;
use CpPress\Application\FrontEnd\FrontPageController;
use CpPress\Application\FrontEnd\FrontSliderController;
use CpPress\Application\FrontEnd\FrontPostController;
use CpPress\Application\FrontEnd\FrontEventController;
use CpPress\Application\WP\Hook\FrontEndHook;
use CpPress\Application\WP\Hook\FrontEndFilter;
use CpPress\CpPress;
use CpPress\Application\Widgets\CpWidgetBase;
use CpPress\Application\WP\Query\Query;
use CpPress\Application\FrontEnd\FrontBreadcrumbController;
use CpPress\Application\FrontEnd\FrontGalleryController;
use CpPress\Application\WP\Theme\Embed;
use CpPress\Application\FrontEnd\FrontContactFormController;
use CpPress\Application\WP\Submitter\ContactFormSubmitter;
use CpPress\Application\WP\Admin\PostMeta;
use Commonhelp\Util\Inflector;
use CpPress\Application\BackEnd\FieldsController;
use CpPress\Application\FrontEnd\FrontMailPoetController;
use CpPress\Application\WP\Submitter\MailPoetSubmitter;

class FrontEndApplication extends CpPressApplication{
	
	private $post;
	
	private static $formResult = array();
	
	public function __construct($urlParams=array()){
		parent::__construct(
			'FrontEndApp', 
			$urlParams,
			array(
					'main' => array(
							'root' 	=> dirname(dirname(dirname(CpPress::$FILE))),
							'uri'	=> plugins_url('', dirname(dirname(CpPress::$FILE)))
					),
			)
		);
		$container = $this->getContainer();
		$container->registerService('Page', function($c){
			$filter = $c->query('FrontEndFilter');
			$widgets = array();
			foreach(CpWidgetBase::getWidgets() as $widget){
				$wObj = $c->query($widget);
				$widgets[$widget] = $wObj;
			}
			return new FrontPageController(
				'PageApp', 
				$c->query('Request'),
				array(
					$this->themeRoot	
				),
				$filter,
				$widgets
			);
		});
		$container->registerService('Slider', function($c){
			$filter = $c->query('FrontEndFilter');
			return new FrontSliderController(
					'SliderApp',
					$c->query('Request'),
					array(
							$this->themeRoot
					),
					$filter,
					$c->query('Query')
			);
		});
		$container->registerService('Post', function($c){
			$filter = $c->query('FrontEndFilter');
			return new FrontPostController(
					'PostApp',
					$c->query('Request'),
					array(
							$this->themeRoot
					),
					$filter,
					$c->query('Query')
			);
		});
		$container->registerService('Breadcrumb', function($c){
			$filter = $c->query('FrontEndFilter');
			return new FrontBreadcrumbController(
					'BreadcrumbApp',
					$c->query('Request'),
					array(
						$this->themeRoot
					),
					$filter
			);
		});
		$container->registerService('Gallery', function($c){
			$filter = $c->query('FrontEndFilter');
			return new FrontGalleryController(
					'GalleryApp',
					$c->query('Request'),
					array(
							$this->themeRoot
					),
					$filter
			);
		});
		$container->registerService('ContactForm', function($c){
			$filter = $c->query('FrontEndFilter');
			return new FrontContactFormController(
					'ContactFormApp',
					$c->query('Request'),
					array(
						$this->themeRoot
					),
					$filter,
					$c->query('ContactFormShortcodeManager')
			);
		});
		$container->registerService('Event', function($c){
			$filter = $c->query('FrontEndFilter');
			return new FrontEventController(
					'EventApp',
					$c->query('Request'),
					array(
							$this->themeRoot
					),
					$filter
			);
		});
		$container->registerService('FrontEndHook', function($c){
			return new FrontEndHook($this);
		});
		$container->registerService('FrontEndFilter', function($c){
			return new FrontEndFilter($this);
		});
		$container->registerService('Query', function($c){
			return new Query();
		});
		$container->registerService('Embed', function($c){
			$request = $c->query('Request');
			$filter = $c->query('FrontEndFilter');
			return new Embed($request, $filter);
		});
		$container->registerService('ContactFormSubmitter', function($c){
			$request = $c->query('Request');
			return new ContactFormSubmitter($request, $c->query('FrontEndFilter'), $c->query('FrontEndHook'));
		});
		
		$container->registerService('MailPoet', function($c){
			$filter = $c->query('FrontEndFilter');
			$mailPoetController = new FrontMailPoetController('MailPoetApp', $c->query('Request'), array($this->themeRoot), $filter);
			return $mailPoetController;
		});
		$container->registerService('MailPoetSubmitter', function($c){
			$request = $c->query('Request');
			return new MailPoetSubmitter($request, $c->query('FrontEndFilter'), $c->query('FrontEndHook'));
		});
	}
	
	public function setWPPost($post){
		$this->post = $post;
	}
	
	public function getWPPost(){
		return $this->post;
	}
	
	public function setup(){
		parent::setup();
		$container = $this->getContainer();
		$hookObj = $this->container->query('FrontEndHook');
		$hookObj->create('cppress_frontend_setup');
		$request = $this->container->query('Request');
		if(!is_null($request->getParam('_cppress-mailpoet'))){
			self::$formResult['cppress-mailpoet'] = 
				json_decode(FrontEndApplication::part('MailPoet', 'submit', $container, array($container->query('MailPoetSubmitter'))), true);
		}
	}
	
	public function registerFrontEndAjax(){
		$container = $this->getContainer();
		$hookObj = $this->getContainer()->query('AjaxHook');
		$hookObj->registerFrontEnd('cppress_cf_ajax', function() use($container){
			$request = $container->query('Request');
			$layout = PostMeta::find($request->getParam('_cppress-cf-id'), 'cp-press-page-layout');
			$widget = $this->getWidget($layout, 'cp_widget_contact_form', $container);
			return $widget['widget']->widget(array(), $widget['data']);
		});
		$hookObj->registerFrontEnd('cppress_loop_loadmore', function() use($container){
			self::main('Post', 'loop_loadmore', $container);
		});
		$hookObj->registerFrontEnd('cppress_search', function() use ($container){
			self::main('Post', 'xhr_search', $container);
		});
		$hookObj->registerFrontEnd('cppress_paginate', function() use ($container){
			self::main('Post', 'xhr_paginate', $container);
		});
		$hookObj->execAll();
	}
	
	public static function getFormResult($form){
		if(isset(self::$formResult[$form]) && self::$formResult[$form] !== null){
			return self::$formResult[$form];
		}
		
		return null;
	}
	
	public function registerHooks(){
		$hook = $this->getContainer()->query('FrontEndHook');
		$hook->massRegister();
	}
	
	public function registerHook($hook, Closure $closure, $priority=10, $acceptedArgs=1){
		$hookObj = $this->getContainer()->query('FrontEndHook');
		$hookObj->register($hook, $closure, $priority, $acceptedArgs);
	}
	
	
	public function execHooks(){
		$hook = $this->getContainer()->query('FrontEndHook');
		$hook->execAll();
	}
	
	public function registerFilters(){
		$filter = $this->getContainer()->query('FrontEndFilter');
		$filter->massRegister();
	}
	
	public function registerFilter($filter, Closure $closure, $priority=10, $acceptedArgs=1){
		$filterObj = $this->getContainer()->query('FrontEndFilter');
		$filterkObj->register($filter, $closure, $priority, $acceptedArgs);
	}
	
	public function execFilters(){
		$filter = $this->getContainer()->query('FrontEndFilter');
		$filter->execAll();
	}
	
	public function loadCpPressFont(){
		$container = $this->getContainer();
		$fields = new FieldsController('', $container->query('Request'));
		$style = $this->getStyles();
		$style->enqueueFonts($fields->getFontAssets());
	}
	
	private function getWidget($layout, $idBase, $container){
		foreach($layout['widgets'] as $w){
			if($w['widget_info']['id_base'] == $idBase){
				$pattern = "/cp_press_([a-z0-9]*)_([a-z0-9]*)_([a-z0-9_]*)/";
				preg_match($pattern, Inflector::underscore($w['widget_info']['class']), $classParts);
				$widget = (
						'CpPress' . '\\' .
						Inflector::camelize($classParts[1]) . '\\' .
						Inflector::camelize($classParts[2]) . '\\' .
						Inflector::camelize($classParts[3])
				);
				$widget = $container->query($widget);
				return array('widget' => $widget, 'data' => $w);
			}
		}
	}
	
}