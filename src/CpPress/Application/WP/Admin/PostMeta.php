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
		$metas = get_post_meta($this->postId);
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
		
		return self::$instances[$id]->_findAll();
	}

	public function _add($key, $value)
    {
        return add_post_meta($this->postId, $key, $value);
    }

    public static function add($id, $key, $value)
    {
        if(!isset(self::$instances[$id])){
            self::$instances[$id] = new static($id);
        }

        return self::$instances[$id]->_add($key, $value);
    }

	public function _delete($key)
    {
        return delete_post_meta($this->postId, $key);
    }

    public static function delete($id, $key)
    {
        if(!isset(self::$instances[$id])){
            self::$instances[$id] = new static($id);
        }

        return self::$instances[$id]->_delete($key);
    }

    public function _update($key, $value)
    {
        return update_post_meta($this->postId, $key);
    }

    public static function update($id, $key, $value)
    {
        if(!isset(self::$instances[$id])){
            self::$instances[$id] = new static($id);
        }

        return self::$instances[$id]->_update($key, $value);
    }
	
}