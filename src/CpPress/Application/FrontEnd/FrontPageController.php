<?php
namespace CpPress\Application\FrontEnd;

use \Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use Commonhelp\App\Http\RequestInterface;

class FrontPageController extends WPController{
	
	private $filter;
	private $widgets;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $frontEndFilter, $widgets){
		parent::__construct($appName, $request, $templateDirs);
		$this->filter = $frontEndFilter;
		$this->widgets = $widgets;
	}
	
	public function layout($post, $layout){
		$layout_data = $this->filter->apply( 'cppress_layout_data', $layout, $post->ID );
		if(empty($layout_data) || empty($layout_data['sections'])){
			return null; /* HANDLE WP ERROR EXCEPTION */
		}
	
		$sections = array();
		foreach($layout_data['sections'] as $skey => $section){
			$skey = intval($skey);
			$sections[$skey] = array(
				'data' => $section
			);
		}
		
		foreach($layout_data['grids'] as $gkey => $grid){
			$gkey = intval($gkey);
			$sections[$grid['section']][] = array(
				'data' => $grid
			);
		}
		
		foreach($layout_data['cells'] as $ckey => $cell){
			$ckey = intval($ckey);
			$sections[$cell['section']][$cell['grid']][] = array(
					'data' => $cell
			);
		}
		
		foreach($layout_data['widgets'] as $wkey => $widget){
			$sections
			[ intval($widget['widget_info']['section']) ]
			[ intval($widget['widget_info']['grid']) ]
			[ intval($widget['widget_info']['cell']) ][] = $widget;
		}
		$this->assign('sections', $sections);
		$this->assign('layout_data', $layout_data);
		$this->assign('filter', $this->filter);
		$this->assign('widgetsFactory', $this->widgets);
		$this->assign('post', $post);
	}
	
}