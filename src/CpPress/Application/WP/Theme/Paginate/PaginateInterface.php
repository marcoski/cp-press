<?php
namespace CpPress\Application\WP\Theme\Paginate;

use CpPress\Application\WP\Theme\Paginate\Element\PaginatorElementInterface;

interface PaginateInterface extends \ArrayAccess, \IteratorAggregate  {

	/**
	 * @param $index
	 *
	 * @return PaginatorElementInterface
	 */
	public function get($index);

	/**
	 * @param $index
	 *
	 * @return bool
	 */
	public function has($index);

	/**
	 * @param $index
	 * @param PaginatorElementInterface $element
	 *
	 * @return mixed
	 */
	public function set($index, PaginatorElementInterface $element);

	/**
	 * @return PaginatorElementInterface
	 */
	public function all();

	public function render();

	public function renderAllElements();
	public function renderElement($element);

	public function getPaginatorElement();
	public function getDotElement();
	public function getLabeledElement();

	public function createElementInstance($element, array $args = array());

	public function getQueryVar();

}