<?php
namespace CpPress;

use \Commonhelp\WP\WPApplication;
use CpPress\Application\BackEndApplication;
use CpPress\Application\FrontEndApplication;

class CpPress{
	
	public static $App;
	public static $FILE = __FILE__;
	public static $WEBROOT = ABSPATH;
	
	public static function install(){
	}
	
	public static function start(){
		if(is_admin()){
			self::$App = new BackEndApplication();
			self::$App->registerHook('init', function(){
				self::$App->setup();
			});
			self::$App->registerHook('plugins_loaded', function(){
				load_plugin_textdomain('cppress', false, dirname(dirname(dirname(CpPress::$FILE))).'/languages');
			});
			self::$App->execHooks();
		}else{
			self::$App = new FrontEndApplication();
		}
	}
	
}