<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 11/10/16
 * Time: 10:17
 */

namespace CpPress\Application\WP\Theme\Paginate;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Query\Query;
use CpPress\Application\WP\Theme\Paginate\Element\DotPaginatorElement;
use CpPress\Application\WP\Theme\Paginate\Element\LabeledPaginatorElement;
use CpPress\Application\WP\Theme\Paginate\Element\NormalPaginatorElement;
use CpPress\Application\WP\Theme\Paginate\Element\PaginatorElementInterface;


/**
 * Class Paginator
 * @package CpPress\Application\WP\Theme\Paginate
 */
class Paginator implements PaginateInterface {

	/**
	 * @var Query
	 */
	protected $query;

	/**
	 * @var Filter
	 */
	private $filter;

	private $paged;
	private $max;

	private $elements;

	private $pagination;

	private $links;

	public function __construct(Query $query, Filter $filter){
		$this->query = $query;
		$this->filter = $filter;
		$this->pagination = '<ul class="%s" data-pagination-query="%s">%s</ul>';
		$this->initialize();
		$this->createElements();

	}

	public function get($index){
		if($this->has($index)){
			return $this->elements[$index];
		}

		return null;
	}

	public function offsetGet($index){
		return $this->get($index);
	}

	public function has($index){
		return isset($this->elements[$index]);
	}

	public function offsetExists($index){
		return $this->has($index);
	}

	public function set($index, PaginatorElementInterface $element){
		$this->elements[$index] = $element;
		return $this;
	}

	public function offsetSet($index, $element){
		$this->set($index, $element);
	}

	/**
	 * @return mixed
	 */
	public function all(){
		return $this->elements;
	}

	/**
	 * @param mixed $index
	 */
	public function offsetUnset($index){
		if($this->has($index)){
			unset($this->elements[$index]);
		}
	}

	/**
	 * @return \ArrayIterator
	 */
	public function getIterator(){
		return new \ArrayIterator($this->elements);
	}

	/**
	 * @return string
	 */
	public function render(){
		$paginationClass = $this->filter->apply('cppress-pagination-ulclass', array('pagination'));
		$beforePagination = $this->filter->apply('cppress-pagination-ulbefore', '');
		$afterPagination = $this->filter->apply('cppress-pagination-ulafter', '');
		$pagination = $beforePagination . $this->pagination . $afterPagination;

		return sprintf($pagination,
			implode(' ', $paginationClass),
			htmlspecialchars(json_encode($this->getQueryVar(), JSON_HEX_TAG)),
			$this->renderAllElements()
		);
	}

	public function renderAllElements(){
		return implode("\n", array_map(function(PaginatorElementInterface $element){
			return $element->render();
		}, $this->elements));
	}

	public function renderElement($element) {
		if($this->has($element)){
			return $this->get($element)->render();
		}

		return null;
	}

	public function getDotElement(){
		return DotPaginatorElement::class;
	}

	public function getLabeledElement(){
		return LabeledPaginatorElement::class;
	}

	public function getPaginatorElement(){
		return NormalPaginatorElement::class;
	}

	public function createElementInstance($element, array $args = array()){
		if(class_exists($element)){
			return call_user_func_array(array(
				new \ReflectionClass($element), 'newInstance'), $args);
		}

		return null;
	}

	public function getQueryVar(){
		if($this->query->has('paged')){
			$this->query->remove('paged');
		}
		return $this->query->all();
	}

	protected function initialize(){
		$this->links = array();
		$this->paged = $this->getPagedVar();
		if(0 === $this->paged){
			$this->paged = 1;
		}

		$this->max = intval($this->query->max_num_pages);

		if($this->paged >= 1){
			$this->links[] = $this->paged;
		}

		if($this->paged >= 3){
			$this->links[] = $this->paged - 1;
			$this->links[] = $this->paged - 2;
		}

		if(($this->paged+2) <= $this->max){
			$this->links[] = $this->paged + 2;
			$this->links[] = $this->paged + 1;
		}
	}

	protected function createElements(){
		if($this->filter->apply('cppress-pagination-show-prevlink', true) && $this->paged > 1){
			$prevLinkText = $this->filter->apply('cppress-pagination-prevlink-text', '&laquo; ' . __('Prev', 'cppress'));
			$this->elements[] = $this->createElementInstance($this->getLabeledElement(), array($prevLinkText, $this->paged-1));
		}

		if(!in_array(1, $this->links)){
			$element = $this->createElementInstance($this->getPaginatorElement(), array(1));
			if($this->paged == 1){
				$element->set('class', 'active');
			}
			$this->elements[] = $element;
		}

		sort($this->links);
		foreach((array) $this->links as $link){
			$element = $this->createElementInstance($this->getPaginatorElement(), array($link));
			if($this->paged == $link){
				$element->set('class', 'active');
			}
			$this->elements[] = $element;

		}

		if(!in_array($this->max, $this->links)){
			if(!in_array($this->max-1, $this->links)){
				$this->elements[] = $this->createElementInstance($this->getDotElement());
			}
			$element = $this->createElementInstance($this->getPaginatorElement(), array($this->max));
			if($this->paged == $this->max){
				$element->set('class', 'active');
			}
			$this->elements[] = $element;
		}

		if($this->filter->apply('cppress-pagination-show-nextlink', true) && $this->paged < $this->max){
			$nextLinkText = $this->filter->apply('cppress-pagination-naexlink-text', __('Next', 'cppress') . ' &raquo;');
			$this->elements[] = $this->createElementInstance($this->getLabeledElement(), array($nextLinkText, $this->paged+1));
		}
	}

	protected function getPagedVar(){
		return get_query_var('paged', 1);
	}
}