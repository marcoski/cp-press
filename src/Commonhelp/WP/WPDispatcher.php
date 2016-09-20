<?php
namespace Commonhelp\WP;

use Commonhelp\App\Dispatcher;
use Commonhelp\WP\WPController;
use Commonhelp\App\Exception\TemplateNotFoundException;
use Commonhelp\WP\WPTemplate;
use Commonhelp\App\Http\JsonResponse;
use Commonhelp\WP\Exception\WPResponderNotFoundException;
use Commonhelp\App\Http\DataResponse;
use Commonhelp\App\Http\Response;

class WPDispatcher extends Dispatcher{
	
	protected $responders;
	
	protected function executeController(WPController $controller, $methodName) {
		$arguments = $this->request->getUrlParams();
		$responder = $this->reflector->getResponder();
		$response = call_user_func_array(array($controller, $methodName), $arguments);
		if(is_null($response)){
			if($responder === null){
				$controller->setAction($methodName);
				$response = $controller->render($response, 'template');
			}else{
				if($controller->hasResponder($responder)){
					$controller->setAction($methodName);
					$response = $controller->render($response, $responder);
				}else{
					throw new WPResponderNotFoundException('Responder: '.$responder.' not register for '
							.get_class($controller));
				}
			}
		}else if(is_array($response)){
			$response = new JsonResponse($response);
		}else if($responder === 'string'){
			return new DataResponse($response);
		}else if($response instanceof Response){
			return $response;
		}else{
			$data = array('data' => $response);
			$response = new JsonResponse($data);
		}
		return $response;
	}
}