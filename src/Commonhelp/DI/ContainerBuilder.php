<?php
namespace Commonhelp\DI;

use Doctrine\Common\Cache\Cache;
use Commonhelp\DI\Definition\Source\AnnotationReader;
use Commonhelp\DI\Definition\Source\Autowiring;
use Commonhelp\DI\Definition\Source\SourceChain;
use Commonhelp\DI\Definition\Source\CachedDefinitionSource;
use Commonhelp\DI\Definition\Source\DefinitionArray;
use Commonhelp\DI\Definition\Source\DefinitionFile;
use Commonhelp\DI\Definition\Source\Commonhelp\DI\Definition\Source;
use Commonhelp\DI\Definition\Source\DefinitionSourceInterface;
use Commonhelp\DI\Proxy\ProxyFactory;
use Commonhelp\DI\Definition\Helper\ValueDefinitionHelper;
use Commonhelp\DI\Definition\Helper\ObjectDefinitionHelper;
use Commonhelp\DI\Definition\Helper\FactoryDefinitionHelper;
use Commonhelp\DI\Definition\Helper\Commonhelp\DI\Definition\Helper;
use Commonhelp\DI\Definition\Helper\EnviromentVariableDefinitionHelper;
use Commonhelp\DI\Definition\Helper\ArrayDefinitionExtensionHelper;
use Commonhelp\DI\Definition\EntryReference;

class ContainerBuilder{
	
	private $useAutowiring = true;
	
	private $useAnnotations = false;
	
	private $ignorePhpDocErrors = false;
	
	private $cache;
	
	private $definitionSources = array();
	
	private $writeProxysToFile = false;
	
	private $proxyDirectory;
	
	public function __construct($containerClass = 'ComplexContainer'){
		$this->containerClass = $containerClass;
	}
	
	/**
	 * 
	 * @return ContainerInterface
	 */
	public function build(){
		$sources = array_reverse($this->definitionSources);
		if($this->useAnnotations){
			$sources[] = new AnnotationReader($this->ignorePhpDocErrors);
		}else if($this->useAutowiring){
			$sources[] = new Autowiring();
		}
		$chain = new SourceChain($sources);
		
		if($this->cache){
			$source = new CachedDefinitionSource($chain, $this->cache);
			$chain->setRootDefinitionSource($source);
		}else{
			$source = $chain;
			$source->setMutableDefinitionSource(new DefinitionArray());
		}
		
		$proxyFactory = new ProxyFactory($this->writeProxysToFile, $this->proxyDirectory);
		
		return new ComplexContainer($source, $proxyFactory);
	}
	
	/**
	 * 
	 * @param bool $bool
	 * @return \Commonhelp\DI\ContainerBuilder
	 */
	public function useAutowiring($bool){
		$this->useAutowiring = $bool;
		return $this;
	}
	
	/**
	 * 
	 * @param bool $bool
	 * @return \Commonhelp\DI\ContainerBuilder
	 */
	public function useAnnotations($bool){
		$this->useAnnotations = $bool;
		return $this;
	}
	
	/**
	 * @param bool $bool
	 * @return \Commonhelp\DI\ContainerBuilder
	 */
	public function ignorePhpDocErrors($bool){
		$this->ignorePhpDocErrors = $bool;
		return $this;
	}
	
	/**
	 * 
	 * @param Cache $cache
	 * @return \Commonhelp\DI\ContainerBuilder
	 */
	public function setDefinitionCache(Cache $cache){
		$this->cache = $cache;
		return $this;
	}
	
	/**
	 * 
	 * @param bool $writeToFile
	 * @param string $proxyDirectory
	 * @throws InvalidArgumentException
	 * @return \Commonhelp\DI\ContainerBuilder
	 */
	public function writeProxiesToFile($writeToFile, $proxyDirectory = null){
		$this->writeProxysToFile = $writeToFile;
		
		if($writeToFile && $proxyDirectory === null){
			throw new InvalidArgumentException('The proxy directory must be specified if you want to write proxies on disk');
		}
		
		$this->proxyDirectory = $proxyDirectory;
		
		return $this;
	}
	
	
	/**
	 * 
	 * @param string|array $definitions
	 * @return \Commonhelp\DI\ContainerBuilder
	 */
	public function addDefinitions($definitions){
		if(is_string($definitions)){
			$definitions = new DefinitionFile($definitions);
		}else if(is_array($definitions)){
			$definitions = new DefinitionArray($definitions);
		}else if(!$definitions instanceof DefinitionSourceInterface){
			throw new InvalidArgumentException(sprintf(
				'%s parameter must be a string, an array or a DefinitionSource object, %s given',
				'ContainerVuilder::addDefinitions()',
				is_object($definitions) ? get_class($definitions) : gettype($definitions)
			));
		}
		
		$this->definitionSources[] = $definitions;
		
		return $this;
	}
	
	/**
	 * Helper for defining a value
	 * 
	 * @param mixed $value
	 * @return ValueDefinitionHelper
	 */
	public static function value($value){
		return new ValueDefinitionHelper($value);
	}
	
	/**
	 * Helper for defining an object
	 * 
	 * @param string|null $className Class name of the object. 
	 * 															If null, the name of the entry (in the container) will be used ad class name
	 * @return ObjectDefinitionHelper
	 */
	public static function object($className = null){
		return new ObjectDefinitionHelper($className);
	}
	
	/**
	 * Helper for defining a container entry using a factory function/callable.
	 * 
	 * @param callable $factory
	 * @return FactoryDefinitionHelper
	 */
	public static function factory($factory){
		return new FactoryDefinitionHelper($factory);
	}
	
	/**
	 * Decorate previous definition using a callable
	 * 	
	 * 		'foo' => ContainerBuilder::decorate(function($foo, $container){
	 * 			return new CachedFoo($foo, $container->get('cache'));
	 * 		});
	 * @param callable $callable
	 * @return FactoryDefinitionHelper
	 */
	public static function decorate($callable){
		return new FactoryDefinitionHelper($callable, true);
	}
	
	/**
	 * Helper for referencing another container entry in an object definition
	 * 
	 * @param string $entryName
	 * @return EntryReference
	 */
	public static function get($entryName){
		return new EntryReference($entryName);
	}
	
	/**
	 * Helper for referencing enviroment variables.
	 * 
	 * @param string $variableName
	 * @param mixed $defaultValue
	 * @return EnviromentVariableDefinitionHelper
	 */
	public static function env($variableName, $defaultValue=null){
		$isOptional = 2 === func_num_args();
		
		return new EnviromentVariableDefinitionHelper($variableName, $isOptional, $defaultValue);
	}
	
	/**
	 * Helper for extending another definition.
	 * Example
	 * 
	 * 			'log.backends' => ContainerBuilder::add(ContainerBuilder::get('My\Custom\LogBackend'));
	 * 
	 * or:
	 * 
	 * 			'log.backends' => ContainerBuilder::add([
	 * 					ContainerBuilder::get('My\Custom\LogBackend')
	 * 			]);
	 * 
	 * @param mixed|array $values A value or an array of values to add to the array
	 * @return ArrayDefinitionExtensionHelper
	 */
	public static function add($values){
		if(!is_array($values)){
			$values = array($values);
		}
		
		return new ArrayDefinitionExtensionHelper($values);
	}
	
	/**
	 * Helper for concatenating strings.
	 * 
	 * Example:
	 * 
	 * 		'log.filename' => ContainerBuilder::string('{app.path}/app.log');
 	 * 
	 * @param string $expression A string expression. Use the '{}' placeholders to reference other container entries.
	 * @return StringDefinitionHelper
	 */
	public static function string($expression){
		return new StringDefinitionHelper((string) $expression);
	}
	
}