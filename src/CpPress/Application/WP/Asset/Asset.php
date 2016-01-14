<?php
namespace CpPress\Application\WP\Asset;

interface Asset{
	
	function register($asset, $deps = array(), $ver = false, $extra = false);
	function deregister($asset);
	function enqueue($asset, $deps = array(), $ver = false, $extra = 'false');
	function dequeue($asset);
	function localize($asset, $objectName, $data);
	function inline($asset, $data);
	function is($asset, $list = 'enqueued');
	
}