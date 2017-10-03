<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 13/10/16
 * Time: 11:28
 */

namespace CpPress\Application\WP\Theme\Filter\Field;

use CpPress\Application\FrontEndApplication;
use CpPress\Application\WP\Hook\FrontEndFilter;

abstract class AbstractField implements FieldInterface  {

	/**
	 * @var FrontEndApplication
	 */
	protected $application;

	/**
	 * @var FrontEndFilter
	 */
	protected $filter;

	public function __construct(FrontEndApplication $application) {
		$container = $application->getContainer();
		$this->application = $application;
		$this->filter = $container->query('FrontEndFilter');
	}

	abstract public function render();

}