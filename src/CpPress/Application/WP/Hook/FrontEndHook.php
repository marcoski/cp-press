<?php
namespace CpPress\Application\WP\Hook;

use Closure;
use CpPress\Application\CpPressApplication;

class FrontEndHook extends Hook{
	
	public function __construct(CpPressApplication $app){
		parent::__construct($app);
	}

	public function massRegister(){
		$this->register('wp_enqueue_scripts', function(){
			$scripts = $this->app->getScripts();
			$styles = $this->app->getStyles();
			if ( is_singular() && get_option( 'thread_comments' ) )
				$scripts->enqueue('comment-reply');
			$styles->enqueue('bootstrap', false, '', 'all');
			$styles->enqueue('animate');
			$styles->enqueue('cp-press-event');
			$styles->enqueue('bootstrap-lightbox');
			$styles->enqueue('cp-press-gallery');
			$styles->enqueue('cp-press-portfolio');
			$styles->enqueue('cp-press-slider');
			$scripts->enqueue('jquery');
			$scripts->enqueue('bootstrap');
			$scripts->enqueue('cp-press-carousel');
			$scripts->localize('cp-press', 'cpPressOptions', get_option('chpress_header_settings'));
			$scripts->enqueue('cp-press-event-carousel');
			$scripts->enqueue('cp-press-event');
			$scripts->localize('cp-press-event', 'cpPressEventOptions', get_option('chpress_event_settings'));
			$scripts->enqueue('cp-press-gallery-carousel');
			$scripts->enqueue('bootstrap-lightbox');
			$scripts->enqueue('cp-press-gallery');
			$scripts->localize('cp-press-gallery', 'cpPressGalleryOptions', get_option('chpress_gallery_settings'));
			$scripts->enqueue('cp-press-portfolio');
			$scripts->localize('cp-press-portfolio', 'cpPressPortfolioOptions', get_option('chpress_portfolio_settings'));
			$scripts->enqueue('cp-press-slider');
			$scripts->localize('cp-press-slider', 'cpPressSliderOptions', get_option('chpress_slider_settings'));
		});
		
		$this->register('init', function(){
			$this->app->setup();
		});
		
		$this->register('after_setup_theme', function(){
			load_theme_textdomain('cppress', $this->app->getThemeRoot().'/languages');
		});
	}
	
}