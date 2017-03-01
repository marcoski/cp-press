<?php
namespace CpPress\Application\WP\Admin;

class PostMeta{
	
	private $postId;
	
	private static $instances = array();
	
	public function __construct($postId = -1){
		if($postId > 0){
			$this->postId = $postId;
		}
	}
	
	public function setPostId($postId){
		$this->postId = $postId;
	}
	
	public function _find($key){
		return get_post_meta($this->postId, $key, true);
	}
	
	public static function find($id, $key){
		if(!isset(self::$instances[$id])){
			self::$instances[$id] = new static($id);
		}
		return self::$instances[$id]->_find($key);
	}
	
	public function _findAll(){
		$metas = get_post_meta($id);
		$toReturn = array();
		foreach($metas as $key => $meta){
			foreach($meta as $k => $v){
				if(is_serialized($v))
					$toReturn[$key] = unserialize($v);
				else
					$toReturn[$key] = $v;
			}
		}
		
		return $toReturn;
	}
	
	public static function findAll($id){
		if(!isset(self::$instances[$id])){
			self::$instances[$id] = new static($id);
			
		}
		
		return self::$instances[$id]->_findAll($id);
	}
	
}