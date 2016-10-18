<?php

namespace CpPress\Application\WP\Theme\Filter;


use CpPress\Application\FrontEnd\FrontFilterController;
use CpPress\Application\FrontEndApplication;
use CpPress\Application\WP\Hook\FrontEndFilter;
use CpPress\Application\WP\Query\Query;
use CpPress\Application\WP\Theme\Filter\Field\FieldInterface;

class Filter implements FilterInterface {


	/**
	 * @var Query
	 */
	protected $query;

	/**
	 * @var FrontEndFilter
	 */
	protected $filter;

	/**
	 * @var FrontFilterController
	 */
	protected $controller;

	/**
	 * @var FrontEndApplication
	 */
	protected $application;

	/**
	 * @var FieldInterface[]
	 */
	protected $fields;

	/**
	 * @var FieldInterface[]
	 */
	protected $infoBoxFields;

	protected $container;
	protected $containerClasses;
	protected $fieldsContainer;
	protected $fieldsContainerClasses;

	public function __construct(FrontEndApplication $application, Query $query = null) {
		$container = $application->getContainer();
		$this->application = $application;
		$this->controller = $container->query(FrontFilterController::class);
		$this->query = null !== $query ? $query : $this->controller->getQuery();
		$this->filter = $this->controller->getFilter();

		$this->container = '<div class="%s"><div class="filters hidden-print">%s</div></div>';
		$this->containerClasses = implode(' ', $this->filter->apply(
			'cppress_filter_container_classes',
			array('filters-container'))
		);

		$this->fieldsContainer = '<div class="%s">%s</div>';
		$this->fieldsContainerClasses = implode(' ', $this->filter->apply(
			'cppress_filter_field_container_classes',
			array('filter'))
		);

	}

	public function setQuery(Query $query){
		$this->query = $query;
	}

	public function getContainer(){
		return $this->container;
	}

	public function setContainer($container){
		$this->container = $container;
	}

	public function getContainerClasses(){
		return $this->containerClasses;
	}

	public function setContainerClasses(array $containerClasses){
		$this->containerClasses = implode(' ', $containerClasses);
	}

	public function getFieldsContainer(){
		return $this->fieldsContainer;
	}

	public function setFieldsContainer($fieldsContainer){
		$this->fieldsContainer = $fieldsContainer;
	}

	public function getFieldsContainerClasses(){
		return $this->fieldsContainerClasses;
	}

	public function setFieldsContainerClasses(array $fieldsContainerClasses) {
		$this->fieldsContainerClasses = $fieldsContainerClasses;
	}

	public function formStart(){
		$query = $this->query->all();
		$url = '';
		return FrontEndApplication::part(FrontFilterController::class, 'form', $this->application->getContainer(), array($query, $url));
	}

	public function create(){
		if(empty($this->fields)){
			throw new \LogicException('no fields has been setted');
		}
		$beforeFilter = $this->filter->apply('cppress-filter-before', '');
		$afterFilter = $this->filter->apply('cppress-filter-after', '');
		$filterContainer = $beforeFilter . $this->container . $afterFilter;

		return sprintf($filterContainer,
			$this->containerClasses,
			$this->renderFields()
		);
	}

	public function createInfoBox(){
		return FrontEndApplication::part(FrontFilterController::class, 'infobox', $this->application->getContainer(), array($this->query));
	}

	public function formEnd(){
		return '</form>';
	}

	public function setField(FieldInterface $field){
		$this->fields[] = $field;
	}

	public function all() {
		return $this->fields;
	}

	private function renderFields(){
		return implode("\n", array_map(function(FieldInterface $field){
			return sprintf($this->fieldsContainer, $this->fieldsContainerClasses, $field->render());
		}, $this->fields));
	}

}