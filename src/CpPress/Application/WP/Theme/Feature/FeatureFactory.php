<?php

namespace CpPress\Application\WP\Theme\Feature;

use Commonhelp\DI\ContainerInterface;
use Commonhelp\DI\ServiceProviderInterface;
use Commonhelp\WP\WPContainer;

class FeatureFactory implements ServiceProviderInterface {


	public function register( ContainerInterface $container ) {
		$container->registerService('CpAttachment', function(WPContainer $container){
			return new AttachmentFeature($container->query('BackEndHook'), $container->query('BackEndFilter'), $container->query('WPScripts'), $container->query('WPStyles'), $container);
		});
		$container->registerService('CpLanguage', function(WPContainer $container){
			return new LanguageFeature($container->query('BackEndHook'), $container->query('BackEndFilter'), $container->query('WPScripts'), $container->query('WPStyles'), $container);
		});
		$container->registerService('CpPageWidgets', function(WPContainer $container){
			return new PageWidgetsFeature($container->query('BackEndHook'), $container->query('BackEndFilter'), $container->query('WPScripts'), $container->query('WPStyles'), $container);
		});
		$container->registerService('CpPageContent', function(WPContainer $container){
			return new PageContentFeature($container->query('BackEndHook'), $container->query('BackEndFilter'), $container->query('WPScripts'), $container->query('WPStyles'), $container);
		});
		$container->registerService('CpPageSubtitle', function(WPContainer $container){
			return new SubtitleFeature($container->query('BackEndHook'), $container->query('BackEndFilter'), $container->query('WPScripts'), $container->query('WPStyles'), $container);
		});
	}

	public static function attachment(ContainerInterface $container){
		$attachment = new AttachmentFeature($container->query('BackEndHook'), $container->query('BackEndFilter'), $container->query('WPScripts'), $container->query('WPStyles'), $container);
		$attachment->hooks();
		return $attachment;
	}

	public static function language(ContainerInterface $container){
		$language = new LanguageFeature($container->query('BackEndHook'), $container->query('BackEndFilter'), $container->query('WPScripts'), $container->query('WPStyles'), $container);
		$language->hooks();
		return $language;
	}

	public static function widgets(ContainerInterface $container){
		$widgets = new PageWidgetsFeature($container->query('BackEndHook'), $container->query('BackEndFilter'), $container->query('WPScripts'), $container->query('WPStyles'), $container);
		$widgets->hooks();
		return $widgets;
	}

	public static function content(ContainerInterface $container){
		$content = new PageContentFeature($container->query('BackEndHook'), $container->query('BackEndFilter'), $container->query('WPScripts'), $container->query('WPStyles'), $container);
		$content->hooks();
		return $content;
	}

	public static function subtitle(ContainerInterface $container){
		$subtitle = new SubtitleFeature( $container->query('BackEndHook'), $container->query('BackEndFilter'), $container->query('WPScripts'), $container->query('WPStyles'), $container);
		$subtitle->hooks();
		return $subtitle;
	}

	public static function multithumb(ContainerInterface $container, array $options = array()){
		$multithumb = new MultiThumbnailFeature($container->query('BackEndHook'), $container->query('BackEndFilter'), $container->query('WPScripts'), $container->query('WPStyles'), $options);
		$multithumb->hooks();
		return $multithumb;
	}

}