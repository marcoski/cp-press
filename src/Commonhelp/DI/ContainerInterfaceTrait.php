<?php
namespace Commonhelp\DI;

trait ContainerInterfaceTrait{
	
	protected $container;
	
	public function setContainer(ContainerInterface $container){
		$this->container = $container;
	}
	
}