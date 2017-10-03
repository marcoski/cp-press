<?php
namespace Commonhelp\App\Http;

interface OutputInterface{
	
	/**
	 * @param string $out
	 */
	public function setOutput($out);
	
	/**
	 * @param string $path
	 *
	 * @return bool false if an error occured
	*/
	public function setReadfile($path);
	
	/**
	 * @param string $header
	*/
	public function setHeader($header);
	
	/**
	 * @return int returns the current http response code
	*/
	public function getHttpResponseCode();
	
	/**
	 * @param int $code sets the http status code
	*/
	public function setHttpResponseCode($code);
	
	/**
	 * @param string $name
	 * @param string $value
	 * @param int $expire
	 * @param string $path
	 * @param string $domain
	 * @param bool $secure
	 * @param bool $httpOnly
	*/
	public function setCookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
}