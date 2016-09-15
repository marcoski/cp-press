<?php
namespace CpPress\Application\WP\Theme;

use CpPress\Application\WP\Hook\Filter;

class Editor{
	
	private $id;
	private $content;
	private $settings;
	
	private $widget;
	
	public function __construct($widget=null){
		$this->widget = $widget;
	}
	
	public function init($id, $content='', $settings=array(), Filter $filter = null){
		$this->id = $id;
		$this->content = $content;
		$this->settings = $settings;
		if(null !== $filter){
			$this->filters($filter);
		}
	}
	
	public function editor(){
		wp_editor($this->content, $this->id, $this->settings);
	}
	
	public function getSettings(){
		return $this->settings;
	}
	
	public function getContent(){
		return $this->content;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function ajax_enqueue(){
		\_WP_Editors::enqueue_scripts();
		print_footer_scripts();
		\_WP_Editors::editor_js();
	}
	
	private function filters(Filter $filter){
		$filter->register('the_editor', function($editor) use ($filter){
			return $filter->apply('cppress_the_editor', $editor, $this->content, $this->settings, $this->widget);
		});
		$filter->register('the_editor_content', function($content, $defaultEditor) use($filter){
			return $filter->apply('cppress_the_editor_content', $content, $defaultEditor, $this->settings, $this->widget);
		}, 9, 2);
		
		$filter->exec('the_editor', true);
		$filter->exec('the_editor_content', true);
	}
	
	
}