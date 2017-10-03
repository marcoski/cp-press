<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use \Commonhelp\App\Http\DataResponse;
use CpPress\Application\WP\Admin\PostMeta;
use Commonhelp\App\Http\Http;

class NewsController extends WPController{
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array()){
		parent::__construct($appName, $request, $templateDirs);
	}
	
	public function advanced($instance, $single){
		$this->assign('values', $instance);
		$this->assign('single', $single);
	}
	
}