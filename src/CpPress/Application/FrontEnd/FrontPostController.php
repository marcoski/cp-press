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
		$qargs = array(
				'post_type'			=> isset($posts['posttype']) ? $posts['posttype'] : 'post',
				'posts_per_page'	=> $posts['limit'],
				'category__in'		=> isset($posts['categories']) ? $posts['categories'] : array(),
				'tag__in'			=> isset($posts['tags']) ? $posts['tags'] : array(),
				'offset'			=> $posts['offset'],
				'order'				=> $posts['order'],
				'orderby'			=> $posts['orderby'],
				/* Set it to false to allow WPML modifying the query. */
				'suppress_filters' => false
		);
		$this->wpQuery->setLoop($qargs);
		if($posts['postspercolumn'] != '' && $posts['postspercolumn'] > 0){
			$this->assign('postWidth', floor(12/$posts['postspercolumn']));
		}else{
			$this->assign('postWidth', floor(12/$this->wpQuery->found_posts));
		}
		$this->assign('posts', $posts);
		$this->assign('filter', $this->filter);
		$this->assign('wpQuery', $this->wpQuery);
	}
	
	public function single($query, $instance){
		$query = $this->filter->apply('cppress_widget_post_queryargs', $query, $instance);
		$template = new WPTemplate($this);
		$template->setTemplateDirs(array(get_template_directory().'/', get_stylesheet_directory().'/'));
		if($instance['wtitle'] !== ''){
			$templateName = $this->filter->apply('cppress_widget_post_template_name',
				'template-parts/' . 'single-' . 
					Inflector::delimit(Inflector::camelize($instance['wtitle']), '-'), $options);
		}else{
			$templateName = $this->filter->apply('cppress_widget_post_template_name',
					'template-parts/' . 'single', $options);
		}
		$this->assign('templateName', $templateName);
		$this->assign('template', $template);
		$this->wpQuery->setLoop($query);
		$this->assign('filter', $this->filter);
		$this->assign('instance', $instance);
		$this->assign('wpQuery', $this->wpQuery);
	}
	
	
	
}