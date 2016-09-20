<?php
namespace Commonhelp\App\Http;

class DataResponse extends Response {
	
	protected $data;
	
	public function __construct($data="", $statusCode=Http::STATUS_OK,
			$headers=[]) {
		$this->data = $data;
		$this->setStatus($statusCode);
		$this->setHeaders(array_merge($this->getHeaders(), $headers));
		$this->addHeader('Content-Disposition', 'inline; filename=""');
	}
	
	public function render() {
		return $this->data;
	}
	
	public function setData($data){
		$this->data = $data;
		return $this;
	}
	
	public function getData(){
		return $this->data;
	}
}