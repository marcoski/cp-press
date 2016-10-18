<?php
namespace CpPress\Application\WP\Theme\Filter;

use CpPress\Application\WP\Theme\Filter\Field\FieldInterface;

interface FilterInterface {


	public function formStart();
	public function formEnd();

	public function create();
	public function createInfoBox();

	public function setField(FieldInterface $field);

	public function all();

	public function getContainer();
	public function setContainer($container);
	public function getContainerClasses();
	public function setContainerClasses(array $containerClasses);

	public function getFieldsContainer();
	public function setFieldsContainer($fieldsContainer);
	public function getFieldsContainerClasses();
	public function setFieldsContainerClasses(array $fieldsContainerClasses);

}