<?php
namespace CpPress\Application\Widgets;

use CpPress\Application\BackEndApplication;
use CpPress\Application\BackEnd\FieldsController;
use CpPress\Application\WP\Theme\Embed;

class CpWidgetText extends CpWidgetBase{

	private $linkifunknown = true;

	public function __construct(array $templateDirs=array()){
		parent::__construct(
				__('Text Box Widget', 'cppress'),
				array(
						'description' 	=> __('Free text box', 'cppress'),
						'default_style' => 'simple'
				),
				array(),
				$templateDirs
		);
		$this->icon = 'dashicons-text';
		$this->frontStyles = array(
			array(
				'source' => 'cp-widgettext-responsive-embed'
			)
		);
	}
	
	public function unwpautop($string) {
		$string = str_replace("<p>", "", $string);
		$string = str_replace(array("<br />", "<br>", "<br/>"), "\n", $string);
		$string = str_replace("</p>", "\n\n", $string);
		return $string;
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		if(!filter_var($instance['link'], FILTER_VALIDATE_URL)){
			$instance['link'] = FieldsController::getLinkPermalink($instance['link']);
		}
		if(isset($instance['taxonomy']) && $instance['taxonomy'] !== ''){
			$instance['link'] = FieldsController::getTaxonomyPermalink($instance['taxonomy']);
		}

		$content = $instance['text'];
		if(isset($instance['removep']) && $instance['removep']){
			$content = wpautop($content);
		}else{
			$content = wpautop($content, false);
		}
		$content = $this->autoembed($content);
		if(strpos($content, '[')){
			$content = do_shortcode($content);
		}
		if(strpos($content, 'cppress_addmailpoet_form')){
			$mpShortcode = $this->container->query('MailPoetShortcodeManager');
			if(null !== $mpShortcode){
				$content = $mpShortcode->doShortcode($content);
			}else{
				$content = '';
			}
		}
		$instance['text'] = $content;
		return parent::widget($args, $instance);
	}

	public function form($instance){
		$editor = BackEndApplication::part(
			'FieldsController', 'editor', $this->container,
			array(
				'cp-widget-editor'.$this->get_field_id( 'text' ),
				$instance['text'],
				array(
					'textarea_name' => $this->get_field_name('text'),
				),
				$instance
			)
		);
		$icon = BackEndApplication::part(
			'FieldsController', 'icon_button', $this->container,
			array(
				array(
					'icon' => $this->get_field_id( 'icon' ),
					'color' => $this->get_field_id( 'iconcolor' ),
					'class' => $this->get_field_id( 'iconclass' ),
                    'iconposition' => $this->get_field_id( 'iconposition' )
				),
				array(
					'icon' => $this->get_field_name( 'icon' ),
					'color' => $this->get_field_name( 'iconcolor' ),
					'class' => $this->get_field_name( 'iconclass' ),
                    'iconposition' => $this->get_field_name('iconposition')
				),
				$instance['icon'],
				$instance['iconcolor'],
				$instance['iconclass'],
                $instance['iconposition'],
				true
			)
		);
		$link = BackEndApplication::part(
			'FieldsController', 'link_button', $this->container,
			array(
					$this->get_field_id( 'link' ),
					$this->get_field_name( 'link' ),
					$instance['link'],
			)
		);
		$taxonomy = BackEndApplication::part(
			'FieldsController', 'taxonomy_button', $this->container,
			array(
				$this->get_field_id( 'taxonomy' ),
				$this->get_field_name( 'taxonomy' ),
				$instance['taxonomy'],
			)
		);
		$this->assign('taxonomy', $taxonomy);
		$this->assign('link', $link);
		$this->assign('icon', $icon);
		$this->assign('editor', $editor);
		return parent::form($instance);
	}



	/**
	 * Processing widget options on save
	 *
	 * @param array $new The new options
	 * @param array $old The previous options
	 * @return
	 */
	public function update($new, $old) {
		return parent::update($new, $old);
	}

	/**
	 * Passes any unlinked URLs that are on their own line to WP_Embed::shortcode() for potential embedding.
	 *
	 * @see WP_Embed::autoembed_callback()
	 *
	 * @param string $content The content to be searched.
	 * @return string Potentially modified $content.
	 */
	private function autoembed( $content ) {
		// Replace line breaks from all HTML elements with placeholders.
		$content = wp_replace_in_html_tags( $content, array( "\n" => '<!-- wp-line-break -->' ) );

		if ( preg_match( '#(^|\s|>)https?://#i', $content ) ) {
			// Find URLs on their own line.
			$content = preg_replace_callback( '|^(\s*)(https?://[^\s<>"]+)(\s*)$|im', array( $this, 'autoembed_callback' ), $content );
			// Find URLs in their own paragraph.
			$content = preg_replace_callback( '|(<p(?: [^>]*)?>\s*)(https?://[^\s<>"]+)(\s*<\/p>)|i', array( $this, 'autoembed_callback' ), $content );
		}

		// Put the line breaks back.
		return str_replace( '<!-- wp-line-break -->', "\n", $content );
	}

	/**
	 * Callback function for WP_Embed::autoembed().
	 *
	 * @param array $match A regex match array.
	 * @return string The embed HTML on success, otherwise the original URL.
	 */
	private function autoembed_callback( $match ) {
		/** @var Embed $embed */
		$embed = $this->container->query('Embed');
		$oldval = $this->linkifunknown;
		$this->linkifunknown = false;
		$return = $embed->shortcode( array(), $match[2] );
		$this->linkifunknown = $oldval;

		return $match[1] . $return . $match[3];
	}

}
