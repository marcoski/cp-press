<?php

namespace Commonhelp\Client;

use Commonhelp\Client\Exception;
use Commonhelp\DI\SimpleContainer;
use Commonhelp\Config\Config;

abstract class Client{

	/**
	 * Flag that say if the resource have been modified.
	 *
	 * @var bool
	 */
	private $isModified = true;

	/**
	 * HTTP Content-Type.
	 *
	 * @var string
	 */
	private $contentType = '';

	/**
	 * HTTP encoding.
	 *
	 * @var string
	 */
	private $encoding = '';

	/**
	 * HTTP request headers.
	 *
	 * @var array
	 */
	protected $requestHeaders = array();

	/**
	 * HTTP Etag header.
	 *
	 * @var string
	*/
	protected $etag = '';

	/**
	 * HTTP Last-Modified header.
	 *
	 * @var string
	 */
	protected $lastModified = '';

	/**
	 * Proxy hostname.
	 *
	 * @var string
	 */
	protected $proxyHostname = '';

	/**
	 * Proxy port.
	 *
	 * @var int
	 */
	protected $proxyPort = 3128;

	/**
	 * Proxy username.
	 *
	 * @var string
	 */
	protected $proxyUsername = '';

	/**
	 * Proxy password.
	 *
	 * @var string
	 */
	protected $proxyPassword = '';

	/**
	 * Basic auth username.
	 *
	 * @var string
	 */
	protected $username = '';

	/**
	 * Basic auth password.
	 *
	 * @var string
	 */
	protected $password = '';

	/**
	 * Client connection timeout.
	 *
	 * @var int
	 */
	protected $timeout = 10;

	/**
	 * User-agent.
	 *
	 * @var string
	 */
	protected $userAgent = '';

	/**
	 * Real URL used (can be changed after a HTTP redirect).
	 *
	 * @var string
	 */
	protected $url = '';

	/**
	 * Page/Feed content.
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Number maximum of HTTP redirections to avoid infinite loops.
	 *
	 * @var int
	 */
	protected $maxRedirects = 5;

	/**
	 * Maximum size of the HTTP body response.
	 *
	 * @var int
	 */
	protected $maxBodySize = 2097152; // 2MB

	/**
	 * HTTP response status code.
	 *
	 * @var int
	 */
	protected $statusCode = 0;

	/**
	 * Enables direct passthrough to requesting client.
	 *
	 * @var bool
	 */
	protected $passthrough = false;
	
	

	/**
	 * Do the HTTP request.
	 *
	 * @abstract
	 *
	 * @return array
	 */
	abstract public function doRequest();

	/**
	 * Get client instance: curl or stream driver.
	 *
	 * @static
	 *
	 * @return \PicoFeed\Client\Client
	*/
	public static function getInstance(){
		if (function_exists('curl_init')) {
			return new Curl();
		} elseif (ini_get('allow_url_fopen')) {
			return new Stream();
		}

		throw new ClientException('You must have "allow_url_fopen=1" or curl extension installed');
	}

	/**
	 * Add HTTP Header to the request.
	 *
	 * @param array $headers
	 */
	public function setHeaders($headers)
	{
		$this->requestHeaders = $headers;
	}

	/**
	 * Perform the HTTP request.
	 *
	 * @param string $url URL
	 *
	 * @return Client
	 */
	public function execute($url = ''){
		if ($url !== '') {
			$this->url = $url;
		}

		$response = $this->doRequest();

		$this->statusCode = $response['status'];
		$this->handleNotModifiedResponse($response);
		$this->handleNotFoundResponse($response);
		$this->handleNormalResponse($response);

		return $this;
	}

	/**
	 * Handle not modified response.
	 *
	 * @param array $response Client response
	 */
	public function handleNotModifiedResponse(array $response){
		if ($response['status'] == 304) {
			$this->isModified = false;
		} elseif ($response['status'] == 200) {
			$this->isModified = $this->hasBeenModified($response, $this->etag, $this->lastModified);
			$this->etag = $this->getHeader($response, 'ETag');
			$this->lastModified = $this->getHeader($response, 'Last-Modified');
		}
	}

	/**
	 * Handle not found response.
	 *
	 * @param array $response Client response
	 */
	public function handleNotFoundResponse(array $response){
		if ($response['status'] == 404) {
			throw new InvalidUrlException('Resource not found');
		}
	}

	/**
	 * Handle normal response.
	 *
	 * @param array $response Client response
	 */
	public function handleNormalResponse(array $response){
		if ($response['status'] == 200) {
			$this->content = $response['body'];
			$this->contentType = $this->findContentType($response);
			$this->encoding = $this->findCharset();
		}
	}

	/**
	 * Check if a request has been modified according to the parameters.
	 *
	 * @param array  $response
	 * @param string $etag
	 * @param string $lastModified
	 *
	 * @return bool
	 */
	private function hasBeenModified($response, $etag, $lastModified){
		$headers = array(
				'Etag' => $etag,
				'Last-Modified' => $lastModified,
		);

		// Compare the values for each header that is present
		$presentCacheHeaderCount = 0;
		foreach ($headers as $key => $value) {
			if (isset($response['headers'][$key])) {
				if ($response['headers'][$key] !== $value) {
					return true;
				}
				++$presentCacheHeaderCount;
			}
		}

		// If at least one header is present and the values match, the response
		// was not modified
		if ($presentCacheHeaderCount > 0) {
			return false;
		}

		return true;
	}

	/**
	 * Find content type from response headers.
	 *
	 * @param array $response Client response
	 *
	 * @return string
	 */
	public function findContentType(array $response){
		return strtolower($this->getHeader($response, 'Content-Type'));
	}

	/**
	 * Find charset from response headers.
	 *
	 * @return string
	 */
	public function findCharset(){
		$result = explode('charset=', $this->contentType);

		return isset($result[1]) ? $result[1] : '';
	}

	/**
	 * Get header value from a client response.
	 *
	 * @param array  $response Client response
	 * @param string $header   Header name
	 *
	 * @return string
	 */
	public function getHeader(array $response, $header){
		return isset($response['headers'][$header]) ? $response['headers'][$header] : '';
	}

	/**
	 * Set the Last-Modified HTTP header.
	 *
	 * @param string $lastModified Header value
	 *
	 * @return Client
	 */
	public function setLastModified($lastModified){
		$this->lastModified = $lastModified;

		return $this;
	}

	/**
	 * Get the value of the Last-Modified HTTP header.
	 *
	 * @return string
	 */
	public function getLastModified(){
		return $this->lastModified;
	}

	/**
	 * Set the value of the Etag HTTP header.
	 *
	 * @param string $etag Etag HTTP header value
	 *
	 * @return Client
	 */
	public function setEtag($etag){
		$this->etag = $etag;

		return $this;
	}

	/**
	 * Get the Etag HTTP header value.
	 *
	 * @return string
	 */
	public function getEtag(){
		return $this->etag;
	}

	/**
	 * Get the final url value.
	 *
	 * @return string
	 */
	public function getUrl(){
		return $this->url;
	}

	/**
	 * Set the url.
	 *
	 * @return string
	 * @return Client
	 */
	public function setUrl($url){
		$this->url = $url;

		return $this;
	}

	/**
	 * Get the HTTP response status code.
	 *
	 * @return int
	 */
	public function getStatusCode(){
		return $this->statusCode;
	}

	/**
	 * Get the body of the HTTP response.
	 *
	 * @return string
	 */
	public function getContent(){
		return $this->content;
	}

	/**
	 * Get the content type value from HTTP headers.
	 *
	 * @return string
	 */
	public function getContentType(){
		return $this->contentType;
	}

	/**
	 * Get the encoding value from HTTP headers.
	 *
	 * @return string
	 */
	public function getEncoding(){
		return $this->encoding;
	}

	/**
	 * Return true if the remote resource has changed.
	 *
	 * @return bool
	 */
	public function isModified(){
		return $this->isModified;
	}

	/**
	 * return true if passthrough mode is enabled.
	 *
	 * @return bool
	 */
	public function isPassthroughEnabled(){
		return $this->passthrough;
	}

	/**
	 * Set connection timeout.
	 *
	 * @param int $timeout Connection timeout
	 *
	 * @return Client
	 */
	public function setTimeout($timeout){
		$this->timeout = $timeout ?: $this->timeout;

		return $this;
	}

	/**
	 * Set a custom user agent.
	 *
	 * @param string $userAgent User Agent
	 *
	 * @return Client
	 */
	public function setUserAgent($userAgent){
		$this->userAgent = $userAgent ?: $this->userAgent;

		return $this;
	}

	/**
	 * Set the mximum number of HTTP redirections.
	 *
	 * @param int $max Maximum
	 *
	 * @return Client
	 */
	public function setMaxRedirections($max){
		$this->maxRedirects = $max ?: $this->maxRedirects;

		return $this;
	}

	/**
	 * Set the maximum size of the HTTP body.
	 *
	 * @param int $max Maximum
	 *
	 * @return Client
	 */
	public function setMaxBodySize($max){
		$this->maxBodySize = $max ?: $this->maxBodySize;

		return $this;
	}

	/**
	 * Set the proxy hostname.
	 *
	 * @param string $hostname Proxy hostname
	 *
	 * @return Client
	 */
	public function setProxyHostname($hostname){
		$this->proxyHostname = $hostname ?: $this->proxyHostname;

		return $this;
	}

	/**
	 * Set the proxy port.
	 *
	 * @param int $port Proxy port
	 *
	 * @return Client
	 */
	public function setProxyPort($port){
		$this->proxyPort = $port ?: $this->proxyPort;

		return $this;
	}

	/**
	 * Set the proxy username.
	 *
	 * @param string $username Proxy username
	 *
	 * @return Client
	 */
	public function setProxyUsername($username){
		$this->proxyUsername = $username ?: $this->proxyUsername;

		return $this;
	}

	/**
	 * Set the proxy password.
	 *
	 * @param string $password Password
	 *
	 * @return Client
	 */
	public function setProxyPassword($password){
		$this->proxyPassword = $password ?: $this->proxyPassword;

		return $this;
	}

	/**
	 * Set the username.
	 *
	 * @param string $username Basic Auth username
	 *
	 * @return Client
	 */
	public function setUsername($username){
		$this->username = $username ?: $this->username;

		return $this;
	}

	/**
	 * Set the password.
	 *
	 * @param string $password Basic Auth Password
	 *
	 * @return Client
	 */
	public function setPassword($password){
		$this->password = $password ?: $this->password;

		return $this;
	}

	/**
	 * Enable the passthrough mode.
	 *
	 * @return Client
	 */
	public function enablePassthroughMode(){
		$this->passthrough = true;

		return $this;
	}

	/**
	 * Disable the passthrough mode.
	 *
	 * @return Client
	 */
	public function disablePassthroughMode(){
		$this->passthrough = false;

		return $this;
	}
	
	/**
	 * Set config object.
	 *
	 *
	 * @return Client
	 */
	public function setConfig($config){
		if ($config !== null) {
			$this->setTimeout($config->getClientTimeout());
			$this->setUserAgent($config->getClientUserAgent());
			$this->setMaxRedirections($config->getMaxRedirections());
			$this->setMaxBodySize($config->getMaxBodySize());
			$this->setProxyHostname($config->getProxyHostname());
			$this->setProxyPort($config->getProxyPort());
			$this->setProxyUsername($config->getProxyUsername());
			$this->setProxyPassword($config->getProxyPassword());
		}
		return $this;
	}
}