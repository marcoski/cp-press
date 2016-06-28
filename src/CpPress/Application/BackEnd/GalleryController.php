<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\Application\WP\Theme\Media\Image;
use \Commonhelp\Util\Hash;

class GalleryController extends WPController{
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array()){
		parent::__construct($appName, $request, $templateDirs);
	}
	
	/**
	 * @responder wpjson
	 */
	public function xhr_add($image, $video){
		$this->assign('image', $image);
		$this->assign('video', $video);
		$this->assign('id', $this->getParam( 'id' ));
		$this->assign('name', $this->getParam( 'name' ));
		$this->assign('values', $this->getParam('values', array()));
	}

	
	
	
}