<?php
namespace CpPress\Application\WP\Query;

use wpdb;

class Db extends wpdb{
	protected static $instance = null;
	
	public static function getInstance(){
		if(!self::$instance){
			self::$instance = new static(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
		}
	
		return self::$instance;
	}
}