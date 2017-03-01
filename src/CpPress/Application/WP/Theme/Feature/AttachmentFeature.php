<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 29/09/16
 * Time: 21:10
 */

namespace CpPress\Application\WP\Theme\Feature;


use Commonhelp\App\Http\Request;
use Commonhelp\WP\WPContainer;
use CpPress\Application\BackEndApplication;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\WP\Asset\Scripts;
use CpPress\Application\WP\Asset\Styles;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Hook\Hook;

class AttachmentFeature extends BaseFeature {

	private $attachmentOptions;

	public function __construct( Hook $hook, Filter $filter, Scripts $scripts, Styles $styles, WPContainer $container ) {
		parent::__construct( $hook, $filter, $scripts, $styles, [], $container );
		$this->options = array(
			'id' => 'cp-press-attachment',
			'label' => __('Featured Attachment', 'cppress'),
			'post_type' => null,
			'priority' => 'low',
			'context' => 'side'
		);
		$this->attachmentOptions = get_option('cppress-options-attachment');
	}

	public function getMetaKey() {
		return 'cp-press-attachments';
	}

	public function hooks() {
		$this->hook->register('save_post', array($this, 'save'));
		$this->hook->register('admin_enqueue_scripts', array($this, 'adminEnqueueScripts'));
		parent::hooks();
	}

	public function render(){
		global $post;
		$files = PostMeta::find($post->ID, $this->getMetaKey());

		$validMime = isset($this->attachmentOptions['validmime']) ?
			json_encode($this->attachmentOptions['validmime']) : json_encode(array());
		BackEndApplication::main('AttachmentController', 'featured', $this->container, array($files, $validMime));
	}

	public function adminEnqueueScripts( $hook ) {
		if(BackEndApplication::isAlowedPage($hook)){
			$this->scripts->enqueue('cp-press-attachment-admin', array('backbone', 'underscore'));
		}
	}

	/**
	 * @param $postId
	 */
	public function save($postId){
		/** @var  Request $request */
		$request = $this->container->getRequest();
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
			return;
		}

		if(is_null($request->getParam('_cppress_attachment_nonce')) || !wp_verify_nonce($request->getParam('_cppress_attachment_nonce'), 'save')){
			return;
		}

		if($request->getParam($this->getMetaKey(), null) !== null){
			$files = json_decode(wp_unslash($request->getParam($this->getMetaKey())), true);
			update_post_meta($postId, $this->getMetaKey(), $files);
		}
	}


	public static function getAttachment(){
		return PostMeta::find(get_the_ID(), 'cp-press-attachments');
	}

}