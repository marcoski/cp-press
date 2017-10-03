<?php
namespace Commonhelp\App\Template\Php;

use Commonhelp\App\Template\TemplateBase;
use Commonhelp\App\AbstractController;
use Commonhelp\Util\Inflector;
use Commonhelp\App\Exception\TemplateNotFoundException;
use Commonhelp\App\Template\Helper\HelperInterface;
use Commonhelp\App\Template\TemplateFileLocator;

class PhpTemplate extends TemplateBase implements \ArrayAccess{
	
	protected $cache = array();
	protected $dirs = array();
	protected $app;
	
	protected $helpers = array();
	protected $parents = array();
	protected $stack = array();
	
	protected $current = null;
	protected $evalTemplate;
	protected $evalParameters;
	
	protected $escapers;
	protected static $escapersCache = array();
	
	protected $charset = 'UTF-8';
	
	public function __construct($dirs, $helpers = array()){
		$this->helpers = $helpers;
		$this->dirs = $dirs;
	}
	
	public function setApplication($app){
		$this->app = $app;
	}
	
	public function render(){
		$template = $this->findTemplate($this->template);
		$parameters = $this->vars;
		return $this->renderTemplate($template, $parameters);
	}
	
	public function inc($template, $additionalParams){
		if(is_array($this->vars)){
			$parameters = array_replace($this->vars, $additionalParams);
		}else{
			$parameters = $additionalParams;
		}
		$template = $this->findTemplate($template);
		return $this->renderTemplate($template, $parameters);
	}
	
	private function renderTemplate($template, $parameters){
		$key = hash('sha256', serialize($template));
		$this->current = $key;
		$template = $template[0];
		$this->parents[$key] = null;
		$parent = null;
		if(false === $content = $this->evaluate($template, $parameters)){
			throw new \RuntimeException(sprintf('Template "%s" cannot be rendered', $template));
		}
	
		
		//decorate
		if($this->parents[$key]){
			$slots = $this->get('slots');
			$this->stack[] = $slots->get('_content');
			$slots->set('_content', $content);
			if(!isset($this->parents[$key]['application'])){
				$parent = ($this->findTemplate($this->parents[$key]['template']));
			}else{
				$parent = ($this->findTemplate($this->parents[$key]['template'], $this->parents[$key]['application']));
			}
			$content = $this->renderTemplate($parent, $parameters);
				
			$slots->set('_content', array_pop($this->stack));
		}
		
		return $content;
	}
	
	public function evaluate($template, $parameters){
		$this->evalTemplate = $template;
		$this->evalParameters = $parameters;
		unset($template, $parameters);
		
		if(isset($this->evalParameters['this'])){
			throw new \InvalidArgumentException('Invalid parameter (this)');
		}
		if(isset($this->evalParameters['view'])){
			throw new \InvalidArgumentException('Invalid parameter (view)');
		}
		
		$view = $this;
		extract($this->evalParameters, EXTR_SKIP);
		$this->evalParameters = null;
		ob_start();
		require $this->evalTemplate;
		
		$this->evalTemplate = null;
		
		return ob_get_clean();
	}
	
	protected function findTemplate($name, $app=''){
		if($app === ''){
			$app = $this->app;
		}
		if(isset($this->cache[$name])){
			return $this->cache[$name];
		}
		$tDirs = array();
		foreach($this->dirs as $dir){
			$tDirs[] = $dir . DIRECTORY_SEPARATOR . Inflector::underscore($app) . DIRECTORY_SEPARATOR;
		}
		$locator = new TemplateFileLocator($tDirs, '.html.php');
		
		return array(
			$locator->find($name),
			$locator->getPath()	
		);
	}
	
	public function exists($name){
		try{
			$this->findTemplate($name);
		}catch(TemplateNotFoundException $e){
			return false;
		}
		
		return true;
	}
	
	public function offsetExists($name){
		return $this->has($name);
	}
	
	public function get($name){
		if(!isset($this->helpers[$name])){
			throw new \InvalidArgumentException(sprintf('The helper "%s" is note defined', $name));
		}
		
		return $this->helpers[$name];
	}
	
	public function offsetGet($name){
		return $this->get($name);
	}
	
	public function setHelpers(array $helpers){
		$this->helpers = $helpers;
	}
	
	public function set(HelperInterface $helper, $alias = null){
		$this->helpers[$helper->getName()] = $helper;
		if(null !== $alias){
			$this->helpers[$alias] = $helper;
		}
		
		$helper->setCharset($this->charset);
	}
	
	public function offsetSet($name, $value){
		$this->set($name, $value);
	}
	
	public function offsetUnset($name){
		throw new \LogicException('Could not unset helper');
	}
	
	public function has($name){
		return isset($this->helpers[$name]);
	}
	
	public function extend($template, $application=''){
		if($application == ''){
			$this->parents[$this->current] = array(
				'template' => $template
			);
		}else{
			$this->parents[$this->current] = array(
				'template' => $template,
				'application' => $application	
			);
		}
	}
	
	public function getCharset(){
		return $this->charset;
	}
	
	public function setCharset($charset){
		if('UTF8' === $charset = strtoupper($charset)){
			$charset = 'UTF-8';
		}
		$this->charset = $charset;
		foreach($this->helpers as $helper){
			$helper->setCharset($charset);
		}
	}
	
	public function setEscaper($context, callable $escaper){
		$this->escapers[$context] = $escaper;
		self::$escapersCache[$context] = array();
	}
	
	public function getEscaper($context){
		if(!isset($this->escapers[$context])){
			throw new \InvalidArgumentException(sprintf('No registered escaper for context "%s"', $context));
		}
		
		return $this->escapers[$context];
	}
	
	public function escape($value, $context = 'html'){
		if(is_numeric($value)){
			return $value;
		}
		if(is_scalar($value)){
			if(!isset(self::$escapersCache[$context][$value])){
				self::$escapersCache[$context][$value] = call_user_func($this->getEscaper($context), $value);
 			}
 			
 			return self::$escapersCache[$context][$value];
		}
		
		return call_user_func($this->getEscaper($context), $value);
	}
	
	protected function initEscapers(){
		$flags = ENT_QUOTES | ENT_SUBSTITUTE;
		$this->escapers = array(
			'html' =>
			/**
					 * Runs the PHP function htmlspecialchars on the value passed.
			*
			* @param string $value the value to escape
			*
			* @return string the escaped value
			*/
			function ($value) use ($flags){
				// Numbers and Boolean values get turned into strings which can cause problems
				// with type comparisons (e.g. === or is_int() etc).
				return is_string($value) ? htmlspecialchars($value, $flags, $this->getCharset(), false) : $value;
			},
			
			'js' =>
			/**
			 * A function that escape all non-alphanumeric characters
			* into their \xHH or \uHHHH representations.
			*
			* @param string $value the value to escape
			*
			* @return string the escaped value
			*/
			function ($value){
				if('UTF-8' != $this->getCharset()){
					$value = iconv($this->getCharset(), 'UTF-8', $value);
				}
				$callback = function ($matches) {
					$char = $matches[0];
					// \xHH
					if(!isset($char[1])){
						return '\\x'.substr('00'.bin2hex($char), -2);
					}
					// \uHHHH
					$char = iconv('UTF-8', 'UTF-16BE', $char);
					return '\\u'.substr('0000'.bin2hex($char), -4);
				};
				if(null === $value = preg_replace_callback('#[^\p{L}\p{N} ]#u', $callback, $value)){
					throw new \InvalidArgumentException('The string to escape is not a valid UTF-8 string.');
				}
				if('UTF-8' != $this->getCharset()){
					$value = iconv('UTF-8', $this->getCharset(), $value);
				}
				return $value;
			},
		);
		self::$escaperCache = array();
	}
	
	public static function renderError(AbstractController $controller, $error_msg){
	}
	
	public static function renderException(AbstractController $controller, \Exception $exception){
	}
	
}