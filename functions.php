<?php
/**
 * @copyright    Copyright (C) Copyright (c) 2007 Marco Trognoni. All rights reserved.
 * @license        GNU/GPLv3, see LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

function is_assoc($array){
    return is_array($array) && count($array) !== array_reduce(array_keys($array), 'is_assoc_callback', 0);
}

function is_assoc_callback($a, $b){
    return $a === $b ? $a + 1 : 0;
}




use CpPress\Util\Dumper;
function dump($obj, $limit=10, $die = 1){
	echo Dumper::dump($obj, $limit, true);
	if ($die) exit;
}

function vardump($obj, $limit=10){
	echo Dumper::dump($obj, $limit, true);
}



if(false === function_exists('lcfirst')){
    /**
     * Make a string's first character lowercase
     *
     * @param string $str
     * @return string the resulting string.
     */
    function lcfirst($str){
        $str[0] = strtolower($str[0]);
        return (string)$str;
    }
}

use CpPress\Util\MobileDetect;

$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
$mobble_detect = new MobileDetect();
$mobble_detect->setDetectionType('extended');



/***************************************************************
* Function is_iphone
* Detect the iPhone
***************************************************************/

function is_iphone() {
	global $mobble_detect;
	return($mobble_detect->isIphone());
}

/***************************************************************
* Function is_ipad
* Detect the iPad
***************************************************************/

function is_ipad() {
	global $mobble_detect;
	return($mobble_detect->isIpad());
}

/***************************************************************
* Function is_ipod
* Detect the iPod, most likely the iPod touch
***************************************************************/

function is_ipod() {
	global $mobble_detect;
	return($mobble_detect->is('iPod'));
}

/***************************************************************
* Function is_android
* Detect an android device.
***************************************************************/

function is_android() {
	global $mobble_detect;
	return($mobble_detect->isAndroidOS());
}

/***************************************************************
* Function is_blackberry
* Detect a blackberry device 
***************************************************************/

function is_blackberry() {
	global $mobble_detect;
	return($mobble_detect->isBlackBerry());
}

/***************************************************************
* Function is_opera_mobile
* Detect both Opera Mini and hopefully Opera Mobile as well
***************************************************************/

function is_opera_mobile() {
	global $mobble_detect;
	return($mobble_detect->isOpera());
}

/***************************************************************
* Function is_palm - to be phased out as not using new detect library?
* Detect a webOS device such as Pre and Pixi
***************************************************************/

function is_palm() {
	_deprecated_function('is_palm', '1.2', 'is_webos');
	global $mobble_detect;
	return($mobble_detect->is('webOS'));
}

/***************************************************************
* Function is_webos
* Detect a webOS device such as Pre and Pixi
***************************************************************/

function is_webos() {
	global $mobble_detect;
	return($mobble_detect->is('webOS'));
}

/***************************************************************
* Function is_symbian
* Detect a symbian device, most likely a nokia smartphone
***************************************************************/

function is_symbian() {
	global $mobble_detect;
	return($mobble_detect->is('Symbian'));
}

/***************************************************************
* Function is_windows_mobile
* Detect a windows smartphone
***************************************************************/

function is_windows_mobile() {
	global $mobble_detect;
	return($mobble_detect->is('WindowsMobileOS') || $mobble_detect->is('WindowsPhoneOS'));
}

/***************************************************************
* Function is_lg
* Detect an LG phone
***************************************************************/

function is_lg() {
	_deprecated_function('is_lg', '1.2');
	global $useragent;
	return(preg_match('/LG/i', $useragent));
}

/***************************************************************
* Function is_motorola
* Detect a Motorola phone
***************************************************************/

function is_motorola() {
	global $mobble_detect;
	return($mobble_detect->is('Motorola'));
}

/***************************************************************
* Function is_nokia
* Detect a Nokia phone
***************************************************************/

function is_nokia() {
	_deprecated_function('is_nokia', '1.2');
	global $useragent;
	return(preg_match('/Series60/i', $useragent) || preg_match('/Symbian/i', $useragent) || preg_match('/Nokia/i', $useragent));
}

/***************************************************************
* Function is_samsung
* Detect a Samsung phone
***************************************************************/

function is_samsung() {
	global $mobble_detect;
	return($mobble_detect->is('Samsung'));
}

/***************************************************************
* Function is_samsung_galaxy_tab
* Detect the Galaxy tab
***************************************************************/

function is_samsung_galaxy_tab() {
	_deprecated_function('is_samsung_galaxy_tab', '1.2', 'is_samsung_tablet');
	return is_samsung_tablet();
}

/***************************************************************
* Function is_samsung_tablet
* Detect the Galaxy tab
***************************************************************/

function is_samsung_tablet() {
	global $mobble_detect;
	return($mobble_detect->is('SamsungTablet'));
}

/***************************************************************
* Function is_kindle
* Detect an Amazon kindle
***************************************************************/

function is_kindle() {
	global $mobble_detect;
	return($mobble_detect->is('Kindle'));
}

/***************************************************************
* Function is_sony_ericsson
* Detect a Sony Ericsson
***************************************************************/

function is_sony_ericsson() {
	global $mobble_detect;
	return($mobble_detect->is('Sony'));
}

/***************************************************************
* Function is_nintendo
* Detect a Nintendo DS or DSi
***************************************************************/

function is_nintendo() {
	global $useragent;
	return(preg_match('/Nintendo DSi/i', $useragent) || preg_match('/Nintendo DS/i', $useragent));
}


/***************************************************************
* Function is_smartphone
* Grade of phone A = Smartphone - currently testing this
***************************************************************/

function is_smartphone() {
	return is_iphone() || is_android() || is_samsung() || is_windows_mobile() || is_opera_mobile();
}

/***************************************************************
* Function is_handheld
* Wrapper function for detecting ANY handheld device
***************************************************************/

function is_handheld() {
	return(is_mobile() || is_iphone() || is_ipad() || is_ipod() || is_android() || is_blackberry() || is_opera_mobile() || is_webos() || is_symbian() || is_windows_mobile() || is_motorola() || is_samsung() || is_samsung_tablet() || is_sony_ericsson() || is_nintendo());
}

/***************************************************************
* Function is_mobile
* For detecting ANY mobile phone device
***************************************************************/

function is_mobile() {
	global $mobble_detect;
	if (is_tablet()) return false;
	return ($mobble_detect->isMobile());
}

/***************************************************************
* Function is_ios
* For detecting ANY iOS/Apple device
***************************************************************/

function is_ios() {
	global $mobble_detect;
	return($mobble_detect->isiOS());
}

/***************************************************************
* Function is_tablet
* For detecting tablet devices (needs work)
***************************************************************/

function is_tablet() {
	global $mobble_detect;
	return($mobble_detect->isTablet());
}


?>