<?php
namespace CpPress\Application;

use Closure;
use CpPress\Application\WP\Hook\BackEndHook;
use CpPress\Application\WP\Hook\Filter;
use CpPress\CpPress;
use CpPress\Application\WP\Admin\Menu\OptionsMenu;
use CpPress\Application\BackEnd\SettingsController;
use CpPress\Application\WP\Admin\Settings;
use CpPress\Application\BackEnd\PageController;
use CpPress\Application\BackEnd\LinkController;
use CpPress\Application\BackEnd\PostController;
use CpPress\Application\BackEnd\EventController;
use CpPress\Application\BackEnd\GalleryController;
use CpPress\Application\BackEnd\PortfolioController;
use CpPress\Application\BackEnd\SliderController;
use CpPress\Application\BackEnd\DialogController;
use CpPress\Application\BackEnd\NewsController;
use CpPress\Application\BackEnd\SocialmediaController;
use CpPress\Application\WP\Theme\Editor;
use CpPress\Application\WP\Query\Query;
use CpPress\Application\WP\MetaType\PostType;
use CpPress\Application\BackEnd\FieldsController;
use CpPress\Application\BackEnd\AttachmentController;
use CpPress\Application\BackEnd\ContactFormController;
use CpPress\Application\BackEnd\MultiLanguageController;
use Commonhelp\Util\Inflector;
use CpPress\Application\WP\Admin\SettingsInterface;

class BackEndApplication extends CpPressApplication{

	private $logo;
	
	public static $deniedPages = array('toplevel_page_wysija_campaigns', 'media-upload-popup');

	public function __construct($urlParams=array()){
		parent::__construct(
			'BackEndApp',
			$urlParams,
			array(
				'main' => array(
						'root' 	=> dirname(dirname(dirname(CpPress::$FILE))),
						'uri'	=> plugins_url('', dirname(dirname(CpPress::$FILE)))
				),
			)
		);
		$this->logo = $this->themeUri.'/assets/img/chpress.png';
		if(file_exists(get_stylesheet_directory().'/favicon.png')){
			$this->logo = get_stylesheet_directory_uri().'/favicon.png';
		}
		$container = $this->getContainer();
		$container->registerService('BackEndHook', function($c){
			return new BackEndHook($this);
		});
		$container->registerService('BackEndFilter', function($c){
			return new Filter($this);
		});
		$container->registerService('WpOptionMenu', function($c) use ($container){
			$menu = new OptionsMenu(
					array(__('CommonHelp Press', 'cppress'), __('CommonHelp Press', 'cppress')),
					'manage_options',
					'cppress-settings'
			);
			return $menu;
		});
		$container->registerService('CpPressSettings', function($c) use($container){
			$menu = $container->query('WpOptionMenu');
			return new Settings('cppress_settings', $menu);
		});
		$container->registerService('WpEditor', function($c){
			return new Editor();
		});
		$container->registerService('Query', function($c){
			return new Query();
		});
		$this->registerControllers();
	}

	public function setup(){
		parent::setup();
		$hookObj = $this->getContainer()->query('BackEndHook');
		$this->registerHooks();
		$this->execHooks();
		$this->registerFilters();
		$this->execFilters();
		$hookObj->create('cppress_backend_setup');
	}

	public function settings(){
		$menu = $this->getContainer()->query('WpOptionMenu');
		$menu->add(function(){
			self::main('SettingsController', 'main', $this->getContainer());
		});
		$this->getSettings()->createSettingsSection();
	}

	public function registerBackEndAjax(){
		$hookObj = $this->getContainer()->query('AjaxHook');
		$hookObj->register('icon_family', function(){
			self::main('FieldsController', 'xhr_icon_family', $this->getContainer());
		});
		$hookObj->register('widget_search_post', function(){
			self::main('PostController', 'xhr_widget_search_post', $this->getContainer());
		});
		$hookObj->register('widget_search_taxonomy', function(){
			self::main('PostController', 'xhr_widget_search_taxonomy', $this->getContainer());
		});
		$hookObj->register('contact_form_tag', function(){
			self::main('ContactFormController', 'xhr_taggenerator', $this->getContainer());
		});
		$hookObj->register('widget_social_add', function(){
			self::main('SocialmediaController', 'xhr_add', $this->getContainer());
		});
		$hookObj->register('widget_social_get_network', function(){
			self::main('SocialmediaController', 'xhr_get_network', $this->getContainer());
		});
		$hookObj->register('widget_gallery_add', function(){
			$container = $this->getContainer();
			$request = $container->query('Request');
			$val = $request->getParam('values', array());
			if(!empty($val)){
				$img = $val['img']; $imgExt = $val['img_ext']; $video = $val['video']; $videoExt = $val['video_ext'];
			}else{
				$img = ''; $imgExt = ''; $video = ''; $videoExt = '';
			}
			$image = BackEndApplication::part(
				'FieldsController', 'media_button', $this->container,
				array(
					array(
						'media' => $request->getParam('id').'_img',
						'external' => $request->getParam('id').'_img_ext',
					),
					array(
						'media' => $request->getParam('name').'[img][]',
						'external' => $request->getParam('name').'[img_ext][]',
					),
					$img,
					$imgExt
				)
			);
			$video = BackEndApplication::part(
				'FieldsController', 'media_button', $this->container,
				array(
					array(
						'media' => $request->getParam('id').'_video',
						'external' => $request->getParam('id').'_video_ext',
					),
					array(
						'media' => $request->getParam('name').'[video][]',
						'external' => $request->getParam('name').'[video_ext][]',
					),
					$video,
					$videoExt,
					true
				)
			);
			self::main('GalleryController', 'xhr_add', $this->getContainer(), array($image, $video));
		});
		$hookObj->register('widget_maps_add_marker', function(){
			$container = $this->getContainer();
			$request = $container->query('Request');
			$val = $request->getParam('values', array());
			if(!empty($val)){
				$content = $val['content'];
			}else{
				$content = '';
			}
			$editor = self::part(
					'FieldsController', 'editor', $container,
					array(
							$request->getParam('id').'_editor_'.$request->getParam('count'),
							$content,
							array(
									'textarea_name' => $request->getParam('name').'[content][]',
									'teeny' => false,
									'media_buttons' => false,
									'editor_height' => 230
							),
							$request->getParam('widget', null)
					)
			);
			self::main('FieldsController', 'xhr_marker', $container, array($editor));
		});
		$hookObj->register('widget_maps_add_customstyles', function(){
			$container = $this->getContainer();
			$request = $container->query('Request');
			
			self::main('FieldsController', 'xhr_gmap_customstyles', $container, array());
		});
		$hookObj->register('post_advanced_add_taxonomy', function(){
			self::main('PostController', 'xhr_taxonomies', $this->getContainer(), array());
		});
		$hookObj->register('change_taxonomy', function(){
			$container = $this->getContainer();
			$request = $container->query('Request');
			$widget = 'CpPress\\Application\\Widgets\\' . Inflector::camelize($request->getParam('widget'));
			self::main('PostController', 'xhr_change_taxonomy', $container, array($container->query($widget)));
		});
		$hookObj->register('widget_portfolio_add', function(){
			$container = $this->getContainer();
			$request = $container->query('Request');
			$validPostTypes = array_diff(
					PostType::getPostTypes(),
					PostType::getPostTypes(array('_builtin' => true))
			);
			$id = $request->getParam('values', array());
			if(!empty($id)){
				$id = $id['id']; $item = self::part('PortfolioController', 'show', $container, array($id));
			}else{
				$id = ''; $item = '';
			}
			$link = self::part(
					'FieldsController', 'link_button', $container,
					array(
							$request->getParam('id').'_id',
							$request->getParam('name').'[id][]',
							$id,
							$validPostTypes
					)
			);
			self::main('PortfolioController', 'xhr_add', $container, array($link, $item));
		});
		$hookObj->register('widget_portfolio_show', function(){
			self::main('PortfolioController', 'xhr_show', $this->getContainer());
		});
		$hookObj->register('widget_slider_add_images', function(){
			$container = $this->getContainer();
			$request = $container->query('Request');
			$val = $request->getParam('values', array());
			if(!empty($val)){
				$link = $val['link']; $img = $val['img']; $imgExt = $val['img_ext']; $content = $val['content']; $taxonomy = $val['taxonomy'];
			}else{
				$link = ''; $img = ''; $imgExt = ''; $content = ''; $taxonomy = '';
			}
			$media = BackEndApplication::part(
				'FieldsController', 'media_button', $this->container,
				array(
					array(
						'media' => $request->getParam('id').'_img',
						'external' => $request->getParam('id').'_img_ext',
					),
					array(
						'media' => $request->getParam('name').'[img][]',
						'external' => $request->getParam('name').'[img_ext][]',
					),
					$img,
					$imgExt
				)
			);
			$linker = self::part(
				'FieldsController', 'link_button', $container,
				array(
					$request->getParam('id').'_link',
					$request->getParam('name').'[link][]',
					$link,
				)
			);
			$taxonomier = self::part(
				'FieldsController', 'taxonomy_button', $container,
				array(
					$request->getParam('id').'_taxonomy',
					$request->getParam('name').'[taxonomy][]',
					$taxonomy,
				)
			);
			$editor = self::part(
				'FieldsController', 'editor', $container,
				array(
					$request->getParam('id').'_editor_'.$request->getParam('count'),
					$content,
					array(
						'textarea_name' => $request->getParam('name').'[content][]',
						'teeny' => false,
						'media_buttons' => false,
						'editor_height' => 230
					),
					$request->getParam('widget', null)
				)	
			);
			self::main('SliderController', 'xhr_add', $this->getContainer(), array($media, $linker, $taxonomier, $editor));
		});
		$hookObj->register('widget_slider_add_singlepost', function(){
			$container = $this->getContainer();
			$request = $container->query('Request');
			$val = $request->getParam('values', array());
			if(!empty($val)){
				$link = $val['post'];
			}else{
				$link = '';
			}
			$linker = self::part(
					'FieldsController', 'link_button', $container,
					array(
							$request->getParam('id').'_post',
							$request->getParam('name').'[post][]',
							$link,
					)
			);
			self::main('SliderController', 'xhr_add_singlepost', $this->getContainer(), array($linker));
		});
		$hookObj->execAll();
	}

	public function favicon(){
		echo '<link rel="shortcut icon" href="' . $this->logo . '" />';
	}

	public function registerHooks(){
		$hook = $this->getContainer()->query('BackEndHook');
		$hook->massRegister();
	}

	public function registerHook($hook, Closure $closure, $priority=10, $acceptedArgs=1){
		$hookObj = $this->getContainer()->query('BackEndHook');
		$hookObj->register($hook, $closure, $priority, $acceptedArgs);
	}

	public function execHooks(){
		$hook = $this->getContainer()->query('BackEndHook');
		$hook->execAll();
	}

	public function registerFilters(){
		$filter = $this->getContainer()->query('BackEndFilter');
		$filter->massRegister();
	}

	public function registerFilter($filter, Closure $closure, $priority=10, $acceptedArgs=1){
		$filterObj = $this->getContainer()->query('BackEndFilter');
		$filterObj->register($filter, $closure, $priority, $acceptedArgs);
	}

	public function execFilters(){
		$filter = $this->getContainer()->query('BackEndFilter');
		$filter->execAll();
	}

	/**
	 * @param SettingsInterface $settings
	 */
	public function getSettings($settings=''){
		return $this->getContainer()->query('CpPress'.$settings.'Settings');
	}

	public function save($id){
		$container = $this->getContainer();
		$event = $container->query('EventController');
		$event->save($id);
	}

	private function registerControllers(){
		$container = $this->getContainer();
		$container->registerService('FieldsController', function($c){
			return new FieldsController('FieldApp', $c->query('Request'), array($this->themeRoot), $c->query('BackEndFilter'));
		});
		$container->registerService('SocialmediaController', function($c){
			return new SocialmediaController('SocialmediaApp', $c->query('Request'), array($this->themeRoot), $this->themeUri);
		});
			$container->registerService('NewsController', function($c){
				return new NewsController('NewsApp', $c->query('Request'), array($this->themeRoot), $this->themeUri);
			});
		$container->registerService('SettingsController', function($c){
			$settings = $c->query('CpPressSettings');
			return new SettingsController('SettingsApp', $c->query('Request'), array($this->themeRoot), $this->themeUri, $settings);
		});
		$container->registerService('PageController', function($c){
            $hook = $c->query('BackEndHook');
			$widgets = array();
			foreach($hook->getWidgets() as $widget){
				$wObj = $c->query($widget);
				$widgets[$widget] = $wObj;
			}
			$editor = $c->query('WpEditor');
			return new PageController('PageApp', $c->query('Request'), array($this->themeRoot), $widgets, $editor);
		});
		$container->registerService('LinkController', function($c){
			return new LinkController('LinkApp', $c->query('Request'), array($this->themeRoot));
		});
		$container->registerService('AttachmentController', function($c){
			return new AttachmentController('AttachmentApp', $c->query('Request'), array($this->themeRoot));
		});
		$container->registerService('PostController', function($c){
			return new PostController('PostApp', $c->query('Request'), array($this->themeRoot), $c->query('Query'));
		});
		$container->registerService('EventController', function($c){
			return new EventController('EventApp', $c->query('Request'), array($this->themeRoot));
		});
		$container->registerService('GalleryController', function($c){
			return new GalleryController('GalleryApp', $c->query('Request'), array($this->themeRoot), $c->query('BackEndFilter'));
		});
		$container->registerService('PortfolioController', function($c){
			return new PortfolioController(
				'PortfolioApp',
				$c->query('Request'),
				array($this->themeRoot),
				$c->query('Query')
			);
		});
		$container->registerService('SliderController', function($c){
			return new SliderController('SliderApp', $c->query('Request'), array($this->themeRoot), $c->query('BackEndFilter'));
		});
		$container->registerService('DialogController', function($c){
			return new DialogController('DialogApp', $c->query('Request'), array($this->themeRoot));
		});
		$container->registerService('ContactFormController', function($c){
			return new ContactFormController('ContactFormApp', $c->query('Request'), array($this->themeRoot), $c->query('BackEndFilter'));
		});
		$container->registerService('MultiLanguageController', function($c){
			return new MultiLanguageController('LanguageApp', $c->query('Request'), array($this->themeRoot));
		});
	}

	public function registerFilesystem(){
	    WP_Filesystem();
	    $container = $this->getContainer();
	    $container->registerService('Filesystem', function(){
	        global $wp_filesystem;
	        return $wp_filesystem;
        });
    }
	
	public static function isAlowedPage($page){
		return !in_array($page, self::$deniedPages);
	}

}