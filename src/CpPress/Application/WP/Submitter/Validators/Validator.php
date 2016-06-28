<?php
namespace CpPress\Application\WP\Submitter\Validators;

use ArrayAccess;
use Commonhelp\App\Http\RequestInterface;

abstract class Validator implements ArrayAccess{
	
	private $container;
	protected $invalidFields;
	
	protected $request;
	
	public function __construct(RequestInterface $request){
		$this->container = array(
			'valid' => true,
			'reason' => array(),
			'idref' => array()
		);
		$this->request = $request;
	}
	
	public function getRequest(){
		return $this->request;
	}
	
	public function getInvalidFields(){
		return $this->invalidFields;
	}
	
	public function isValid($name=null){
		if(!is_null($name)){
			return !isset($this->invalidFields[$name]);
		}
		
		return empty($this->invalidFields);
	}
	
	abstract public function invalidate($context, $message);
	abstract public function validate($evaluator, $eval);
	
	public function offsetSet($offset, $value){
		if(isset($this->container[$offset])){
			$this->container[$offset] = $value;
		}
		
		if($offset == 'reason' && is_array($value)){
			foreach($value as $k => $v){
				$this->invalidate($k, $v);
			}
		}
	}
	
	public function offsetExists($offset){
		return isset($this->container[$offset]);
	}
	
	public function offsetGet($offset){
		if(isset($this->container[$offset])){
			return $this->container[$offset];
		}
		
		return null;
	}
	
	public function offsetUnset($offset){
		unset($this->container[$offset]);
	}
	
}