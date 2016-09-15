<?php
namespace CpPress\Application\WP\Hook;

use Closure;
use CpPress\Application\CpPressApplication;
use CpPress\CpPress;
use CpPress\Application\WP\Admin\MetaBox;
use CpPress\Application\BackEndApplication;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\BackEnd\GalleryController;
use CpPress\Application\WP\Theme\Media\Image;
use CpPress\Application\WP\Theme\Media\Video;
use CpPress\Application\BackEnd\PortfolioController;
use CpPress\Application\Widgets\CpWidgetBase;

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
			$this->app->registerBackEndAjax();
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
			foreach(CpWidgetBase::getWidgets() as $widget){
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
			$this->create('cppress_admin_init');
		});
		$this->register('admin_menu', function(){
			$this->app->settings();
			$this->create('cppress_admin_menu');
		});
		$this->register('add_meta_boxes', function(){
			$container = $this->app->getContainer();
			$container->registerService('CpAttachment', function($c){
				$metaBox = new MetaBox('cp-press-attachment', __('Featured Attachment', 'cppress'));
				$metaBox->setContext('side');
				$metaBox->setCallback(function($post, $box) use($c){
					BackEndApplication::main('AttachmentController', 'featured', $c, array($post));
				});
				
				return $metaBox;
			});
			$container->registerService('CpLanguage', function($c){
				$metaBox = new MetaBox('cp-press-language', __('Language', 'cppress'));
				$metaBox->setContext('side');
				$metaBox->setCallback(function($post, $box) use($c){
					BackEndApplication::main('MultiLanguageController', 'language', $c, array($post));
				});
			
				return $metaBox;
			});
			$container->registerService('CpLink', function($c){
				$metaBox = new MetaBox('cp-press-link', __('Link', 'cppress'));
				$metaBox->setCallback(function($post, $box) use ($c){
					BackEndApplication::main('LinkerController', 'create', $c, array($post, $box));
				});
				return $metaBox;
			});
			$container->registerService('CpPageWidgets', function($c){
				$metaBox = new MetaBox('cp-press-page-widgets', __('Widgets', 'cppress'));
				$metaBox->setCallback(function($post, $box) use ($c){
					BackEndApplication::main('PageController', 'widgets', $c, array($post, $c));
				});
				return $metaBox;
			});
			
			$pageWidgets = $container->query('CpPageWidgets');
			$pageWidgets->setPostType($container->query('PagePostType'));
			$pageWidgets->add();
			
			$container->registerService('CpPageContent', function($c){
				$metaBox = new MetaBox('cp-press-page-content', __('Content', 'cppress'));
				$metaBox->setCallback(function($post, $box) use ($c){
					$dialog = BackEndApplication::part('DialogController', 'content_dialog', $c);
					$template = BackEndApplication::part('PageController', 'page_template', $c);
					$fields = BackEndApplication::part('FieldsController', 'template', $c);
					$pdTemplate = BackEndApplication::part('PageController', 'dialog_template', $c);
					BackEndApplication::main('PageController', 'content', $c, array($post, $dialog, $template, $fields, $pdTemplate));
				});
				return $metaBox;
			});
			$pageContent = $container->query('CpPageContent');
			$pageContent->setPostType($container->query('PagePostType'));
			$pageContent->add();
			
			$container->registerService('CpPageSubtitle', function($c){
				$metaBox = new MetaBox('cp-press-page-subtitle', __('Sub Title', 'cppress'));
				$metaBox->setCallback(function($post, $box) use ($c){
					BackEndApplication::main('PageController', 'subtitle', $c, array($post->ID));
				});
				$metaBox->setPriority('high');
				return $metaBox;
			});
			$subTitle = $container->query('CpPageSubtitle');
			$subTitle->setPostType($container->query('PagePostType'));
			$subTitle->add();
			
			$container->registerService('CpPostSettings', function($c){
				$metaBox = new MetaBox('cp-press-post-settings', __('View options', 'cppress'));
				$metaBox->setContext('side');
				$metaBox->setPostType('post');
				$metaBox->setCallback(function($post, $box) use ($c){
					BackEndApplication::main('PostController', 'singlebox', $c, array($post, $box));
				});
				$metaBox->add();
				return $metaBox;
			});
			$container->query('CpPostSettings');
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
		$this->register('save_post', function($post_id, $post, $update){
			$this->app->save($post_id);
		}, 10, 3);
		
		
		$this->register('admin_enqueue_scripts', function($page){
			if(BackEndApplication::isAlowedPage($page)){
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
				$scripts->enqueue('cp-press-dialog', array('backbone', 'underscore', 'wp-util'));
				$scripts->enqueue('cp-press-dragbg');
				$scripts->enqueue('cp-press-admin');
				$scripts->enqueue('cp-press-page-admin-dialog', array('backbone', 'underscore', 'wp-util'));
				$scripts->enqueue('cp-press-page-admin', array('backbone', 'underscore'));
				$scripts->enqueue('cp-press-field-admin', array('backbone', 'underscore'));
				$scripts->enqueue('cp-press-attachment-admin', array('backbone', 'underscore'));
				$scripts->enqueue('cp-press-field-tinymce');
				$scripts->enqueue('cp-press-event-admin');
				$scripts->enqueue('media-upload');
				$styles->enqueue('cp-press-flag-icon');
				$styles->enqueue('cp-press-dialog');
				$styles->enqueue('cp-press-event-admin');
				$styles->enqueue('cp-press-admin');
				
				foreach(CpWidgetBase::getWidgets() as $widget){
					$container = $this->app->getContainer();
					$wObj = $container->query($widget);
					$wObj->enqueueAdminScripts();
					$wObj->localizeAdminScripts();
					$wObj->enqueueAdminStyles();
				}
				$this->create('cppress_admin_enqueue_scripts');
			}
		});
		$this->register('plugins_loaded', function(){
			
		});
		$this->register('manage_section_posts_custom_column' , function($col, $post){
		}, 10, 2 );
	}
}