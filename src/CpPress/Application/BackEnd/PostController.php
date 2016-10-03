<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\Settings;
use CpPress\Application\WP\Query\Query;
use Commonhelp\Util\Hash;
use CpPress\Application\WP\Query\Db;
use CpPress\Application\WP\MetaType\PostType;
use CpPress\Application\Widgets\CpWidgetBase;
use CpPress\Application\BackEndApplication;
use CpPress\CpPress;

class PostController extends WPController{
	
	private $query;
	
	public static $convertTaxonomyToFormForCompatibility = array(
		'category' => array('categories', 'excludecat'),
		'post_tag' => array('tags', 'excludetags')
	);
	
	private static $exludedTaxonomies = array('post_format' => 1);
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Query $query){
		parent::__construct($appName, $request, $templateDirs);
		$this->query = $query;
	}
	
	public function advanced(CpWidgetBase $widget, $instance, array $options){
		$options = wp_parse_args($options, array('single' => false, 'show_view_options' => false));
		$instance = $this->getTaxonomyInstanceCompatibility($instance);
		$this->assign('taxonomies', $this->getTaxonomiesRepeater($widget, $instance));
		$this->assign('posttypes', PostType::getPostTypes(array('public' => true)));
		$this->assign('instance', $instance);
		$this->assign('single', $options['single']);
		$this->assign('widget', $widget);
		$this->assign('show_view_options', $options['show_view_options']);
	}
	
	/**
	 * @responder wpjson
	 */
	public function xhr_taxonomies(){
		$id = $this->getParam( 'id' );
		$name = $this->getParam( 'name' );
		$count = $this->getParam('count');
		$this->assign('taxonomy', $this->getTaxonomy($name));
		$this->assign('values', $this->getParam('values', array()));
		$this->assign('id', $id);
		$this->assign('name', $name);
		$this->assign('count', $count);
	}
	
	public function xhr_change_taxonomy(CpWidgetBase $widget){
		$instance = array();
		$instance['posttype'] = $this->getParam('post_type', '');
		if($instance['posttype'] === ''){
			$instance['posttype'] = 'post';
		}
		$taxonomies = array();
		foreach($this->getTaxonomiesRepeater($widget, $instance) as $repeater){
			$taxonomies[] = $repeater;
		}
		
		return $taxonomies;
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
	
	private function getTaxonomiesRepeater($widget, $instance){
		$repeaters = array();
		
		foreach($this->getTaxonomiesToForm($instance['posttype']) as $taxonomy => $taxonomyRepeater){
			$repeaters[$taxonomy] = BackEndApplication::part(
					'FieldsController', 'repeater', CpPress::$App->getContainer(),
					array(
							$widget->get_field_id( $taxonomy ),
							$widget->get_field_name( $taxonomy ),
							!isset($instance[$taxonomy]) ? array() : $instance[$taxonomy],
							array('add' => 'post_advanced_add_taxonomy'),
							__($taxonomyRepeater['label'] . ' filter', 'cppress'),
							__($taxonomyRepeater['label'], 'cppress')
					)
			);
		}
		return $repeaters;
	}
	
	private function getTaxonomiesToForm($postType){
		$taxonomies = get_object_taxonomies($postType, 'object');
		$taxonomies = array_diff_key($taxonomies, PostController::$exludedTaxonomies);
		$taxonomiesToForm = array();
		foreach($taxonomies as $taxonomy){
			if(array_key_exists($taxonomy->name, PostController::$convertTaxonomyToFormForCompatibility)){
				$taxonomiesToForm[$taxonomy->name] = PostController::$convertTaxonomyToFormForCompatibility[$taxonomy->name];
			}else{
				$taxonomiesToForm[$taxonomy->name] = array($taxonomy->name, 'exclude_'.$taxonomy->name);
			}
			$taxonomiesToForm[$taxonomy->name]['label'] = $taxonomy->label;
		}
		return $taxonomiesToForm;
	}
	
	private function getTaxonomy($id){
		preg_match("/\[[0-9]+\]\[([A-Za-z0-9_]+)\]/", $id, $matches);
		$taxForm = $matches[1];
		foreach(PostController::$convertTaxonomyToFormForCompatibility as $taxonomy => $taxCompatibility){
			if($taxForm === $taxonomy){
				$taxCompatibility['term'] = $taxonomy;
				return $taxCompatibility;
			}
		}
		
		return array($taxForm, 'exclude_'.$taxForm, 'term' => $taxForm);
	}
	
	private function getTaxonomyInstanceCompatibility($instance){
		$taxonomies = get_object_taxonomies($postType);
		$taxonomies = array_diff_key($taxonomies, PostController::$exludedTaxonomies);
		foreach(PostController::$convertTaxonomyToFormForCompatibility as $taxonomy => $taxCompatibility){
			if(isset($instance[$taxCompatibility[0]])){
				$instance[$taxonomy][$taxCompatibility[0]] = $instance[$taxCompatibility[0]];
				unset($instance[$taxCompatibility[0]]);
			}
			if(isset($instance[$taxCompatibility[1]])){
				$instance[$taxonomy][$taxCompatibility[1]] = $instance[$taxCompatibility[1]];
				unset($instance[$taxCompatibility[1]]);
			}
			$instance[$taxonomy]['countitem'] = count($instance[$taxonomy][$taxCompatibility[0]]);
		}
		return $instance;
	}
	
	public static function correctInstanceForCompatibility($instance){
		if(empty($instance) || $instance === null){
			return $instance;
		}
		
		
		foreach(PostController::$convertTaxonomyToFormForCompatibility as $t => $taxonomies){
			foreach($taxonomies as $taxonomy){
				if(isset($instance[$taxonomy]) && isset($instance[$taxonomy][0]) && is_array($instance[$taxonomy][0])){
					$taxonomyValues = $instance[$taxonomy][0];
					unset($instance[$taxonomy][0]);
					$instance[$taxonomy] = $taxonomyValues;
				}
			}
			
			if(isset($instance[$t])){
				foreach($taxonomies as $taxonomy){
					$instance[$taxonomy] = $instance[$t][$taxonomy];
				}
				unset($instance[$t]);
			}
		}
		
		return $instance;
	}
	
}