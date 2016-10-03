<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 29/09/16
 * Time: 22:07
 */

namespace CpPress\Application\WP\Theme\Feature;


use Commonhelp\App\Http\Request;
use Commonhelp\Util\Inflector;
use Commonhelp\WP\WPContainer;
use CpPress\Application\BackEndApplication;
use CpPress\Application\Widgets\CpWidgetBase;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\WP\Asset\Scripts;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Hook\Hook;

class PageContentFeature extends BaseFeature {

	public function __construct(Hook $hook, Filter $filter, Scripts $scripts, WPContainer $container ) {
		parent::__construct( $hook, $filter, $scripts, array(), $container );
		$this->options = array(
			'id' => 'cp-press-page-content',
			'label' => __('Content', 'cppress'),
			'post_type' => $this->container->query('PagePostType'),
			'priority' => 'default',
			'context' => 'advanced'
		);

		$this->hooks();
	}

	public function getMetaKey() {
		return 'cp-press-page-layout';
	}

	public function hooks() {
		$this->hook->register('save_post', array($this, 'save'));
		$this->hook->register('admin_enqueue_scripts', array($this, 'adminEnqueueScripts'));
		$this->hook->register('wp_ajax_page_sidebar_grid', function(){
			BackEndApplication::main('PageController', 'xhr_page_sidebar', $this->container, array('grid'));
		});
		$this->hook->register('wp_ajax_page_widget_form', function(){
			BackEndApplication::main('PageController', 'xhr_page_widget_form', $this->container);
		});
		parent::hooks();
	}

	public function adminEnqueueScripts( $hook ) {
		if(BackEndApplication::isAlowedPage($hook)){
			$this->scripts->enqueue('cp-press-dialog', array('backbone', 'underscore', 'wp-util'));
			$this->scripts->enqueue('cp-press-dragbg');
			$this->scripts->enqueue('cp-press-admin');
			$this->scripts->enqueue('cp-press-page-admin-dialog', array('backbone', 'underscore', 'wp-util'));
			$this->scripts->enqueue('cp-press-page-admin', array('backbone', 'underscore'));
			$this->scripts->enqueue('cp-press-field-admin', array('backbone', 'underscore'));
			$this->scripts->enqueue('cp-press-field-tinymce');
			foreach(CpWidgetBase::getWidgets() as $widget){
				$wObj = $this->container->query($widget);
				$wObj->enqueueAdminScripts();
				$wObj->localizeAdminScripts();
				$wObj->enqueueAdminStyles();
			}
		}
	}

	public function render(){
		global $post;
		$layout = PostMeta::find($post->ID, $this->getMetaKey());
		if($layout == ''){
			$layout = array();
		}

		$dialog = BackEndApplication::part('DialogController', 'content_dialog', $this->container);
		$template = BackEndApplication::part('PageController', 'page_template', $this->container);
		$fields = BackEndApplication::part('FieldsController', 'template', $this->container);
		$pdTemplate = BackEndApplication::part('PageController', 'dialog_template', $this->container);
		BackEndApplication::main('PageController', 'content', $this->container, array($layout, $dialog, $template, $fields, $pdTemplate));
	}

	public function save($postId){
		/** @var Request $request */
		$request = $this->container->getRequest();
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
			return;
		}


		if(is_null($request->getParam('_cppress_nonce')) || !wp_verify_nonce($request->getParam('_cppress_nonce'), 'save')){
			return;
		}
		if(!current_user_can( 'edit_post', $postId )){
			return;
		}

		if($request->getParam($this->getMetaKey(), null) !== null){
			$layoutData = json_decode(wp_unslash($request->getParam($this->getMetaKey())), true);
			if(!empty($layoutData)){
				$layoutData['widgets'] = $this->saveWidgets($layoutData['widgets']);
				update_post_meta($postId, $this->getMetaKey(), $layoutData);
			}
		}
	}

	private function saveWidgets($widgets){
		for($i=0; $i<count($widgets); $i++){
			$info = $widgets[$i]['widget_info'];
			$info['class'] = 'CpPress\\Application\\Widgets\\'.Inflector::camelize($info['id_base']);
			if($info['raw']){
				$theWidget = new $info['class'];
				$widgets[$i] = $theWidget->update($widgets[$i], $widgets[$i]);
				unset($info['raw']);
			}
			$widgets[$i]['widget_info'] = $info;
		}
		return $widgets;
	}



}