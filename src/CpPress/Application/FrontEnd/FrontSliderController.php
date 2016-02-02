<?php
namespace CpPress\Application\FrontEnd;

use \Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Theme\Media\Image;
use Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Query\Query;

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
		$this->setAction('frontend_image_'.$options['theme']);
		for($i=0; $i<count($slides); $i++){
			if(is_numeric($slides[$i]['img'])){
				$image = new Image();
				$image->set($slides[$i]['img']);
				$slideImg['src'] = $image->getImage($slides[$i]['img']);
				$slideImg['title'] = $image->getTitle($slides[$i]['img']);
				$slides[$i]['img'] = $slideImg;
			}else{
				$slides[$i]['img'][0] = $slides[$i]['img']; //uniform array
			}
		}
		$this->assign('options', $options);
		$this->assign('filter', $this->filter);
		$this->assign('slides', $slides);
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
		$this->setAction('frontend_image_'.$options['theme']);
	}
	
	public function frontend_post($posts, $options){
		$options = wp_parse_args($options, array(
				'theme' => 'bootstrap',
				'speed' => 800,
				'timeout' => 8000,
				'navcolor' => '#ffffff'
		));
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
		$this->setAction('frontend_image_'.$options['theme']);
		$this->wpQuery->setLoop($qargs);
		$this->assign('posts', $posts);
		$this->assign('options', $options);
		$this->assign('filter', $this->filter);
		$this->assign('wpQuery', $this->wpQuery);
	}
	
}