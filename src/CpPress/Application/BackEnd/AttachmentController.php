<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\Settings;
use CpPress\Application\WP\Admin\PostMeta;

class AttachmentController extends WPController{
	
	private $options;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array()){
		parent::__construct($appName, $request, $templateDirs);
		$this->options = get_option('cppress-options-attachment');
	}
	
	public function featured($post){
		$files = PostMeta::find($post->ID, 'cp-press-attachments');
		if($files == ''){
			$files = array();
		}
		$validMime = isset($this->options['validmime']) ? 
			json_encode($this->options['validmime']) : json_encode(array());
		$this->assign('validMime', htmlspecialchars($validMime));
		$this->assign('json_files', htmlspecialchars(json_encode($files, JSON_HEX_TAG)));
	}
	
	public function save($id){
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
			return;
		}
	
		if(is_null($this->getParam('_cppress_attachment_nonce')) || !wp_verify_nonce($this->getParam('_cppress_attachment_nonce'), 'save')){
			return;
		}
		
		if($this->getParam('cp-press-attachments', null) !== null){
			$files = json_decode(wp_unslash($this->getParam('cp-press-attachments')), true);
			update_post_meta($id, 'cp-press-attachments', $files);
		}
		
	}
	
}