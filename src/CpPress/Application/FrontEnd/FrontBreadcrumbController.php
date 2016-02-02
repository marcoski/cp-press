<?php
namespace CpPress\Application\FrontEnd;

use \Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Query\Query;

class FrontBreadcrumbController extends WPController{
	
	private $filter;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $frontEndFilter){
		parent::__construct($appName, $request, $templateDirs);
		$this->filter = $frontEndFilter;
	}
	
	public function show($post){
		$elements = array();
		
		$elements[] = array(
			'title' => __('Home', 'cppress'),
			'link' => get_bloginfo( 'url' ),
			'active' => false
		);
		
		if(is_attachment()){
			$aid = get_query_var('attachment_id');
			$elements[] = array(
				'title' => get_the_title(),
				'link' => get_attachment_link($aid),
				'active' => true
			);
		}else if(is_singular()){
			$parentId = wp_get_post_parent_id(get_the_ID());
			$parentTitle = get_the_title($parentId);
			$parenPermalink = get_permalink($parentId);
			if($post->post_parent){
				foreach($this->getPostChilds($post) as $element){
					$elements[] = $element;
				}
			}
			
			$elements[] = array(
				'title' => get_the_title(),
				'link' => '#',
				'active' => true
			);
			
			
		}else if(is_tax()){
			$qObject = get_queried_object();
			$elements[] = array(
				'title' => $qObject->name,
				'link' => '#',
				'active' => true
			);
		}else if(is_category()){
			$currentCategoryId = get_query_var('cat');
			
		}else if(is_tag()){
			$currentTagId = get_query_var('tag_id');
			$currentTag = get_tag($currentTagId);
			$elements[] = array(
				'title' => $currentTag->name,
				'link' => get_tag_link($currentTagId),
				'active' => true
			);
		}else if(is_author()){
			$elements[] = array(
				'title' => get_the_author(),
				'link' => get_author_posts_url( get_the_author_meta('ID')),
				'active' => true
			);
 		}else if(is_search()){
			$elements[] = array(
				'title' => sanitize_text_field(get_query_var('s')),
				'link' => '#',
				'active' => true
			);
		}else if(is_404()){
			$elements[] = array(
				'title' => '404',
				'link' => '#',
				'active' => true
			);
		}
		
		$this->assign('elements', $elements);
		$this->assign('filter', $this->filter);
		
	}
	
	private function getPostChilds($post){
		$childs = array();
		$home = get_page(get_option('page_on_front'));
		
		for($i=count($post->ancestors)-1; $i >= 0; $i--){
			if($home->ID != $post->ancestors[$i]){
				$childs[] = array(
						'title' => get_the_title($post->ancestors[$i]),
						'link' => get_the_permalink($post->ancestors[$i]),
						'active' => false
				);
			}
		}
		
		return $childs;
	}
	
	
	
}