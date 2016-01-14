<?php
namespace CpPress\Application\WP\Theme\Media;


use Countable;
use IteratorAggregate;
use ArrayIterator;

abstract class Media implements Countable, IteratorAggregate{
	
	
	protected $type;
	protected $media;
	
	public function __construct($id=-1, $type){
		$this->type = $type;
		$this->load($id);
	}
	
	public function load($id){
		if($id < -1){
			$this->media = get_attached_media($this->type, $id);
		}
	}
	
	
	public function getType(){
		return $this->type;
	}
	
	public function getMedia(){
		return $this->media;
	}
	
	public function count(){
		return count($this->media);
	}
	
	public function getIterator(){
		return new ArrayIterator($this->media);
	}
	
	public function exists($offset){
		return isset($this->media[$offset]);
	}
	
	public function get($offset){
		return $this->media[$offset];
	}
	
	public function getMime($offset){
		return $this->media[$offset]->post_mime_type;
	}
	
	public function getTitle($offset){
		return $this->media['offset']->post_title;
	}
	
	public function set($offset){
		$this->media[$offset] = get_post($offset);
	}
	
	public function reset($offset){
		unset($this->media[$offset]);
	}
	
}