<?php
namespace Commonhelp\App\Routing;

use Commonhelp\App\Http\Request;
use Symfony\Component\Routing\RequestContext as BaseRequestContext;

class RequestContext extends BaseRequestContext{
	
	/**
	 * Updates the RequestContext information based on a HttpFoundation Request.
	 *
	 * @param Request $request A Request instance
	 *
	 * @return RequestContext The current instance, implementing a fluent interface
	 */
	public function load(Request $request){
		$this->setBaseUrl($request->getBaseUrl());
		$this->setPathInfo($request->getPathInfo());
		$this->setMethod($request->getMethod());
		$this->setHost($request->getHost());
		$this->setScheme($request->getScheme());
		$this->setHttpPort($request->isSecure() ? $this->getHttpPort() : $request->getPort());
		$this->setHttpsPort($request->isSecure() ? $request->getPort() : $this->getHttpsPort());
		$this->setQueryString($request->server['QUERY_STRING']);
		return $this;
	}
	
}