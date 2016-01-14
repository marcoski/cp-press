<?php
namespace CpPress\Application\WP\Query;

use WP_Query;

class Query extends WP_Query{

	public function __construct($query=''){
		parent::__construct($query);
	}
	
	public function find($query){
		$this->setQuery($query);
		return $this->query($this->query);
	}
	
	public function findAll(){
		if(is_null($this->query)){
			return null;
		}
		$data = $this->query($this->query);
		$this->reset_postdata();
		
		return $data;
	}
	
	public function setQuery($query){
		$this->query = $query;
	}
	
}