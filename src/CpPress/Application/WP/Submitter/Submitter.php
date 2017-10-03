<?php
namespace CpPress\Application\WP\Submitter;

use Commonhelp\App\Http\Request;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Hook\Hook;
abstract class Submitter{
	
	protected $request;
	protected $filter;
	protected $hook;
	protected $uploadedFiles = array();
	protected $data;
	protected $instance;
	protected $args;
	protected $meta = array();
	protected $validator;
	
	public function __construct(Request $request, Filter $filter, Hook $hook){
		$this->request = $request;
		$this->filter = $filter;
		$this->hook = $hook;
		$this->instance = array();
		$this->args = array();
	}
	
	public function getUploadedFiles(){
		return $this->uploadedFiles;
	}
	
	public function getMeta($name){
		if(isset($this->meta[$name])){
			return $this->meta[$name];
		}
	
		return null;
	}
	
	public function addUploadedFile($name, $path){
		$this->uploadedFiles[$name] = $path;
		if(empty($this->data[$name])){
			$this->data[$name] = basename($path);
		}
	}
	
	public function removeUploadedFiles(){
		foreach($this->uploadedFiles as $name => $path){
			@unlink($path);
			@rmdir(dirname($path));
		}
	}
	
	public function getData($name=''){
		if($name != ''){
			if(isset($this->data[$name])){
				return $this->data[$name];
			}
			
			return null;
		}
		
		return $this->data;
	}
	
	public function checkNonce($nonce){
		return is_null($this->request->getParam('_wpnonce')) || 
		!wp_verify_nonce($this->request->getParam('_wpnonce'), $nonce);
	}
	
	protected function submit(){
		
	}
	
	protected function setupData(){
		
	}
	
	protected function sanitize($value){
		if(is_array($value)){
			$value = array_map(function($v){
				return $this->sanitize($v);
			}, $value);
		}else if(is_string($value)){
			$value = wp_check_invalid_utf8($value);
			$value = wp_kses_no_null($value);
		}

		return $value;
	}
	
	abstract function ajaxSubmit($instance, $args);
	abstract function nonajaxSubmit($instance, $args);
	
}