<?php
namespace Commonhelp\App\Http;

interface RequestInterface{
	
	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function getHeader($name);
	
	/**
	 * Lets you access post and get parameters by the index
	 * In case of json requests the encoded json body is accessed
	 *
	 * @param string $key the key which you want to access in the URL Parameter
	 *                     placeholder, $_POST or $_GET array.
	 *                     The priority how they're returned is the following:
	 *                     1. URL parameters
	 *                     2. POST parameters
	 *                     3. GET parameters
	 * @param mixed $default If the key is not found, this value will be returned
	 * @return mixed the content of the array
	*/
	public function getParam($key, $default = null);
	
	/**
	 * Returns all params that were received, be it from the request
	 *
	 * (as GET or POST) or through the URL by the route
	 * @return array the array with all parameters
	*/
	public function getParams();
	
	/**
	 * Returns the method of the request
	 *
	 * @return string the method of the request (POST, GET, etc)
	*/
	public function getMethod();
	
	/**
	 * Shortcut for accessing an uploaded file through the $_FILES array
	 *
	 * @param string $key the key that will be taken from the $_FILES array
	 * @return array the file in the $_FILES element
	*/
	public function getUploadedFile($key);
	
	/**
	 * Shortcut for getting env variables
	 *
	 * @param string $key the key that will be taken from the $_ENV array
	 * @return array the value in the $_ENV element
	*/
	public function getEnv($key);
	
	/**
	 * Shortcut for getting cookie variables
	 *
	 * @param string $key the key that will be taken from the $_COOKIE array
	 * @return array the value in the $_COOKIE element
	*/
	public function getCookie($key);
	
	/**
	 * Checks if the CSRF check was correct
	 * @return bool true if CSRF check passed
	*/
	public function passesCSRFCheck();
	
	/**
	 * Returns an ID for the request, value is not guaranteed to be unique and is mostly meant for logging
	 * If `mod_unique_id` is installed this value will be taken.
	 * @return string
	*/
	public function getId();
	
	/**
	 * Returns the remote address, if the connection came from a trusted proxy
	 * and `forwarded_for_headers` has been configured then the IP address
	 * specified in this header will be returned instead.
	 * Do always use this instead of $_SERVER['REMOTE_ADDR']
	 * @return string IP address
	*/
	public function getRemoteAddress();
	
	/**
	 * Returns the server protocol. It respects reverse proxy servers and load
	 * balancers.
	 * @return string Server protocol (http or https)
	*/
	public function getServerProtocol();
	
	/**
	 * Returns the used HTTP protocol.
	 *
	 * @return string HTTP protocol. HTTP/2, HTTP/1.1 or HTTP/1.0.
	*/
	public function getHttpProtocol();
	
	/**
	 * Returns the request uri, even if the website uses one or more
	 * reverse proxies
	 * @return string
	*/
	public function getRequestUri();
	
	/**
	 * Get raw PathInfo from request (not urldecoded)
	 * @throws \Exception
	 * @return string Path info
	*/
	public function getRawPathInfo();
	
	/**
	 * Get PathInfo from request
	 * @throws \Exception
	 * @return string|false Path info or false when not found
	*/
	public function getPathInfo();
	
	/**
	 * Returns the script name, even if the website uses one or more
	 * reverse proxies
	 * @return string the script name
	*/
	public function getScriptName();
	
	/**
	 * Checks whether the user agent matches a given regex
	 * @param array $agent array of agent names
	 * @return bool true if at least one of the given agent matches, false otherwise
	*/
	public function isUserAgent(array $agent);
	
	/**
	 * Returns the unverified server host from the headers without checking
	 * whether it is a trusted domain
	 * @return string Server host
	*/
	public function getInsecureServerHost();
	
	/**
	 * Returns the server host from the headers, or the first configured
	 * trusted domain if the host isn't in the trusted list
	 * @return string Server host
	*/
	public function getServerHost();
}