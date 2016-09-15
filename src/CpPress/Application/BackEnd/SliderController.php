<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\BackEndApplication;
use CpPress\Application\WP\Theme\Editor;
use CpPress\Application\WP\Theme\Media\Image;
use CpPress\Application\WP\Hook\Filter;
use Commonhelp\WP\WPTemplate;

class SliderController extends WPController{
	
	private $backEndFilter;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $filter){
		parent::__construct($appName, $request, $templateDirs);
		$this->backEndFilter = $filter;
	}
	
	public function image($fields, $values, $imagesRepeater){
		$this->assign('fields', $fields);
		$this->assign('values', $values);
		$this->assign('images', $imagesRepeater);
	}
	
	public function single_post($fields, $values, $postRepeater){
		$this->assign('fields', $fields);
		$this->assign('values', $values);
		$this->assign('post', $postRepeater);
	}
	
	/**
	 * @responder wpjson
	 */
	public function xhr_add($media, $linker, $editor){
		$id = $this->getParam( 'id' );
		$name = $this->getParam('name');
		$count = $this->getParam('count', 0);
		$values = array_merge($this->getParam('values', array()), $this->getParam('widget', array()));
		$this->assign('values', $values);
		$this->assign('linker', $linker);
		$this->assign('media', $media);
		$this->assign('editor', $editor);
		$this->assign('id', $id);
		$this->assign('name', $name);
		$this->assign('filter', $this->backEndFilter);
	}
	
	/**
	 * @responder wpjson
	 */
	public function xhr_add_singlepost($linker){
		$this->assign('linker', $linker);
	}
}