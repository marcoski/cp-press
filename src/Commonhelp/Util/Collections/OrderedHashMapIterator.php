<?php
namespace Commonhelp\Util\Collections;

class OrderedHashMapIterator implements \Iterator{
	
	private $elements;
	
	private $orderedKeys;
	
	private $cursor;
	
	private $cursorId;
	
	private $managedCursor;
	
	private $key;
	
	private $current;
	
	public function __construct(array &$elements, array &$orderedKeys, array &$managedCursors){
		$this->elements = &$elements;
		$this->orderedKeys = &$orderedKeys;
		$this->managedCursor = &$managedCursors;
		$this->cursorId = count($this->managedCursor);
		
		$this->managedCursor[$this->cursorId] = &$this->cursor;
	}
	
	public function __destruct(){
		array_splice($this->managedCursor, $this->cursorId, 1);
	}
	
	public function current(){
		return $this->current;
	}
	
	public function next(){
		++$this->cursor;
		
		if(isset($this->orderedKeys[$this->cursor])){
			$this->key = $this->orderedKeys[$this->cursor];
			$this->current = $this->elements[$this->key];
		}else{
			$this->key = null;
			$this->current = null;
		}
	}
	
	public function key(){
		return $this->key;
	}
	
	public function valid(){
		return null !== $this->key;
	}
	
	public function rewind(){
		$this->cursor = 0;
		
		if(isset($this->orderedKeys[0])){
			$this->key = $this->orderedKeys[0];
			$this->current = $this->elements[$this->key];
		}else{
			$this->key = null;
			$this->current = null;
		}
	}
	
}