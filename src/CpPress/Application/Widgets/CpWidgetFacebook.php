<?php
namespace CpPress\Application\Widgets;

use CpPress\Util\FacebookApi;
class CpWidgetFacebook extends CpWidgetBase{
	
	
	/**
	 * 
	 * @SEE https://github.com/dannyvankooten/wordpress-recent-facebook-posts
	 */
	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Facebook Widget', 'cppress'),
				array(
						'description' 	=> __('Facebook page latset posts', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-facebook';
		$this->frontStyles = array(
				array(
						'source' => 'cp-facebook'
				)
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$this->assign('posts', $this->getPosts($instance));
		return parent::widget($args, $instance);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
		return parent::form($instance);
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update($new_instance, $old_instance) {
		
		return parent::update($new_instance, $old_instance);
	}
	
	private function getPosts($instance){
		/*$posts = json_decode(get_transient('cp_fb_posts'), true);
		if(is_array($posts)){
			return $posts;
		}*/
		$api = new FacebookApi($instance['fbapp'], $instance['fbappsecret'], $instance['fbid']);
		$posts = $api->getPosts();
		/*if(is_array($posts)){
			$encodedPosts = json_encode($posts);
			set_transient('cp_fb_posts', $encodedPosts, 3600);
			set_transient('cp_fb_posts_fallback', $encodedPosts, 2629744);
			
			return $posts;
		}*/
		
		//$posts = json_decode(get_transient('cp_fb_fallback'), true);
		if(is_array($posts)){
			return $posts;
		}
		
		return array();
	}

}
