<?php
namespace Commonhelp\App\Http;

class JsonResponse extends Response{
	
	protected $data;
	
	public function __construct($data=array(), $statusCode=Http::STATUS_OK) {
		$this->data = $data;
		$this->setStatus($statusCode);
		$this->addHeader('Content-Type', 'application/json; charset=utf-8');
	}
	
	public function render() {
		$response = json_encode($this->data, JSON_HEX_TAG);
		if($response === false) {
			throw new \Exception(sprintf('Could not json_encode due to invalid ' .
					'non UTF-8 characters in the array: %s', var_export($this->data, true)));
		}
		return $response;
	}

	public function setData($data){
		$this->data = $data;
		return $this;
	}
	
	public function getData(){
		return $this->data;
	}
}