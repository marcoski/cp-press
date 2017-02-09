<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 29/09/16
 * Time: 21:59
 */

namespace CpPress\Application\WP\Theme\Feature;


use Commonhelp\WP\WPContainer;
use CpPress\Application\BackEndApplication;
use CpPress\Application\WP\Asset\Scripts;
use CpPress\Application\WP\Asset\Styles;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Hook\Hook;

class PageWidgetsFeature extends BaseFeature {

	public function __construct( Hook $hook, Filter $filter, Scripts $scripts, Styles $styles, WPContainer $container ) {
		parent::__construct( $hook, $filter, $scripts, $styles, [], $container );
		$this->options = array(
			'id' => 'cp-press-page-widgets',
			'label' => __('Widgets', 'cppress'),
			'post_type' => $this->container->query('PagePostType'),
			'priority' => 'low',
			'context' => 'normal'
		);

		$this->hooks();
	}

	public function getMetaKey() {
		return;
	}

	public function render(){
		BackEndApplication::main('PageController', 'widgets', $this->container);
	}
}