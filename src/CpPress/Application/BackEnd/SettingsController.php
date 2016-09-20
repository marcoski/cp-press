<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\Settings;
use CpPress\Application\WP\MetaType\PostType;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionFactoryInterface;

class SettingsController extends WPController{
	
	/**
	 * @var Settings
	 */
	private $settings;
	
	private $themeUri;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), $themeUri, Settings $settings){
		parent::__construct($appName, $request, $templateDirs);
		$this->settings = $settings;
		$this->themeUri = $themeUri;
	}
	
	public function main(){
		$this->assign('_settings', $this->settings);
		$this->assign('_sectionSettingsFactory', $this->settings->getSettingsSectionFactory());
		$this->assign('tab', $this->getParam('tab', 'cppress-options-general'));
	}
	
}