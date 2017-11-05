<?php
namespace CpPress\Application\FrontEnd;


use Commonhelp\App\Http\RequestInterface;
use Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Query\Query;

class FrontFilterController extends WPController  {

	/**
	 * @var Query
	 */
	private $query;

	/**
	 * @var Filter
	 */
	private $filter;

	public function __construct( $appName, RequestInterface $request, array $templateDirs, Filter $filter, Query $query ) {
		parent::__construct( $appName, $request, $templateDirs );
		$this->filter = $filter;
		$this->query = $query;
	}

	public function form($query, $url, $id){
		$this->assign('query', $query);
		$this->assign('url', $url);
		$this->assign('id', $id);
		$this->assign('filter', $this->filter);
	}

	public function dropdown($options, $label, $query, $type){
		$this->assign('options', $options);
		$this->assign('label', $label);
		$this->assign('query', $query);
		$this->assign('type', $type);
		$this->assign('filter', $this->filter);

	}

	public function infobox(Query $query){
		$this->assign('query', $query);
		$this->assign('filter', $this->filter);
	}

	public function simple($label){
	    $this->assign('label', $label);
	    $this->assign('filter', $this->filter);
    }

	public function getFilter(){
		return $this->filter;
	}

	public function getQuery(){
		return $this->query;
	}
}