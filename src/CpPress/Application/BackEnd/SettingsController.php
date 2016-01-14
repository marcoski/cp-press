<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\Settings;
use CpPress\Application\WP\MetaType\PostType;

class SettingsController extends WPController{
	
	private $settings;
	private $themeUri;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), $themeUri, Settings $settings){
		parent::__construct($appName, $request, $templateDirs);
		$this->settings = $settings;
		$this->themeUri = $themeUri;
		$this->assign('_settings', $this->settings);
	}
	
	public function main(){
	}
	
	public function general(){
		$this->assign('root', $this->themeUri.'/assets');
	}
	
	public function gallery(){
	}
	
	public function event(){
	}
	
	public function slider(){
	}
	
	public function portfolio(){
		$this->assign('post_types', 
			array_diff(
				PostType::getPostTypes(),
				PostType::getPostTypes(array('_builtin' => true)), array('portfolio')
			)
		);
	}
	
}