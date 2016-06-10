<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use \Commonhelp\App\Http\DataResponse;
use CpPress\Application\WP\Admin\PostMeta;
use Commonhelp\App\Http\Http;

class EventController extends WPController{
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array()){
		parent::__construct($appName, $request, $templateDirs);
	}
	
	public function where($post, $box){
		$event = PostMeta::find($post->ID, 'cp-press-event');
		if($event && isset($event['where'])){
			$this->assign('where', $event['where']);
		}else{
			$this->assign('where', null);
		}
	}
	
	public function advanced($instance, $single){
		$this->assign('values', $instance);
		$this->assign('single', $single);
	}
	
	public function when($post, $box){
		$event = PostMeta::find($post->ID, 'cp-press-event');
		if($event && isset($event['when'])){
			$this->assign('when', $event['when']);
		}else{
			$this->assign('when', null);
		}
	}
	
	public function calendar_taxonomy_form($tags){
		$calendar_color = '#FFFFFF';
		if(isset($tags->term_id)){
			$calendar_color = get_option('category_bgcolor_'.$tags->term_id);
		}
		$this->assign('calendar_color', $calendar_color);
	}
	
	public function calendar_taxonomy_save($term_id, $tt_id){
		if (!$term_id) return new DataResponse(array('not saved'), Http::STATUS_NOT_ACCEPTABLE);
		if(preg_match('/^#[a-zA-Z0-9]{6}$/', $this->getParam('category_bgcolor', ''))){
			add_option('category_bgcolor_'.$term_id, $this->getParam('category_bgcolor'));
		}
		return new DataResponse(array('msg' => 'saved'));
	}
	
	public function calendar_taxonomy_delete($term_id){
		delete_option('category_bgcolor_'.$term_id);
		return new DataResponse(array('msg' => 'deleted'));
	}
	
	public function save($id){
		$event = $this->getParam('cp-press-event', null);
		if($event !== null){
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
				return;
			if($event['when']['event_start_time'] !== ''){
				$startDateStr = $event['when']['event_start_date'].' '.$event['when']['event_start_time'];
				$dtStart = \DateTime::createFromFormat('d/m/Y G:i', $startDateStr);
			}else{
				$startDateStr = $event['when']['event_start_date'];
				$dtStart = \DateTime::createFromFormat('d/m/Y', $startDateStr);
			}
			if($event['when']['event_end_time'] !== ''){
				$endDateStr = $event['when']['event_end_date'].' '.$event['when']['event_end_time'];
				$dtEnd = \DateTime::createFromFormat('d/m/Y G:i', $endDateStr);
			}else{
				$endDateStr = $event['when']['event_end_date'];
				$dtEnd = \DateTime::createFromFormat('d/m/Y', $endDateStr);
			}
			update_post_meta($id, 'cp-press-event-start', $dtStart->getTimestamp());
			update_post_meta($id, 'cp-press-event-end', $dtEnd->getTimestamp());
			update_post_meta($id, 'cp-press-event', $_POST['cp-press-event']);
		}
	}
	
}