<?php
namespace CpPress\Application\FrontEnd;

use \Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Theme\Media\Image;
use Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Query\Query;
use Commonhelp\WP\WPTemplate;
use Commonhelp\WP\WPTemplateResponse;
use Commonhelp\WP\Commonhelp\WP;
use Commonhelp\Util\Inflector;

class FrontSliderController extends WPController{
	
	private $filter;
	private $wpQuery;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $frontEndFilter, Query $wpQuery){
		parent::__construct($appName, $request, $templateDirs);
		$this->filter = $frontEndFilter;
		$this->wpQuery = $wpQuery;
	}
	
	public function frontend_image($slides, $options){
		$options = wp_parse_args($options, array(
				'theme' => 'bootstrap',
				'speed' => 800,
				'timeout' => 8000,
				'navcolor' => '#ffffff',
				'link' => 'slide',
				'hideindicators' => 0,
				'hidecontrol' => 0
		));
		for($i=0; $i<count($slides); $i++){
			if(is_numeric($slides[$i]['img'])){
				$image = new Image();
				$image->set($slides[$i]['img']);
				$slideImg['src'] = $image->getImage($slides[$i]['img']);
				$slideImg['title'] = $image->getTitle($slides[$i]['img']);
				$slides[$i]['img'] = $slideImg;
			}else if(is_array($slides[$i]['img'])){
				$slides[$i]['img'][0] = $slides[$i]['img']; //uniform array
			}
		}
		$this->assign('options', $options);
		$this->assign('filter', $this->filter);
		$this->assign('slides', $slides);
		$templateTitle = str_replace(' ', '_', strtolower($options['title']));
		$template = new WPTemplate($this);
		$template->setTemplateDirs(array(get_template_directory().'/', get_stylesheet_directory().'/'));
		$this->assign('template', $template);
		$templateName = $this->filter->apply('cppress_widget_slider_post_template_name',
				'template-parts/slider_' . $templateTitle, $options);
		$this->assign('templateName', $templateName);
		
		return new WPTemplateResponse($this, 'frontend_image_'.$options['theme']);
	}
	
	public function frontend_parallax($slides, $options){
		$options = wp_parse_args($options, array(
				'theme' => 'bootstrap',
				'speed' => 800,
				'timeout' => 8000,
				'navcolor' => '#ffffff'
		));
		if(is_numeric($slides['img'])){
			$image = new Image();
			$image->set($slides['img']);
			$slides['img'] = $image->getImage($slides[$i]['img']);
		}else{
			$slides['img'][0] = $slides['img'];
		}
		$this->filter->register('cppress_layout_cell_attrs', function($attrs, $cell) use ($slides){
			$attrs['data-stellar-background-ratio'] = '0.5';
			$attrs['style'] = 'background-image: url(' . $slides['img'][0] . ');';
			return $attrs;
		}, 10, 2);
		
		return new WPTemplateResponse($this, 'frontend_image_'.$options['theme']);
	}
	
	public function frontend_post($posts, $options){
		$options = wp_parse_args($options, array(
				'theme' => 'bootstrap',
				'speed' => 800,
				'timeout' => 8000,
				'navcolor' => '#ffffff'
		));
		$qargs = array(
				'post_type'			=> isset($posts['post']['posttype']) ? $posts['post']['posttype'] : 'post',
				'posts_per_page'	=> $posts['post']['limit'],
				'category__in'		=> isset($posts['post']['categories']) ? $posts['post']['categories'] : array(),
				'tag__in'			=> isset($posts['post']['tags']) ? $posts['post']['tags'] : array(),
				'offset'			=> $posts['post']['offset'],
				'order'				=> $posts['post']['order'],
				'orderby'			=> $posts['post']['orderby'],
				/* Set it to false to allow WPML modifying the query. */
				'suppress_filters' => false
		);
		if($posts['post']['postspercolumn'] == 0){
			$posts['post']['postspercolumn'] = 1;
		}
		
		$template = new WPTemplate($this);
		$template->setTemplateDirs(array(get_template_directory().'/', get_stylesheet_directory().'/'));
		$this->assign('template', $template);
		$templateName = $this->filter->apply('cppress_widget_slider_post_template_name',
				'template-parts/' . $posts['post']['posttype'].'-slider', $options);
		$this->assign('templateName', $templateName);
		$this->wpQuery->setLoop($qargs);
		$this->assign('posts', $posts);
		$this->assign('pColumn', $posts['post']['postspercolumn']);
		$this->assign('col', $this->filter->apply('cppress_widget_slider_post_col', array(
				'md' => floor(12/$posts['post']['postspercolumn']),
				'lg' => floor(12/$posts['post']['postspercolumn']),
				'sm' => floor(12/$posts['post']['postspercolumn'])*2
		), $posts, $options));
		$this->assign('indicators', ceil($this->wpQuery->found_posts/$posts['post']['postspercolumn']));
		$this->assign('options', $options);
		$this->assign('filter', $this->filter);
		$this->assign('wpQuery', $this->wpQuery);
		
		return new WPTemplateResponse($this, 'frontend_post_'.$options['theme']);
	}
	
	public function frontend_singlepost($posts, $options){
		$options = wp_parse_args($options, array(
				'theme' => 'bootstrap',
				'speed' => 800,
				'timeout' => 8000,
				'navcolor' => '#ffffff'
		));
		$template = new WPTemplate($this);
		$template->setTemplateDirs(array(get_template_directory().'/', get_stylesheet_directory().'/'));
		$this->assign('template', $template);
		$templateName = $this->filter->apply('cppress_widget_slider_post_template_name',
				'template-parts/' . $posts['title'].'-slider', $options);
		$this->assign('templateName', $templateName);
		$this->wpQuery->setLoop($posts['args']);
		$this->assign('posts', $posts);
		$this->assign('pColumn', 1);
		$this->assign('col', array('md'=> 12, 'sm' => 12, 'lg' => 12));
		$this->assign('indicators', count($posts['countitem']));
		$this->assign('options', $options);
		$this->assign('filter', $this->filter);
		$this->assign('wpQuery', $this->wpQuery);
		
		return new WPTemplateResponse($this, 'frontend_post_'.$options['theme']);
	}
	
}