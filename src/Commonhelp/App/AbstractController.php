<?php
namespace Commonhelp\App;
use Commonhelp\App\Http\Request;
use Commonhelp\App\Http\RequestInterface;
use Commonhelp\App\Http\DataResponse;
use Commonhelp\App\Http\JsonResponse;
use Commonhelp\App\Http\Commonhelp\App\Http;
use Commonhelp\App\Exception\RenderException;
use Commonhelp\App\Http\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Commonhelp\App\Http\TemplateResponse;
use Commonhelp\App\Template\Php\PhpTemplate;
use Commonhelp\DI\ContainerInterfaceTrait;
use Commonhelp\DI\ContainerInterfaceTraitInterface;
use Commonhelp\Form\FormCreator;
use Commonhelp\App\Template\TemplateInterface;

abstract class AbstractController implements ContainerInterfaceTraitInterface{
	
	use ContainerInterfaceTrait;
	
	protected $appName;
	
	protected $request;
	
	protected $responders = array();

	protected $vars;
	
	protected $templateDirs;
	
	public function __construct($appName, RequestInterface $request){
		$this->appName = $appName;
		$this->request = $request;
		$this->vars = array();
		$this->templateDirs = array();
	}
	
	public function getAppName(){
		return $this->appName;
	}
	
	public function setAppName($app){
		$this->appName = $app;
	}
	
	public function generateUrl($route, $parameters=array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_URL){
		if($this->container !== null){
			return $this->get('router')->generate($route, $parameters, $referenceType);
		}
		
		return null;
	}
	
	/**
	 * Parses an HTTP accept header and returns the supported responder type
	 * @param string $acceptHeader
	 * @return string the responder type
	 */
	public function getResponderByHTTPHeader($acceptHeader) {
		$headers = explode(',', $acceptHeader);
		// return the first matching responder
		foreach ($headers as $header) {
			$header = strtolower(trim($header));
			$responder = str_replace('application/', '', $header);
			if (array_key_exists($responder, $this->responders)) {
				return $responder;
			}
		}
		// no matching header defaults to json
		return 'json';
	}
	
	/**
	 * Serializes and formats a response
	 * @param mixed $response the value that was returned from a controller and
	 * is not a Response instance
	 * @param string $format the format for which a formatter has been registered
	 * @throws \DomainException if format does not match a registered formatter
	 * @return Response
	 */
	public function buildResponse($response, $format='json') {
		if(array_key_exists($format, $this->responders)) {
			$responder = $this->responders[$format];
			return $responder($response);
		} else {
			throw new \DomainException('No responder registered for format ' .
					$format . '!');
		}
	}
	
	
	public function registerResponder($responder, \Closure $closure){
		$this->responders[$responder] = $closure;
	}
	
	public function hasResponder($responder){
		return array_key_exists($responder, $this->responders);
	}
	
	public function render($name, array $parameters=array(), array $headers=array()){
		if($this->container === null){
			return null;
		}
		$parameters = array_replace($this->vars, $parameters);
		if($this->has('Engine')){
			$engine = $this->get('Engine');
		}else{
			$engine = $this->get(TemplateInterface::class);
		}
		$engine->setApplication($this->appName);
		$engine->setTemplate($name);
		$response = new TemplateResponse($engine, array_replace($this->vars, $parameters));
		foreach($headers as $name => $value){
			$response->addHeader($name, $value);
		}
		
		return $response;
	}
	
	public function redirect($url){
		return new RedirectResponse($url);
	}
	
	public function redirectToRoute($route, $parameters=array()){
		if(null !== $url = $this->generateUrl($route, $parameters)){
			return $this->redirect($url);
		}
		
		return null;
	}
	
	public function assign($key, $value) {
		$this->vars[$key] = $value;
		return true;
	}
	
	public function getVars(){
		return $this->vars;
	}
	
	public function get($service){
		return $this->container->get($service);
	}
	
	public function has($service){
		return $this->container->has($service);
	}
	
	/**
	 * Creates and returns a FormCreator instance from the type of the form.
	 *
	 * @param string $factory    The fully qualified class name of the form factory
	 * @param mixed  $data    The initial data for the form
	 * @param array  $options Options for the form
	 *
	 * @return FormCreator
	 */
	protected function createForm($factory, $data=null, $options=array()){
		return $this->get(FormCreator::class)->create($factory, $data, $options);
	}
	
	/**
	 * Creates and returns a form creator instance.
	 *
	 * @param mixed $data    The initial data for the form
	 * @param array $options Options for the form
	 *
	 * @return FormCreator
	 */
	protected function createFormBuilder($data = null, array $options = array()){
		return $this->get(FormCreator::class)->create(null, $data, $options);
	}
	
	
	
	
	public function getParams() {
		return $this->request->getParams();
	}
	
	public function getParam($key, $default=null){
		return $this->request->getParam($key, $default);
	}
	
	public function method() {
		return $this->request->getMethod();
	}
	
	public function getUploadedFile($key) {
		return $this->request->getUploadedFile($key);
	}
	
	public function env($key) {
		return $this->request->getEnv($key);
	}
	
	public function cookie($key) {
		return $this->request->getCookie($key);
	}
	
	public function setTemplateDirs($resource){
		if($this->container !== null){
			$locator = $this->get('locator');
			$this->templateDirs = $locator->findResources($resource);
		}else{
			$this->templateDirs = array();
		}
	}
	
	public function getTemplateDirs(){
		return $this->templateDirs;
	}
	
}