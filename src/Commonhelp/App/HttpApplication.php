<?php
namespace Commonhelp\App;

use Commonhelp\App\Exception\ControllerNotFoundException;
use Commonhelp\App\Http\CallbackResponse;
use Commonhelp\DI\ContainerInterface;
use Commonhelp\DI\Exception\QueryException;

class HttpApplication extends AbstractApplication{
	
	protected $appName;
	protected $webRoot;
	
	public function __construct($appName, $webRoot, ContainerInterface $container){
		$this->appName = $appName;
		$this->container = $container;
		$this->webRoot = $webRoot;
	}
	
	public static function main($controllerName, $methodName, ContainerInterface $container, array $urlParams = null){
		if(!is_null($urlParams)){
			$container->get('Request')->setUrlParameters($urlParams);
		} else if ($container->has('urlParams') && is_array($container->get('urlParams'))){
			$container->get('Request')->setUrlParameters($container->get('urlParams'));
		}
		try {
			$controller = $container->get($controllerName);
		} catch(QueryException $e) {
			throw new ControllerNotFoundException($controllerName.' is not registered');
		}
		// initialize the dispatcher and run all the middleware before the controller
		$dispatcher = $container->get(Dispatcher::class);
		list(
				$httpHeaders,
				$responseHeaders,
				$responseCookies,
				$output,
				$response
		) = $dispatcher->dispatch($controller, $methodName);
		$io = $container->get('Output');
		if(!is_null($httpHeaders)) {
			$io->setHeader($httpHeaders);
		}
		foreach($responseHeaders as $name => $value) {
			$io->setHeader($name . ': ' . $value);
		}
		foreach($responseCookies as $name => $value) {
			$expireDate = null;
			if($value['expireDate'] instanceof \DateTime) {
				$expireDate = $value['expireDate']->getTimestamp();
			}
			$io->setCookie(
					$name,
					$value['value'],
					$expireDate,
					$this->webRoot,
					null,
					$container->get('Request')->getServerProtocol() === 'https',
					true
			);
		}
		if ($response instanceof CallbackResponse) {
			$response->callback($io);
		} else if(!is_null($output)) {
			$io->setHeader('Content-Length: ' . strlen($output));
			$io->setOutput($output);
		}
	}
	
	public static function part($controllerName, $methodName, ContainerInterface $container, array $urlParams = null){
		if (!is_null($urlParams)) {
			$container->get('Request')->setUrlParameters($urlParams);
		} else if ($container->has('urlParams') && is_array($container->get('urlParams'))) {
			$container->get('Request')->setUrlParameters($container->get('urlParams'));
		}
		$controller = $container->get($controllerName);
		$dispatcher = $container->get(Dispatcher::class);
		list(, , $output) =  $dispatcher->dispatch($controller, $methodName);
		return $output;
	}
	
}