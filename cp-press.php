<?php
use CpPress\CpPress;
/*  Copyright 2014  Marco Trognoni  (email : mtrognon@commonhelp.it)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Plugin Name: CpPress Commonhelp App
 * Plugin URI: https://github.com/marcoski/cp-press
 * Description: Commonhelp Plugin App
 * Version: 1.0
 * Author: Marco Trognoni
 * Author URI: http://www.commonhelp.it
 * License: MIT
 */



$filename = __DIR__.'/vendor/autoload.php';
if (!file_exists($filename)) {
	echo 'You must first install the vendors using composer.'.PHP_EOL;
	exit(1);
}

require_once $filename;

require_once 'convenience.php';
require_once 'functions.php';
register_activation_hook(__FILE__, function(){
	CpPress::install();
});
CpPress::start();


?>