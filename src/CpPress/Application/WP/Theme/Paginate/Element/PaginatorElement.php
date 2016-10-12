<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 11/10/16
 * Time: 10:23
 */

namespace CpPress\Application\WP\Theme\Paginate\Element;


use CpPress\Application\WP\Hook\Filter;

/**
 * Class PaginatorElement
 * @package CpPress\Application\WP\Theme\Paginate
 */
abstract class PaginatorElement implements PaginatorElementInterface {

	protected $element;

	private $page;

	private $attrs;

	public function __construct($page = null, array $attrs = array()) {
		$this->page = $page;
		$this->attrs = $attrs;
		$this->element = '<li %s><a href="%s">%s</a></li>';
	}

	public function getPage(){
		return $this->page;
	}

	public function setPage($page){
		$this->page = $page;
		return $this;
	}

	public function get($attr){
		if($this->has($attr)){
			return $this->attrs[$attr];
		}

		return null;
	}

	public function has($attr){
		return isset($this->attrs[$attr]);
	}

	public function set($attr, $value){
		$this->attrs[$attr] = $value;

		return $this;
	}

	public function all(){
		$attrsString = '';
		foreach($this->attrs as $name => $value){
			$attrsString .= $name.'="'.$value.'" ';
		}

		return $attrsString;
	}

	abstract public function render();
}