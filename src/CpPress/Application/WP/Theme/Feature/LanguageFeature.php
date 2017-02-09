<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 29/09/16
 * Time: 21:49
 */

namespace CpPress\Application\WP\Theme\Feature;


use Commonhelp\App\Http\Request;
use Commonhelp\WP\WPContainer;
use CpPress\Application\BackEndApplication;
use CpPress\Application\WP\Asset\Scripts;
use CpPress\Application\WP\Asset\Styles;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Hook\Hook;

class LanguageFeature extends BaseFeature {

	public function __construct( Hook $hook, Filter $filter, Scripts $scripts, Styles $styles, WPContainer $container ) {
		parent::__construct( $hook, $filter, $scripts, $styles, [], $container );
		$this->options = array(
			'id' => 'cp-press-language',
			'label' => __('Language', 'cppress'),
			'post_type' => null,
			'priority' => 'low',
			'context' => 'side'
		);
	}

	public function getMetaKey() {
		return 'cp-press-country';
	}

	public function hooks() {
		$this->hook->register('save_post', array($this, 'save'));
		parent::hooks();
	}

	public function render(){
		global $post;
		$country = PostMeta::find($post->ID, $this->getMetaKey());
		BackEndApplication::main('MultiLanguageController', 'language', $this->container, array($country));
	}

	public function save($postId){
		/** @var Request $request */
		$request = $this->container->getRequest();
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
			return;
		}

		if(is_null($request->getParam('_cppress_multilanguage_nonce')) || !wp_verify_nonce($request->getParam('_cppress_multilanguage_nonce'), 'save')){
			return;
		}

		if($request->getParam($this->getMetaKey(), null) !== null){
			$langs = $request->getParam($this->getMetaKey());
			update_post_meta($postId, $this->getMetaKey(), $langs);
		}
	}
}