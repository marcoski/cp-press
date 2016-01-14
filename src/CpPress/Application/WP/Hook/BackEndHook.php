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
		});
		$this->register('login_head', function(){
			$this->app->favicon();
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
					return $w;
				});
				register_widget($widget);
			}
		});
		$this->register('admin_menu', function(){
			$this->app->settings();
		});
		$this->register('add_meta_boxes', function(){
			$container = $this->app->getContainer();
			$container->registerService('CpPageWidgets', function($c){
				$metaBox = new MetaBox('cp-press-page-widgets', __('Widgets', 'cppress'));
				$metaBox->setPostType($c->query('PagePostType'));
				$metaBox->setCallback(function($post, $box) use ($c){
					BackEndApplication::main('PageController', 'widgets', $c, array($post, $c));
				});
				$metaBox->add();
				return $metaBox;
			});
			$container->query('CpPageWidgets');
			$container->registerService('CpPageContent', function($c){
				$metaBox = new MetaBox('cp-press-page-content', __('Content', 'cppress'));
				$metaBox->setPostType($c->query('PagePostType'));
				$metaBox->setCallback(function($post, $box) use ($c){
					$dialog = BackEndApplication::part('DialogController', 'content_dialog', $c);
					$template = BackEndApplication::part('PageController', 'page_template', $c);
					$fields = BackEndApplication::part('FieldsController', 'template', $c);
					$pdTemplate = BackEndApplication::part('PageController', 'dialog_template', $c);
					BackEndApplication::main('PageController', 'content', $c, array($post, $dialog, $template, $fields, $pdTemplate));
				});
				$metaBox->add();
				return $metaBox;
			});
			$container->query('CpPageContent');
			$container->registerService('CpPageSubtitle', function($c){
				$metaBox = new MetaBox('cp-press-page-subtitle', __('Sub Title', 'cppress'));
				$metaBox->setPostType($c->query('PagePostType'));
				$metaBox->setCallback(function($post, $box) use ($c){
					BackEndApplication::main('PageController', 'subtitle', $c, array($post->ID));
				});
				$metaBox->setPriority('high');
				$metaBox->add();
				return $metaBox;
			});
			$container->query('CpPageSubtitle');
			$container->registerService('CpLink', function($c){
				$metaBox = new MetaBox('cp-press-link', __('Link', 'cppress'));
				$metaBox->setCallback(function($post, $box) use ($c){
					BackEndApplication::main('LinkerController', 'create', $c, array($post, $box));
				});
				return $metaBox;
			});
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
		});
		$this->register('save_post', function($post_id, $post, $update){
			$this->app->save($post_id);
		}, 10, 3);
		$this->register('admin_enqueue_scripts', function(){
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
			$scripts->enqueue('cp-press-field-tinymce', array('editor', 'quicktags'));
			$scripts->enqueue('media-upload');
			$styles->enqueue('jquery-ui');
			$styles->enqueue('cp-press-dialog');
			$styles->enqueue('cp-press-admin');
		});
		$this->register('plugins_loaded', function(){
			//CpOnePage::include_widgets();
		}, 1);
		$this->register('manage_section_posts_custom_column' , function($col, $post){
		}, 10, 2 );
	}
}
