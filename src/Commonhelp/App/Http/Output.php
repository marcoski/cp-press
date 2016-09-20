<?php
namespace Commonhelp\App\Http;

class Output implements OutputInterface{
	
	/** @var string */
	private $webRoot;
	
	/**
	 * @param $webRoot
	 */
	public function __construct($webRoot) {
		$this->webRoot = $webRoot;
	}
	
	/**
	 * @param string $out
	 */
	public function setOutput($out) {
		print($out);
	}
	
	/**
	 * @param string $path
	 *
	 * @return bool false if an error occured
	 */
	public function setReadfile($path) {
		return @readfile($path);
	}
	
	/**
	 * @param string $header
	 */
	public function setHeader($header) {
		header($header);
	}
	
	/**
	 * @param int $code sets the http status code
	 */
	public function setHttpResponseCode($code) {
		http_response_code($code);
	}
	
	/**
	 * @return int returns the current http response code
	 */
	public function getHttpResponseCode() {
		return http_response_code();
	}
	
	/**
	 * @param string $name
	 * @param string $value
	 * @param int $expire
	 * @param string $path
	 * @param string $domain
	 * @param bool $secure
	 * @param bool $httpOnly
	 */
	public function setCookie($name, $value, $expire, $path, $domain, $secure, $httpOnly) {
		$path = $this->webRoot ? : '/';
		setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
	}
	
}