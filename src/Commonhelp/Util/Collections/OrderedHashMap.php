<?php
namespace Commonhelp\Util\Collections;

/**
 * A hash map which keeps track of deletions and additions.
 *
 * Like in associative arrays, elements can be mapped to integer or string keys.
 * Unlike associative arrays, the map keeps track of the order in which keys
 * were added and removed. This order is reflected during iteration.
 *
 * The map supports concurrent modification during iteration. That means that
 * you can insert and remove elements from within a foreach loop and the
 * iterator will reflect those changes accordingly.
 *
 * While elements that are added during the loop are recognized by the iterator,
 * changed elements are not. Otherwise the loop could be infinite if each loop
 * changes the current element:
 *
 *     $map = new OrderedHashMap();
 *     $map[1] = 1;
 *     $map[2] = 2;
 *     $map[3] = 3;
 *
 *     foreach ($map as $index => $value) {
 *         echo "$index: $value\n"
 *         if (1 === $index) {
 *             $map[1] = 4;
 *             $map[] = 5;
 *         }
 *     }
 *
 *     print_r(iterator_to_array($map));
 *
 *     // => 1: 1
 *     //    2: 2
 *     //    3: 3
 *     //    4: 5
 *     //    Array
 *     //    (
 *     //        [1] => 4
 *     //        [2] => 2
 *     //        [3] => 3
 *     //        [4] => 5
 *     //    )
 *
 * The map also supports multiple parallel iterators. That means that you can
 * nest foreach loops without affecting each other's iteration:
 *
 *     foreach ($map as $index => $value) {
 *         foreach ($map as $index2 => $value2) {
 *             // ...
 *         }
 *     }
 *
 */
class OrderedHashMap implements \ArrayAccess, \IteratorAggregate, \Countable{
	
	private $elements = array();
	
	private $orderedKeys = array();
	
	private $managedCursors = array();
	
	public function __construct(array $elements = array()){
		$this->elements = $elements;
		$this->orderedKeys = array_keys($elements);
	}
	
	public function offsetExists($key){
		return isset($this->elements[$key]);
	}
	
	public function offsetGet($key){
		if(!isset($this->elements[$key])){
			throw new \OutOfBoundsException('The offset "'.$key.'" does not exists');
		}
		
		return $this->elements[$key];
	}
	
	public function offsetSet($key, $value){
		if(null === $key || isset($this->elements[$key])){
			if(null === $key){
				$key = array() === $this->orderedKeys ? 0 : 1 + (int) max($this->orderedKeys);
			}
			
			$this->orderedKeys[] = $key;
		}
		
		$this->elements[$key] = $value;
	}
	
	public function offsetUnset($key){
		if(false !== ($position = array_search($key, $this->orderedKeys))){
			array_splice($this->orderedKeys, $position, 1);
			unset($this->elements[$key]);
			
			foreach($this->managedCursors as $i => $cursor){
				if($cursor >= $position){
					--$this->managedCursors[$i];
				}
			}
		}
	}
	
	public function getIterator(){
		return new OrderedHashMapIterator($this->elements, $this->orderedKeys, $this->managedCursors);
	}
	
	public function count(){
		return count($this->elements);
	}
}