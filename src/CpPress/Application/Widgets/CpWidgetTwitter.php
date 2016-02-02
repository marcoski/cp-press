<?php
namespace CpPress\Application\Widgets;

class CpWidgetTwitter extends CpWidgetBase{

	private $default;
	
	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Twitter Widget', 'cppress'),
				array(
						'description' 	=> __('Twitter mashup widget', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-twitter';
		$this->default = array(
			'wtitle' => esc_attr__( 'Twitter Widget', 'cppress'),
			'showtitle' => false,
			'twitter_id' => '344713329262084096',
			'twitter_screen_name' => 'designorbital',
			'twitter_tweet_limit' => 0,
			'twitter_show_replies' => 'false',
			'twitter_width' => 300,
			'twitter_height' => 250,
			'twitter_theme' => 'light',
			'twitter_link_color' => '',
			'twitter_border_color' => '',
			'twitter_chrome_header' => 0,
			'twitter_chrome_footer' => 0,
			'twitter_chrome_border' => 0,
			'twitter_chrome_scrollbar' => 0,
			'twitter_chrome_background' => 0
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		$instance = wp_parse_args($instance, $this->default);
		$dataChrome = array(
			$instance['twitter_chrome_header'] == 0  ? 'noheader' : '',
			$instance['twitter_chrome_footer'] == 0  ? 'nofooter' : '',
			$instance['twitter_chrome_border'] == 0  ? 'noborders' : '',
			$instance['twitter_chrome_scrollbar'] == 0 ? 'noscrollbar' : '',
			$instance['twitter_chrome_background'] == 0 ? 'transparent' : ''
		);
		$data = array(
			'data-widget-id' => $instance['twitter_id'],
			'data-screen-name' => $instance['twitter_screen_name'],
			'data-show-replies' => $instance['twitter_show_replies'],
			'data-theme' => $instance['twitter_theme'],
			'data-link-color' => $instance['twitter_link_color'],
			'data-border-color' => $instance['twitter_border_color'],
			'data-chrome' => trim( join( ' ', $dataChrome ) )
		);
		/** Twitter only manages scrollbar / height at default value. So this is for it :) */
		if( $instance['twitter_tweet_limit'] != 0 ) {
			$data['data-tweet-limit'] = $instance['twitter_tweet_limit'];
		}
		/** Data Attributes as name=value */
		$dataTwitter = '';
		foreach ( $data as $key => $val ) {
			$dataTwitter .= $key . '=' . '"' . esc_attr( $val ) . '"' . ' ';
		}
		$this->assign('dataTwitter', $dataTwitter);
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
}
