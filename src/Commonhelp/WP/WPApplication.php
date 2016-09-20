<?php
namespace Commonhelp\WP;

use Commonhelp\App\AbstractApplication;
use Commonhelp\WP\Exception\WPControllerNotFoundException;
use Commonhelp\App\Http\JsonResponse;
use Commonhelp\DI\ContainerInterface;

abstract class WPApplication extends AbstractApplication{

	public static $APPPATH = '';
	
	protected $themeRoot;
	protected $childRoot;
	protected $themeUri;
	protected $childUri;
	
	public function __construct($appName, $urlParams = array(), array $themePathUri){
		if(isset($themePathUri['main'])){
			$this->themeRoot = isset($themePathUri['main']['root']) ? $themePathUri['main']['root'] : null;
			$this->themeUri = isset($themePathUri['main']['uri']) ? $themePathUri['main']['uri'] : null;
		}
		if(isset($themePathUri['child'])){
			$this->childRoot = isset($themePathUri['child']['root']) ? $themePathUri['child']['root'] : null;
			$this->childUri = isset($themePathUri['child']['uri']) ? $themePathUri['child']['uri'] : null;
		}
		$this->container = new WPContainer($appName, $urlParams, $this->themeRoot, $this->childRoot);
	}
	
	public function getChildRoot(){
		return $this->childRoot;
	}
	
	public function getThemeRoot(){
		return $this->themeRoot;
	}
	
	public function getThemeUri(){
		return $this->themeUri;
	}
	
	public function getChildUri(){
		return $this->childUri;
	}
	
	public static function main($controllerName, $methodName, ContainerInterface $container, array $urlParams = null){
		if (!is_null($urlParams)) {
			$container['Request']->setUrlParameters($urlParams);
		} else if (isset($container['urlParams']) && !is_null($container['urlParams'])) {
			$container['Request']->setUrlParameters($container['urlParams']);
		}
		$appName = $container['appName'];
		try {
			$controller = $container->query($controllerName);
		} catch(QueryException $e) {
			throw new WPControllerNotFoundException($controllerName.' is not registered');
		}
		// initialize the dispatcher and run all the middleware before the controller
		$dispatcher = $container['WPDispatcher'];
		list(
			$httpHeaders,
			$responseHeaders,
			$responseCookies,
			$output,
			$response
		) = $dispatcher->dispatch($controller, $methodName);
		$io = $container['Output'];
		if ($response instanceof ICallbackResponse) {
			$response->callback($io);
		} else if($response instanceof JsonResponse){
			foreach($responseHeaders as $name => $value){
				$io->setHeader($name .':'. $value);
			}
			$io->setOutput($output);
			exit;
		} else if(!is_null($output)) {
			$io->setOutput($output);
		}
	}
	
	public static function part($controllerName, $methodName, ContainerInterface $container, array $urlParams = null){
		if (!is_null($urlParams)) {
			$container['Request']->setUrlParameters($urlParams);
		} else if (isset($container['urlParams']) && !is_null($container['urlParams'])) {
			$container['Request']->setUrlParameters($container['urlParams']);
		}
		$controller = $container[$controllerName];
		$dispatcher = $container['WPDispatcher'];
		list(, , , $output) =  $dispatcher->dispatch($controller, $methodName);
		return $output;
	}
	

}