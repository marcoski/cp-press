<?php
namespace CpPress\Application\WP\Query;

use WP_Query;

class Query extends WP_Query implements \ArrayAccess, \IteratorAggregate, \Countable {

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

	public function related($id, $count, array $args = array()){
		$args = wp_parse_args($args, array(
			'orderby' => 'rand',
			'return' => 'loop'
		));

		$relatedArgs = array(
			'post_type' => get_post_type($id),
			'posts_per_page' => $count,
			'post_status' => 'publish',
			'orderby' => $args['orderby'],
			'tax_query' => array()
		);

		$post = get_post($id);
		$taxonomies = get_object_taxonomies($post, 'names');

		foreach($taxonomies as $taxonomy){
			$terms = get_the_terms($id, $taxonomy);
			if(empty($terms)){
				continue;
			}
			$termList = wp_list_pluck($terms, 'slug');
			$relatedArgs['tax_query'][] = array(
				'taxonomy' => $taxonomy,
				'field' => 'slug',
				'terms' => $termList
			);
		}

		if(count($relatedArgs['tax_query']) > 1){
			$relatedArgs['tax_query']['relation'] = 'OR';
		}

		if('loop' === $args['return']){
			$this->setLoop($relatedArgs);
			return;
		}

		return $relatedArgs;
	}

	public function findLastByCategory(){

	}
	
	public function setQuery($query){
		$this->query = $query;
	}
	
	public function setLoop($query){
		$this->query = $query;
		$this->query($query);
	}

	public function get($query_var, $default = ''){
		if($this->has($query_var)){
			return $this->query[$query_var];
		}

		return null;
	}

	public function offsetGet($var){
		return $this->get($var);
	}

	public function has($var){
		return isset($this->query[$var]);
	}

	public function offsetExists($var){
		return $this->has($var);
	}

	public function set($var, $value){
		$this->query[$var] = $value;
		return $this;
	}

	public function offsetSet($var, $value) {
		$this->set($var, $value);
	}

	public function all(){
		return $this->query;
	}

	public function remove($var){
		if($this->has($var)){
			unset($this->query[$var]);
		}
	}

	public function offsetUnset($var){
		$this->remove($var);
	}

	public function getIterator(){
		return new \ArrayIterator($this->query);
	}

	public function count(){
		return $this->found_posts;
	}

	public function order(callable $order){
		$this->posts = $order($this->posts, $this);
	}

}