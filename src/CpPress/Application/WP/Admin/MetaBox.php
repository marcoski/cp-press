<?php
namespace CpPress\Application\WP\Admin;

use Closure;
use CpPress\Exception\MetaBoxException;
use CpPress\Application\WP\MetaType\PostType;
class MetaBox{
	
	private $id;
	private $title;
	
	private $defaultPostType = array('post','page','dashboard','link','attachment', 'comment');
	private $postType = null;
	
	private $validContexts = array('normal', 'advanced', 'side');
	private $context;
	
	private $validPriorities = array('high', 'core', 'default', 'low');
	private $priority;
	
	private $callback;
	
	public function __construct($id, $title){
		$this->id = $id;
		$this->title = $title;
		$this->context = 'advanced';
		$this->priority = 'default';
	}
	
	public function setPostType($postType){
		if($postType instanceof PostType){
			$this->postType = $postType->getPostTypeName();
		}else if(in_array($postType, $this->defaultPostType)){
			$this->postType = $postType;
		}else{
			throw new MetaBoxException('Invalid screen '.$postType);
		}
	}
	
	public function setContext($context){
		if(in_array($context, $this->validContexts)){
			$this->context = $context;
		}else{
			throw new MetaBoxException('Invalid screen '.$context);
		}
	}
	
	public function setPriority($priority){
		if(in_array($priority, $this->validPriorities)){
			$this->priority = $priority;
		}else{
			throw new MetaBoxException('Invalid screen '.$priority);
		}
	}
	
	public function setCallback(Closure $callback){
		$this->callback = $callback;
	}
	
	public function add($args=null){
		add_meta_box(
			$this->id, 
			$this->title, 
			$this->callback, 
			$this->postType, 
			$this->context, 
			$this->priority,
			$args
		);
	}
	
}