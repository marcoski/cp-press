<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 11/10/16
 * Time: 10:22
 */

namespace CpPress\Application\WP\Theme\Paginate\Element;


interface PaginatorElementInterface {

	public function render();

	public function getPage();
	public function setPage($page);

	public function get($attr);
	public function has($attr);
	public function set($attr, $value);
	public function all();

}