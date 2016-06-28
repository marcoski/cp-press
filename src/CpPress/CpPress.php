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
		if(is_admin() && !isset($_POST['_cppress_front_ajax'])){
			self::$App = new BackEndApplication();
			self::$App->registerHook('init', function(){
				self::$App->setup();
			});
			self::$App->registerHook('plugins_loaded', function(){
				load_plugin_textdomain('cppress', false, dirname(dirname(dirname(CpPress::$FILE))).'/languages');
			});
			self::$App->execHooks();
		}else{
			add_filter('show_admin_bar', '__return_false');
			add_action('get_header', function() {
			    remove_action('wp_head', '_admin_bar_bump_cb');
			});
			self::$App = new FrontEndApplication();
			self::$App->registerHooks();
			self::$App->execHooks();
			self::$App->registerFilters();
			self::$App->execFilters();
		}
	}
	
}