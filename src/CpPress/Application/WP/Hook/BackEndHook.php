<?php
namespace CpPress\Application\WP\Hook;

use Commonhelp\WP\WPContainer;
use CpPress\Application\CpPressApplication;
use CpPress\Application\WP\Asset\Scripts;
use CpPress\Application\WP\Theme\Feature\FeatureFactory;
use CpPress\Application\WP\Admin\MetaBox;
use CpPress\Application\BackEndApplication;
use CpPress\Application\Widgets\CpWidgetBase;
use CpPress\Application\WP\Theme\Feature\PageContentFeature;
use CpPress\Application\WP\Theme\Feature\PageWidgetsFeature;
use CpPress\Application\WP\Theme\Feature\SubtitleFeature;

class BackEndHook extends Hook{

	public function __construct(CpPressApplication $app){
		parent::__construct($app);
	}

	public function massRegister(){
		$this->register('admin_head', function(){
			$this->app->favicon();
			$this->create('cppress_admin_head');
		});
		$this->register('login_head', function(){
			$this->app->favicon();
			$this->create('cppress_login_head');
		});
		$this->register('admin_init', function(){
		    $this->app->registerFilesystem();
			$this->app->registerBackEndAjax();
			$this->setCapRoles();
			$container = $this->app->getContainer();
			$pagePostType = $container->query('PagePostType');
			$pagePostType->removeSupport('editor');
			$this->register('calendar_edit_form_fields', function($tags) use ($container){
				BackEndApplication::main('EventController', 'calendar_taxonomy_form', $container, array($tags));

			}, 10, 1);
			$this->register('calendar_add_form_fields', function($tags) use ($container){
				BackEndApplication::main('EventController', 'calendar_taxonomy_form', $container, array($tags));
			}, 10, 1);
			$this->register('edited_calendar', function($term_id, $tt_id) use ($container){
				BackEndApplication::main('EventController', 'calendar_taxonomy_save', $container, array($term_id, $tt_id));
			}, 10, 2);
			$this->register('create_calendar', function($term_id, $tt_id) use ($container){
				BackEndApplication::main('EventController', 'calendar_taxonomy_save', $container, array($term_id, $tt_id));
			}, 10, 2);
			$this->register('delete_calendar', function($term_id) use ($container){
				BackEndApplication::main('EventController', 'calendar_taxonomy_delete', $container, array($term_id));
			}, 10, 2);
			$this->execAll();
			foreach($this->getWidgets() as $widget){
				$container->registerService($widget, function($c) use ($widget){
					$w = new $widget();
					$w->setContainer($c);
					$w->setUri($this->app->getThemeUri());
					$w->setFilter($c->query('BackEndFilter'));
					$w->setScriptsObj($this->app->getScripts());
					$w->setStylesObj($this->app->getStyles());
					return $w;
				});
			}
			$this->featureFactory();
			$this->create('cppress_admin_init');
		});
		$this->register('admin_menu', function(){
			$this->app->settings();
			$this->create('cppress_admin_menu');
		});
		$this->register('add_meta_boxes', function(){
			$container = $this->app->getContainer();
			$container->registerService('CpEventWhere', function($c){
				$metaBox = new MetaBox('cp-press-event-where', __('Where', 'cppress'));
				$metaBox->setContext('normal');
				$metaBox->setPostType($c->query('EventPostType'));
				$metaBox->setPriority('high');
				$metaBox->setCallback(function($post, $box) use ($c){
					BackEndApplication::main('EventController', 'where', $c, array($post, $box));
				});
				$metaBox->add();
				return $metaBox;
			});
			$container->query('CpEventWhere');
			$container->registerService('CpEventWhen', function($c){
				$metaBox = new MetaBox('cp-press-event-when', __('When', 'cppress'));
				$metaBox->setContext('side');
				$metaBox->setPostType($c->query('EventPostType'));
				$metaBox->setPriority('high');
				$metaBox->setCallback(function($post, $box) use ($c){
					BackEndApplication::main('EventController', 'when', $c, array($post, $box));
				});
				$metaBox->add();
				return $metaBox;
			});
			$container->query('CpEventWhen');
			$this->create('cppress_add_meta_boxes');
		});
		$this->register('save_post', function($postId){
			$this->app->save($postId);
		}, 10, 3);
		
		
		$this->register('admin_enqueue_scripts', function($page){
			if(BackEndApplication::isAlowedPage($page)){
                $gApiKey = '';
                if(get_option('cppress-options-apikey')){
                    $apiKeyOptions = get_option('cppress-options-apikey');
                    $gApiKey = $apiKeyOptions['google-api-key'];
                }
			    /** @var Scripts $scripts */
				$scripts = $this->app->getScripts();
				$styles = $this->app->getStyles();
				wp_enqueue_media();
				add_thickbox();
				$scripts->enqueue('jquery');
				$scripts->enqueue('jquery-ui-sortable');
				$scripts->enqueue('jquery-ui-accordion');
				$scripts->enqueue('jquery-ui-draggable');
				$scripts->enqueue('jquery-ui-droppable');
				$scripts->enqueue('jquery-ui-datepicker');
				$scripts->enqueue('jquery-ui-selectable');
				$scripts->enqueue('jquery-ui-dialog');
				$scripts->enqueue('utils');
				$scripts->enqueue('wp-color-picker');
				$styles->enqueue('wp-color-picker');
				$scripts->enqueue('cp-press-event-admin');
				wp_localize_script('cp-press-event-admin', 'settings', ['gApiKey' => $gApiKey]);
				$scripts->enqueue('cp-press-settings-admin');
				$scripts->enqueue('cp-press-select2');
				$scripts->enqueue('ace-editor/ace');
				$scripts->enqueue('media-upload');
				$styles->enqueue('cp-press-select2');
				$styles->enqueue('cp-press-flag-icon');
				$styles->enqueue('cp-press-dialog');
				$styles->enqueue('cp-press-event-admin');
                $styles->enqueue('cp-press-jquery-ui');
				$styles->enqueue('cp-press-admin');

				$this->create('cppress_admin_enqueue_scripts');
			}
		});
	}

	private function setCapRoles()
    {
        $roles = ['event_manager', 'editor', 'administrator'];
        foreach($roles as $roleString){
            /** @var \WP_Role $role */
            $role = get_role($roleString);
            $role->add_cap('read');
            $role->add_cap('read_event');
            $role->add_cap('read_private_events');
            $role->add_cap('edit_event');
            $role->add_cap('edit_events');
            $role->add_cap('edit_published_events');
            $role->add_cap('publish_events');
            $role->add_cap('delete_private_events');
            $role->add_cap('delete_published_events');

            if($roleString !== 'event_manager'){
                $role->add_cap('delete_events');
                $role->add_cap('delete_others_events');
                $role->add_cap('edit_others_events');
            }else{
                $role->remove_cap('delete_others_events');
                $role->remove_cap('edit_others_events');
            }
        }

        //dump($GLOBALS['wp_post_types']['event']);
    }

	private function featureFactory(){
		/** @var WPContainer $container */
		$container = $this->app->getContainer();
		$container->register(new FeatureFactory());

		$container->query('CpPageContent');
		$container->query('CpPageSubtitle');
	}
}