<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\BackEndApplication;
use CpPress\Application\WP\Theme\Editor;
use CpPress\Application\WP\Theme\Media\Image;

class SliderController extends WPController{
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array()){
		parent::__construct($appName, $request, $templateDirs);
	}
	
	public function image($fields, $values, $imagesRepeater){
		$this->assign('fields', $fields);
		$this->assign('values', $values);
		$this->assign('images', $imagesRepeater);
	}
	
	public function parallax($fields, $values, $sentencesRepeater, $media){
		$this->assign('fields', $fields);
		$this->assign('values', $values);
		$this->assign('sentences', $sentencesRepeater);
		$this->assign('media', $media);
	}
	
	/**
	 * @responder wpjson
	 */
	public function xhr_add($media, $linker, $editor){
		$id = $this->getParam( 'id' );
		$name = $this->getParam('name');
		$count = $this->getParam('count', 0);
		$this->assign('values', $this->getParam('values', array()));
		$this->assign('linker', $linker);
		$this->assign('media', $media);
		$this->assign('editor', $editor);
		$this->assign('id', $id);
		$this->assign('name', $name);
	}
	
	/**
	 * @responder wpjson
	 */
	public function xhr_add_parallax(){
		$this->assign('id', $this->getParam( 'id' ));
		$this->assign('name', $this->getParam('name'));
		$this->assign('values', $this->getParam('values', array()));
	}
	
}