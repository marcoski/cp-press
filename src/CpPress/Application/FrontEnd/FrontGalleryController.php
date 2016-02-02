<?php
namespace CpPress\Application\FrontEnd;

use \Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use Commonhelp\App\Http\RequestInterface;

class FrontGalleryController extends WPController{
	
	private $filter;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $frontEndFilter){
		parent::__construct($appName, $request, $templateDirs);
		$this->filter = $frontEndFilter;
	}
	
	public function carousel($gallery, $options){
		$this->assign('gallery', $gallery);
		$this->assign('options', $options);
	}
	
	public function glist($gallery, $options){
		$this->assign('gallery', $gallery);
		$this->assign('options', $options);
	}
	
	
}