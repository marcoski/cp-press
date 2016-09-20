<?php
namespace Commonhelp\App;

use Commonhelp\App\Http\Response;

abstract class Middleware{
	
	public function beforeController($controller, $methodName){
	}
	
	public function afterException($controller, $methodName, \Exception $exception){
		throw $exception;
	}
	
	public function afterController($controller, $methodName, Response $response){
		return $response;
	}
	
	public function beforeOutput($controller, $methodName, $output){
		return $output;
	}
	
}