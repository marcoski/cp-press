<?php
namespace Commonhelp\App;
use Commonhelp\DI\ContainerInterface;

interface ApplicationInterface{
	
	public static function main($controllerName, $methodName, ContainerInterface $container, array $urlParams = null);
	
	public static function part($controllerName, $methodName, ContainerInterface $container, array $urlParams = null);
	
}