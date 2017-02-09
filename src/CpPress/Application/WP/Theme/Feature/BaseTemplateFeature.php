<?php
/**
 * Created by Marco 'Marcoski' Trognoni.
 */

namespace CpPress\Application\WP\Theme\Feature;


use Commonhelp\WP\WPContainer;
use Commonhelp\WP\WPIController;
use Commonhelp\WP\WPTemplate;
use CpPress\Application\WP\Asset\Scripts;
use CpPress\Application\WP\Asset\Styles;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Hook\Hook;

abstract class BaseTemplateFeature extends BaseFeature implements WPIController {

	/**
	 * @var WPTemplate
	 */
	private $tamplate;

	public function __construct(Hook $hook, Filter $filter, Scripts $scripts, Styles $styles, array $options, WPContainer $container)
	{
		parent::__construct($hook, $filter, $scripts, $styles, $options, $container);
		$this->tamplate = new WPTemplate($this);
	}

	abstract public function getAction();
	abstract public function getAppName();

	public function getTemplateDirs()
	{
		return array( get_template_directory() . '/', get_stylesheet_directory() . '/' );
	}

	public function getTemplateName(){
		return 'template-parts/admin/'.$this->getAction();
	}

}