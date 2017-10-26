<?php
namespace CpPress\Application\FrontEnd;

use \Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\PostMeta;
use Commonhelp\App\Http\DataResponse;

class FrontEventController extends WPController{
	
	private $filter;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $frontEndFilter){
		parent::__construct($appName, $request, $templateDirs);
		$this->filter = $frontEndFilter;
	}
	
	public function when($post){
		$event = PostMeta::find($post->ID, 'cp-press-event');
		if(isset($event['when']['event_start_time']) && $event['when']['event_start_time'] !== ''){
			$startDateStr = $event['when']['event_start_date'].' '.$event['when']['event_start_time'];
			$dtStart = \DateTime::createFromFormat('d/m/Y G:i', $startDateStr);
		}else{
			$startDateStr = $event['when']['event_start_date'];
			$dtStart = \DateTime::createFromFormat('d/m/Y', $startDateStr);
		}
		if(isset($event['when']['event_end_time']) && $event['when']['event_end_time'] !== ''){
			$endDateStr = $event['when']['event_end_date'].' '.$event['when']['event_end_time'];
			$dtEnd = \DateTime::createFromFormat('d/m/Y G:i', $endDateStr);
		}else{
			$endDateStr = $event['when']['event_end_date'];
			$dtEnd = \DateTime::createFromFormat('d/m/Y', $endDateStr);
		}
		return new DataResponse(array(
			'start' => $dtStart,
			'end' => $dtEnd,
		));
	}
	
	public function address($post){
		$event = PostMeta::find($post->ID, 'cp-press-event');
		
		return new DataResponse(array(
			'name' => $event['where']['location_name'],
			'address' => $event['where']['location_address'],
			'town' => $event['where']['location_town']
		));
	}
	
	public function map($post, $options=array()){
		$event = PostMeta::find($post->ID, 'cp-press-event');
		$options = wp_parse_args($options, array(
				'mapheight' 					=> '200',
				'zoom'              	=> 14,
				'scroll_zoom'       	=> 1,
				'draggable'         	=> 1,
				'disable_ui'        	=> 0,
				'keep_centered'     	=> 1,
				'markers'  						=> array(
						array(
								'place' => $event['where']['location_address'] . ', ' . $event['where']['location_town'],
								'content' => $event['where']['location_name']
						)
				),
				'map_styles'        	=> '',
			)
		);
		$this->assign('mapdata', json_encode(
				array(
						'map_name' => $event['where']['location_name'],
						'location' => array(
							'lat' => $event['where']['location_latitude'],
							'lng' => $event['where']['location_longitude']
						),
						'zoom' => $options['zoom'],
						'scroll_zoom' => $options['scroll_zoom'],
						'draggable' => $options['draggable'],
						'disable_ui' => $options['disable_ui'],
						'markers' => $options['markers'],
						'map_styles' => $options['map_styles']
				)
		));
		$this->assign('mapid', md5($event['where']['location_latitude'].$event['where']['location_longitude']));
		$this->assign('filter', $this->filter);
		$this->assign('options', $options);
	}
	
	
}