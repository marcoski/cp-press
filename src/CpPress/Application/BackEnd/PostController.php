<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\Settings;
use CpPress\Application\WP\Query\Query;
use Commonhelp\Util\Hash;
use CpPress\Application\WP\Query\Db;
use CpPress\Application\WP\MetaType\PostType;

class PostController extends WPController{
	
	private $query;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Query $query){
		parent::__construct($appName, $request, $templateDirs);
		$this->query = $query;
	}
	
	public function singlebox(){
	}
	
	public function advanced($instance, $single, $showViewOptions=false){
		if(isset($instance['id']['posttype'])){
			$this->assign('posttypes', PostType::getPostTypes(array('public' => true)));
		}
		$this->assign('values', $instance);
		$this->assign('single', $single);
		$this->assign('show_view_options', $showViewOptions);
	}
	
	public function select($name, $id, $value){
		$posts = $this->query->find(array('post_type' => 'post'));
		$this->assign('items', Hash::combine($posts, '{n}.ID', '{n}.post_title'));
		$this->assign('name', $name);
		$this->assign('id', $id);
		$this->assign('value', $value);
	}
	
	/**
	 * @responder wpjson
	 */
	public function xhr_widget_search_post(){
		global $wpdb;
		$validTypes = $this->getParam('valid', array());
		if(empty($validTypes)){
			$postTypes = PostType::getPostTypes(array(
					'public' => true,
			));
			unset($postTypes['attachment']);
		}else{
			$postTypes = $validTypes;
		}
		if($this->getParam('query') !== ''){
			$query = "AND post_title LIKE '%" . esc_sql( $this->getParam('query') ) . "%'";
		}else{
			$query = '';
		}
		$postTypes = "'" . implode("', '", array_map( 'esc_sql', $postTypes ) ) . "'";
		$results = $wpdb->get_results( "
				SELECT ID, post_title, post_type
				FROM {$wpdb->posts}
		WHERE
		post_type IN ( {$postTypes} ) AND post_status = 'publish' {$query}
		ORDER BY post_modified DESC
		LIMIT 20
		", ARRAY_A );
		
		return $results;
	}
	
	public function save($id){
		if($this->getParam('cp-press-post-options', null) !== null){
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
				return;
		
			update_post_meta($id, 'cp-press-post-options', $this->getParam('cp-press-post-options'));
		}
	}
	
}