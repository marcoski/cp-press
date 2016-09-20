<?php
namespace Commonhelp\App\Template\Helper;

abstract class Helper implements HelperInterface{
	
	protected $charset = 'UTF-8';
	
	public function getCharset(){
		return $this->charset;
	}
	
	public function setCharset($charset){
		$this->charset = $charset;
	}
	
}