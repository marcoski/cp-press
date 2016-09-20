<?php
namespace Commonhelp\App;

use Commonhelp\App\Http\RequestInterface;
use Commonhelp\App\Http\Http;
use Commonhelp\App\Http\Response;
use Commonhelp\App\Http\DataResponse;
use Commonhelp\Util\Annotations\ControllerMethodAnnotations;


class Dispatcher{

	protected $protocol;
	protected $middlewareDispatcher;
	
	/**
	 * 
	 * @var ControllerMethodAnnotations;
	 */
	protected $reflector;
	protected $request;
	
	public function __construct(Http $protocol, MiddlewareDispatcher $middlewareDispatcher, 
			ControllerMethodAnnotations $reflector, RequestInterface $request){
		
		$this->protocol = $protocol;
		$this->middlewareDispatcher = $middlewareDispatcher;
		$this->reflector = $reflector;
		$this->request = $request;
	}
	

	public function dispatch(AbstractController $controller, $methodName){
		$out = array(null, array(), null);
		try{
			$this->reflector->initMethod($controller, $methodName);
			$this->reflector->parse();
			$this->middlewareDispatcher->beforeController($controller, $methodName);
			$response = $this->executeController($controller, $methodName);
		}catch(\Exception $exception){
			$response = $this->middlewareDispatcher->afterException($controller, $methodName, $exception);
			if (is_null($response)) {
				throw $exception;
			}
		}
		$response = $this->middlewareDispatcher->afterController($controller, $methodName, $response);
		$out[0] = $this->protocol->getStatusHeader($response->getStatus(), $response->getLastModified(), $response->getETag());
		$out[1] = array_merge($response->getHeaders());
		$out[2] = $response->getCookies();
		$out[3] = $this->middlewareDispatcher->beforeOutput($controller, $methodName, $response->render());
		$out[4] = $response;
		return $out;
	}
	
	protected function executeController($controller, $methodName) {
		$arguments = array();
		$types = array('int', 'integer', 'bool', 'boolean', 'float');
		foreach($this->reflector->getParameters() as $param => $default) {
			$value = $this->request->getParam($param, $default);
			$type = $this->reflector->getType($param);
			if(($type === 'bool' || $type === 'boolean') &&
					$value === 'false' &&
					(
							$this->request->method === 'GET' ||
							strpos($this->request->getHeader('Content-Type'),
									'application/x-www-form-urlencoded') !== false
					)
			) {
				$value = false;
			} elseif($value !== null && in_array($type, $types)) {
				settype($value, $type);
			}
			$arguments[] = $value;
		}
		$response = call_user_func_array(array($controller, $methodName), $arguments);
		if($response instanceof DataResponse || !($response instanceof Response)) {
			$format = $this->request->getParam('format');
			if($format === null) {
				$headers = $this->request->getHeader('Accept');
				$format = $controller->getResponderByHTTPHeader($headers);
			}
			$response = $controller->buildResponse($response, $format);
		}
		return $response;
	}
	

}