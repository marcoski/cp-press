<?php
namespace CpPress\Application;

use Closure;
use \Commonhelp\WP\WPApplication;
use CpPress\Application\FrontEnd\FrontPageController;
use CpPress\Application\FrontEnd\FrontSliderController;
use CpPress\Application\FrontEnd\FrontPostController;
use CpPress\Application\WP\Hook\FrontEndHook;
use CpPress\Application\WP\Hook\FrontEndFilter;
use CpPress\CpPress;
use CpPress\Application\Widgets\CpWidgetBase;
use CpPress\Application\WP\Query\Query;
use CpPress\Application\FrontEnd\FrontBreadcrumbController;
use CpPress\Application\FrontEnd\FrontGalleryController;
use CpPress\Application\WP\Theme\Embed;

class FrontEndApplication extends CpPressApplication{
	
	private $post;
	
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
	}
	
	public function setWPPost($post){
		$this->post = $post;
	}
	
	public function getWPPost(){
		return $this->post;
	}
	
	public function setup(){
		parent::setup();
		$hookObj = $this->getContainer()->query('FrontEndHook');
		$hookObj->create('cppress_frontend_setup');
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
	
}