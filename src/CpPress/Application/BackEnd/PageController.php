<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\Settings;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\Widgets\CpWidgetBase;
use Commonhelp\Util\Inflector;
use CpPress\Application\WP\Theme\Editor;

class PageController extends WPController{

	private $widgets;


	public function __construct($appName, RequestInterface $request, $templateDirs = array(), $widgets){
		parent::__construct($appName, $request, $templateDirs);
		$this->widgets = $widgets;
	}

	public function content($layout, $dialog, $template, $fields, $pdTemplate){

		$fakeEditor = new Editor();
		$fakeEditor->init(
				'cp-widget-fake-editor',
				'',
				array(
						'teeny' => false,
						'media_buttons' => false,
						'editor_height' => 230,
						'editor_class' => 'cp-widget-input'
				)
		);
		$this->assign('fake_editor', $fakeEditor);
		$this->assign('dialog_tmpl', $dialog);
		$this->assign('page_tmpl', $template);
		$this->assign('page_dialog_tmpl', $pdTemplate);
		$this->assign('fields_tmpl', $fields);
		$this->assign('layout', $layout);
		$this->assign('json_layout', htmlspecialchars(json_encode($layout, JSON_HEX_TAG)));
		$this->assign('post_id', $post->ID);
		$this->assign('post_name', $post->post_name);
	}

	public function widgets(){
		$this->assign('widgets', $this->widgets);
	}

	public function page_template(){
	}

	public function dialog_template(){
	}

	/**
	 * @responder wpjson
	 */
	public function xhr_page_sidebar($type){
		$this->assign('args', json_decode(stripslashes($this->getParam('args')), true));
		$this->assign('type', $type);
	}

	/**
	* @responder wpjson
	*/
	public function xhr_page_widget_form(){
		$args = json_decode(stripslashes($this->getParam('args')), true);
		
		$widget = null;
		foreach($this->widgets as $wdg){
			if($wdg->id_base != $args['widget_id']){
				continue;
			}
			$widget = $wdg;
		}

		return $widget->form($args['widget']);
	}

	/**
	 * @TODO field sanitizer
	 */
	public function save($id){
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
			return;
		}
		if($this->getParam('cp-press-page-subtitle', null) !== null){
			update_post_meta($id, 'cp-press-page-subtitle', sanitize_text_field($this->getParam('cp-press-page-subtitle')));
		}
		
		if(is_null($this->getParam('_cppress_nonce')) || !wp_verify_nonce($this->getParam('_cppress_nonce'), 'save')){
			return;
		}
		if(!current_user_can( 'edit_post', $id )){
			return;
		}
		
		if($this->getParam('cp-press-page-layout', null) !== null){
			$layoutData = json_decode(wp_unslash($this->getParam('cp-press-page-layout')), true);
			if(!empty($layoutData)){
				$layoutData['widgets'] = $this->saveWidgets($layoutData['widgets']);
				update_post_meta($id, 'cp-press-page-layout', $layoutData);
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
