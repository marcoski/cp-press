<?php
namespace CpPress\Application\FrontEnd;

use Commonhelp\App\Http\JsonResponse;
use \Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Query\Query;
use Commonhelp\WP\WPTemplate;
use Commonhelp\Util\Inflector;
use CpPress\Application\BackEnd\FieldsController;
use Commonhelp\Util\Hash;
use CpPress\Application\BackEnd\PostController;
use CpPress\Application\WP\Theme\Paginate\AjaxPaginator;

class FrontPostController extends WPController{
	
	private $filter;
	private $wpQuery;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $frontEndFilter, Query $wpQuery){
		parent::__construct($appName, $request, $templateDirs);
		$this->filter = $frontEndFilter;
		$this->wpQuery = $wpQuery;
	}
	
	public function loop($posts){
		$this->wpQuery->setLoop(FrontPostController::getQueryArgs($posts));
		if($posts['postspercolumn'] != '' && $posts['postspercolumn'] > 0){
			$this->assign('postWidth', floor(12/$posts['postspercolumn']));
		}else{
			if($this->wpQuery->post_count > 0){
				$this->assign('postWidth', floor(12/$this->wpQuery->post_count));
			}else{
				$this->assign('postWidth', 12);
			}
		}
		$this->assignTemplate($posts, 'loop');
		$this->assign('posts', $posts);
		$this->assign('filter', $this->filter);
		$this->assign('wpQuery', $this->wpQuery);
	}
	
	/**
	 * @responder wpjson
	 */
	public function loop_loadmore(){
		$data = $this->getParams();
		$posts = $data['options'];
		$offset = (int) $posts['limit'] + $posts['offset'];
		$this->wpQuery->setLoop(FrontPostController::getQueryArgs($posts, $offset));
		if($this->wpQuery->post_count > 0){
			$this->setWpAjaxData('hasmore', true);
			$data['options']['offset'] = $offset;
		}else{
			$this->setWpAjaxData('hasmore', false);
		}
		$this->assignTemplate($posts, 'loop_loadmore');
		$this->assign('posts', $posts);
		$this->assign('filter', $this->filter);
		$this->assign('wpQuery', $this->wpQuery);
		$this->setWpAjaxData('data', $data);
	}


	public function xhr_search(){
		$posts = array();
		$queryArgs = $this->request->getParam('query');
		$this->wpQuery->setLoop($queryArgs);
		$i=0;
		while($this->wpQuery->have_posts()){
			$this->wpQuery->the_post();
			$posts[$i]['id'] = get_the_ID();
			$posts[$i]['title'] = get_the_title();
			$posts[$i]['permalink'] = get_the_permalink();
			$posts[$i]['excerpt'] = get_the_excerpt();
			$posts[$i]['date'] = get_the_date('');
			$posts[$i]['author'] = get_the_author();
			$posts[$i]['thumbnail'] = get_the_post_thumbnail(null, 'post-thumbnail', array('class' => 'img-responsive'));
			$posts[$i]['terms'] = wp_get_post_terms(get_the_ID());
			$posts[$i]['categories'] = wp_get_post_categories(get_the_ID(), array('fields' => 'all_with_object_id'));
			$posts[$i] = $this->filter->apply('cppress_search_post', $posts[$i]);
			$i++;
		}
		$this->wpQuery->reset_postdata();
		return new JsonResponse($this->filter->apply('cppress_search_posts', $posts));
	}

	public function xhr_paginate(){
		$queryArgs = $this->request->getParam('query', array());
		$queryArgs['paged'] = $this->request->getParam('paged', 1);
		$this->wpQuery->query($queryArgs);
		$paginator = new AjaxPaginator($this->wpQuery, $this->filter);
		return new JsonResponse(array(
			'html' => $paginator->renderAllElements(),
			'query' => $paginator->getQueryVar()
		));
	}
	
	public function single($instance){
		if(isset($instance['postid']) && $instance['postid'] != ''){
			$this->wpQuery->setLoop(FieldsController::getLinkArgs($instance['postid']));
		}else{
			$this->wpQuery->setLoop(FrontPostController::getQueryArgs($instance));
		}
		$this->assignTemplate($instance, 'single');
		$this->assign('filter', $this->filter);
		$this->assign('instance', $instance);
		$this->assign('wpQuery', $this->wpQuery);
	}
	
	/**
	 * @responder string
	 */
	public function paginate($instance){
		if($this->wpQuery->max_num_pages <= 1){
			return '';
		}
		$paged = get_query_var('paged', 1);
		if($paged === 0){
			$paged = 1;
		}
		$max = intval($this->wpQuery->max_num_pages);
		
		if($paged >= 1){
			$links[] = $paged;
		}
		
		if($paged >= 3){
			$links[] = $paged - 1;
			$links[] = $paged - 2;
		}
		
		if(($paged+2) <= $max){
			$links[] = $paged + 2;
			$links[] = $paged + 1;
		}
		
		$paginationClass = $this->filter->apply('cppress-pagination-ulclass', array('pagination'), $instance);
		$beforePagination = $this->filter->apply('cppress-pagination-ulbefore', '', $instance);
		$afterPagination = $this->filter->apply('cppress-pagination-ulafter', '', $instance);
		$pagination = $beforePagination . '<ul class="' . implode(' ', $paginationClass) . '">%s</ul>' . $afterPagination;
		
		$els = array();
		
		if($this->filter->apply('cppress-pagination-show-prevlink', true, $instance) && $paged > 1){
			$prevLinkText = $this->filter->apply('cppress-pagination-prevlink-text', '&laquo; ' . __('Prev', 'cppress'), $instance);
			$els[] = sprintf('<li><a href="%s">%s</a></li>', get_pagenum_link($paged-1), $prevLinkText);
		}
		
		if(!in_array(1, $links)){
			$class = 1 == $paged ? ' class="active"' : '';
			$els[] = sprintf(
				'<li%s><a href="%s">%s</a></li>', 
				$class,
				esc_url(get_pagenum_link(1)),
				'1' 
			);
		}
		
		sort($links);
		foreach((array) $links as $link){
			$class = $paged == $link ? ' class="active"' : '';
			$els[] = sprintf(
				'<li %s><a href="%s">%s</a></li>',
				$class,
				esc_url(get_pagenum_link($link)),
				$link
			);
		}
		
		if(!in_array($max, $links)){
			if(!in_array($max-1, $links)){
				$els[] = '<li><a>...</a></li>';
			}
			$class = $paged == $max ? ' class="active"' : '';
			$els[] = sprintf(
				'<li %s><a href="%s">%s</a></li>',
				$class,
				esc_url(get_pagenum_link($max)),
				$max
			);
		}
		
		if($this->filter->apply('cppress-pagination-show-nextlink', true, $instance) && $paged < $max){
			$nextLinkText = $this->filter->apply('cppress-pagination-naexlink-text', __('Next', 'cppress') . ' &raquo;');
			$els[] = sprintf('<li><a href="%s">%s</a></li>', get_pagenum_link($paged+1), $nextLinkText);
		}

		return sprintf($pagination, implode("\n", $els));
	}
	
	private function assignTemplate($instance, $tPreName){
		$template = new WPTemplate($this);
		$template->setTemplateDirs(array(get_template_directory().'/', get_stylesheet_directory().'/'));
		$templateName = '';
		if(isset($instance['templatename']) && $instance['templatename'] !== ''){
			$templateName = $this->filter->apply('cppress_widget_post_template_name',
					'template-parts/' . $instance['templatename'], $instance);
		}
		if(!$template->issetTemplate($templateName) && $instance['wtitle'] !== ''){
			$templateName = $this->filter->apply('cppress_widget_post_template_name',
					'template-parts/' . $tPreName . '-' .
					Inflector::delimit(Inflector::camelize($instance['wtitle']), '-'), $instance);
		}
		if(!$template->issetTemplate($templateName)){
			$templateName = $this->filter->apply('cppress_widget_post_template_name',
					'template-parts/' . $tPreName, $instance);
		}
        if(!$template->issetTemplate($templateName)){
		    $templateName = preg_replace("/(.php)/", "", $instance['templatename']);
            $templateName = $this->filter->apply('cppress_widget_post_template_name', $templateName);
        }
		$this->assign('templateName', $templateName);
		$this->assign('template', $template);
	}
	
	public static function getQueryArgs($instance, $offset=null){
		if($offset === null){
			$offset = $instance['offset'];
		}
		if(isset($instance['paginate'])){
			$page = get_query_var('paged', 1);
			if($page === 0){
				$page = 1;
			}
			if($instance['offset'] > 0){
				$offset = ($instance['limit'] * ($page-1)) + $offset;
			}else{
				$offset = $instance['limit'] * ($page-1);
			}
		}
		$postType = isset($instance['posttype']) ? $instance['posttype'] : 'post';
		return apply_filters('cppress_loop_args', array(
				'post_type'			=> $postType,
				'posts_per_page'	=> isset($instance['limit']) ? $instance['limit'] : '1',
				'tax_query' 	=> FrontPostController::getTaxQuery($instance),
				'offset'			=> $offset,
				'order'				=> $instance['order'],
				'orderby'			=> $instance['orderby'],
				/* Set it to false to allow WPML modifying the query. */
				'suppress_filters' => false
		), $postType);
	}
	
	private static function getTaxQuery($instance){
		$instance = PostController::correctInstanceForCompatibility($instance);
		$convertTaxonomyToFormForCompatibility = array(
			'category' => array('categories', 'excludecat'),
			'post_tag' => array('tags', 'excludetags')
		);
		$taxQuery = array();
		foreach(get_object_taxonomies($instance['posttype']) as $taxonomy){
			if(array_key_exists($taxonomy, $convertTaxonomyToFormForCompatibility)){
				$convertedTaxonomy = $convertTaxonomyToFormForCompatibility[$taxonomy];
                if(isset($instance[$convertedTaxonomy[1]])){
                    $excludeValues = array();
                    foreach($instance[$convertedTaxonomy[1]] as $toExclude => $val){
                        $excludeValues[] = $instance[$convertedTaxonomy[0]][$toExclude];

                    }
                    $taxQuery[] = array(
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $excludeValues,
                        'operator' => 'NOT IN'
                    );
                }else if(isset($instance[$convertedTaxonomy[0]])){
					$includeValues = $instance[$convertedTaxonomy[0]];
					if(isset($instance[$convertedTaxonomy[1]])){
						$includeValues = array();
						foreach($instance[$convertedTaxonomy[0]] as $toInclude => $val){
							if(is_array($instance[$convertedTaxonomy[1]]) && !in_array($toInclude, $instance[$convertedTaxonomy[1]])){
								$includeValues[] = $val;
							}
						}
					}
					$taxQuery[] = array(
						'taxonomy' => $taxonomy,
						'field' => 'term_id',
						'terms' => $includeValues
					);
				}
			}else{
				/** TODO HANDLE CUSTOM AND NEW TAXONOMIES */
                if(isset($instance[$taxonomy]['exclude_' . $taxonomy])){
                    $excludeValues = array();
                    foreach($instance[$taxonomy]['exclude_' . $taxonomy] as $toExclude => $val){
                        $excludeValues[] = $instance[$taxonomy][$taxonomy][$toExclude];

                    }
                    $taxQuery[] = array(
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $excludeValues,
                        'operator' => 'NOT IN'
                    );
                }else if(isset($instance[$taxonomy][$taxonomy])){
					$includeValues = $instance[$taxonomy][$taxonomy];
					if(isset($instance[$taxonomy]['exclude_' . $taxonomy])){
						$includeValues = array();
						foreach($instance[$taxonomy][$taxonomy] as $toInclude => $val){
							if(is_array($instance[$taxonomy]['exclude_' . $taxonomy]) && !in_array($toInclude, $instance[$taxonomy]['exclude_' . $taxonomy])){
								$includeValues[] = $val;
							}
						}
					}
					$taxQuery[] = array(
						'taxonomy' => $taxonomy,
						'field' => 'term_id',
						'terms' => $includeValues
					);
				}

			}
		}
		
		if(empty($taxQuery)){
			return array();
		}
		
		if(count($taxQuery) == 1){
			return $taxQuery;
		}else if(count($taxQuery) == 2){
			if(in_array('NOT IN', Hash::flatten($taxQuery))){
				$taxQuery['relation'] = 'AND';
			}else{
				$taxQuery['relation'] = 'OR';
			}
		}else{
			$toOr = array();
			array_walk($taxQuery, function($el, $key) use (&$toOr, &$taxQuery){
				if(is_array($el) && isset($el['operator']) && $el['operator'] === 'NOT IN'){
					$toOr[] = $el;
					unset($taxQuery[$key]);
				}
			});
			array_walk($taxQuery, function($el, $key) use (&$toOr, &$taxQuery){
				if(is_array($el) && !isset($el['operator']) && $el['taxonomy'] === $toOr[0]['taxonomy']){
					$toOr[] = $el;
					unset($taxQuery[$key]);
				}
			});
			$toOr['relation'] = 'AND';
			array_unshift($taxQuery, $toOr);
			$taxQuery = array_values($taxQuery);
			$taxQuery['relation'] = 'OR';
		}

		return $taxQuery;
	}
	
	
	
}