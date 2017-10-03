<?php
namespace Commonhelp\App;

use Commonhelp\Config\Config;
/**
*/
class SystemConfig extends Config{
	
	protected $writeable = false;
	
	protected $validMethodList = array(
		'getSecret',
		'setSecret',
		'getHashingcost',
		'setHashingcost',
		'getPasswordsalt',
		'setPasswordsalt',
		'getTrusteddomains',
		'setTrusteddomains',
		'getTrustedproxies',
		'setTrustedproxies',
		'getOverwritehost',
		'setOverwritehost',
		'getOverwriteprotocol',
		'setOverwriteprotocol',
		'getOverwritewebroot',
		'setOverwritewebroot',
		'getOverwritecondaddr',
		'setOverwritecondaddr',
		'getForwardedforheaders',
		'setForwardedforheaders',
		'getDbname',
		'setDbname',
		'getDbusername',
		'setDbusername',
		'getDbpassword',
		'setDbpassword',
		'setDbdriver',
		'getDbdriver',
		'getSystemvalue',
		'setSystemvalue',
		'getInstalled',
		'setInstalled',
		'getEngine',
		'setEngine'
	);
	
	public function write(){
		return null;
	}
	
}