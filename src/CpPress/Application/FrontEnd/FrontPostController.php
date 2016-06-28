<?php
namespace CpPress\Application\FrontEnd;

use \Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Theme\Media\Image;
use Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Query\Query;
use Commonhelp\WP\WPTemplate;
use Commonhelp\Util\Inflector;

class FrontPostController extends WPController{
	
	private $filter;
	private $wpQuery;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $frontEndFilter, Query $wpQuery){
		parent::__construct($appName, $request, $templateDirs);
		$this->filter = $frontEndFilter;
		$this->wpQuery = $wpQuery;
	}
	
	public function loop($posts){
		$offset = $posts['offset'];
		if(isset($posts['paginate'])){
			$page = get_query_var('paged', 1);
			if($page === 0){
				$page = 1;
			}
			if($posts['offset'] > 0){
				$offset = ($posts['limit'] * ($page-1)) + $offset;
			}else{
				$offset = $posts['limit'] * ($page-1);
			}
		}
		$qargs = array(
				'post_type'			=> isset($posts['posttype']) ? $posts['posttype'] : 'post',
				'posts_per_page'	=> $posts['limit'],
				'category__in'		=> isset($posts['categories']) ? $posts['categories'] : array(),
				'category__not_in' => isset($posts['excludecat']) ? $posts['excludecat'] : array(),
				'tag__in'			=> isset($posts['tags']) ? $posts['tags'] : array(),
				'tag__not_in' => isset($posst['excludetags']) ? $posts['excludetags'] : array(),
				'offset'			=> $offset,
				'order'				=> $posts['order'],
				'orderby'			=> $posts['orderby'],
				/* Set it to false to allow WPML modifying the query. */
				'suppress_filters' => false
		);
		$this->wpQuery->setLoop($qargs);
		if($posts['postspercolumn'] != '' && $posts['postspercolumn'] > 0){
			$this->assign('postWidth', floor(12/$posts['postspercolumn']));
		}else{
			if($this->wpQuery->found_posts > 0){
				$this->assign('postWidth', floor(12/$this->wpQuery->found_posts));
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
		$qargs = array(
				'post_type'			=> isset($posts['posttype']) ? $posts['posttype'] : 'post',
				'posts_per_page'	=> $posts['limit'],
				'category__in'		=> isset($posts['categories']) ? $posts['categories'] : array(),
				'category__not_in' => isset($posts['excludecat']) ? $posts['excludecat'] : array(),
				'tag__in'			=> isset($posts['tags']) ? $posts['tags'] : array(),
				'tag__not_in' => isset($posst['excludetags']) ? $posts['excludetags'] : array(),
				'offset'			=> $offset,
				'order'				=> $posts['order'],
				'orderby'			=> $posts['orderby'],
				/* Set it to false to allow WPML modifying the query. */
				'suppress_filters' => false
		);
		$this->wpQuery->setLoop($qargs);
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
	
	public function single($query, $instance){
		$query = $this->filter->apply('cppress_widget_post_queryargs', $query, $instance);
		$this->assignTemplate($instance, 'single');
		$this->wpQuery->setLoop($query);
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
		$this->assign('templateName', $templateName);
		$this->assign('template', $template);
	}
	
	
	
}