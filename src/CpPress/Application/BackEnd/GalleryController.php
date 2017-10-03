<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\WP\Theme\Media\Image;
use \Commonhelp\Util\Hash;
use CpPress\Application\WP\Hook\Filter;

class GalleryController extends WPController{
	
	private $backEndFilter;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $backEndFilter){
		parent::__construct($appName, $request, $templateDirs);
		$this->backEndFilter = $backEndFilter;
	}
	
	/**
	 * @responder wpjson
	 */
	public function xhr_add($image, $video){
		$values = array_merge($this->getParam('values', array()), $this->getParam('widget', array()));
		$this->assign('image', $image);
		$this->assign('video', $video);
		$this->assign('id', $this->getParam( 'id' ));
		$this->assign('name', $this->getParam( 'name' ));
		$this->assign('values', $values);
		$this->assign('filter', $this->backEndFilter);
	}

	
	
	
}