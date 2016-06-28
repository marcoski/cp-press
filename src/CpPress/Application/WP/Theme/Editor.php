<?php
namespace CpPress\Application\WP\Theme;

class Editor{
	
	private $id;
	private $content;
	private $settings;
	
	public function init($id, $content='', $settings=array()){
		$this->id = $id;
		$this->content = $content;
		$this->settings = $settings;
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
	
	
}