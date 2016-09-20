<?php
namespace Commonhelp\Config\Configurator;

use Commonhelp\Config\Configurator\Exception\UnsupportedFormatException;
use Commonhelp\Filesystem\Exception\FileNotFoundException;
use Commonhelp\Filesystem\Exception\EmptyDirectoryException;
use Commonhelp\Util\Inflector;
use Commonhelp\DI\ContainerInterface;

class Configurator implements \ArrayAccess, ConfiguratorInterface, \Iterator{
	
	/**
	 * All file formats supported by Config
	 *
	 * @var array
	 */
	protected $supportedParsers = array(
			'Commonhelp\Config\Configurator\Parser\Php',
			'Commonhelp\Config\Configurator\Parser\Ini',
			'Commonhelp\Config\Configurator\Parser\Json',
			'Commonhelp\Config\Configurator\Parser\Xml',
			'Commonhelp\Config\Configurator\Parser\Yaml'
	);
	
	/**
	 * Stores the configuration data
	 *
	 * @var array|null
	 */
	protected $data = null;
	
	/**
	 * Caches the configuration data
	 *
	 * @var array
	 */
	protected $cache = array();
	
	protected $paths;
	
	/**
	 * Constructor method and sets default options, if any
	 *
	 * @param array $data
	*/
	public function __construct($path){
		$this->paths = $this->getValidPath($path);
		$this->data = array();
	}
	
	public function getPaths(){
		$paths = array();
		foreach($this->paths as $k => $path){
			$paths[$k] = pathinfo($path);
		}
		return $paths;
	}
	
	public function parse(ContainerInterface $c){
		foreach($this->paths as $path){
			$info = pathinfo($path);
			$parts = explode('.', $info['basename']);
			$extension = array_pop($parts);
			if($extension === 'dist'){
				$extension = array_pop($parts);
			}
			$parser = $this->getParser($extension);
			$this->data = array_replace_recursive($this->data, (array) $parser->parse($path));
			$configClass = $c->get(Inflector::classify($info['filename']) . 'Config');
			$configClass->setFile($path);
			$configClass->setParser($parser);
			foreach((array) $parser->parse($path) as $key => $value){
				$setter = 'set' . Inflector::camelize($key, '-');
				if(method_exists($configClass, $setter) || $configClass->isValidMethod($setter)){
					if($oldWriteable = $configClass->isWriteable()){
						$configClass->setNotWriteable();
					}
					$configClass->$setter($value);
					if($oldWriteable){
						$configClass->setWriteable();
					}
				}
			}
		}
		
		$this->data = array_merge($this->getDefaults(), $this->data);
	}
	
	/**
	 * Override this method in your own subclass to provide an array of default
	 * options and values
	 *
	 * @return array
	 *
	 * @codeCoverageIgnore
	 */
	protected function getDefaults(){
		return array();
	}
	
	/**
	 * ConfigInterface Methods
	 */
	/**
	 * {@inheritDoc}
	 */
	public function get($key, $default = null){
		if($this->has($key)){
			return $this->cache[$key];
		}
		return $default;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function set($key, $value){
		$segs = explode('.', $key);
		$root = &$this->data;
		$cacheKey = '';
		// Look for the key, creating nested keys if needed
		while($part = array_shift($segs)){
			if ($cacheKey != '') {
				$cacheKey .= '.';
			}
			$cacheKey .= $part;
			if(!isset($root[$part]) && count($segs)){
				$root[$part] = array();
			}
			$root = &$root[$part];
			//Unset all old nested cache
			if(isset($this->cache[$cacheKey])){
				unset($this->cache[$cacheKey]);
			}
			//Unset all old nested cache in case of array
			if(count($segs) == 0){
				foreach($this->cache as $cacheLocalKey => $cacheValue){
					if(substr($cacheLocalKey, 0, strlen($cacheKey)) === $cacheKey){
						unset($this->cache[$cacheLocalKey]);
					}
				}
			}
		}
		// Assign value at target node
		$this->cache[$key] = $root = $value;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function has($key){
		// Check if already cached
		if(isset($this->cache[$key])){
			return true;
		}
		
		$segments = explode('.', $key);
		$root = $this->data;
		// nested case
		foreach($segments as $segment){
			if(array_key_exists($segment, $root)){
				$root = $root[$segment];
				continue;
			}else{
				return false;
			}
		}
		// Set cache for the given key
		$this->cache[$key] = $root;
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function all(){
		return $this->data;
	}
	
	/**
	 * ArrayAccess Methods
	 */
	/**
	 * Gets a value using the offset as a key
	 *
	 * @param  string $offset
	 *
	 * @return mixed
	 */
	public function offsetGet($offset){
		return $this->get($offset);
	}
	
	/**
	 * Checks if a key exists
	 *
	 * @param  string $offset
	 *
	 * @return bool
	 */
	public function offsetExists($offset){
		return $this->has($offset);
	}
	
	/**
	 * Sets a value using the offset as a key
	 *
	 * @param  string $offset
	 * @param  mixed  $value
	 *
	 * @return void
	 */
	public function offsetSet($offset, $value){
		$this->set($offset, $value);
	}
	
	/**
	 * Deletes a key and its value
	 *
	 * @param  string $offset
	 *
	 * @return void
	 */
	public function offsetUnset($offset){
		$this->set($offset, null);
	}
	
	/**
	 * Iterator Methods
	 */
	/**
	 * Returns the data array element referenced by its internal cursor
	 *
	 * @return mixed The element referenced by the data array's internal cursor.
	 *     If the array is empty or there is no element at the cursor, the
	 *     function returns false. If the array is undefined, the function
	 *     returns null
	 */
	public function current(){
		return (is_array($this->data) ? current($this->data) : null);
	}
	
	/**
	 * Returns the data array index referenced by its internal cursor
	 *
	 * @return mixed The index referenced by the data array's internal cursor.
	 *     If the array is empty or undefined or there is no element at the
	 *     cursor, the function returns null
	 */
	public function key(){
		return (is_array($this->data) ? key($this->data) : null);
	}
	
	/**
	 * Moves the data array's internal cursor forward one element
	 *
	 * @return mixed The element referenced by the data array's internal cursor
	 *     after the move is completed. If there are no more elements in the
	 *     array after the move, the function returns false. If the data array
	 *     is undefined, the function returns null
	 */
	public function next(){
		return (is_array($this->data) ? next($this->data) : null);
	}
	
	/**
	 * Moves the data array's internal cursor to the first element
	 *
	 * @return mixed The element referenced by the data array's internal cursor
	 *     after the move is completed. If the data array is empty, the function
	 *     returns false. If the data array is undefined, the function returns
	 *     null
	 */
	public function rewind(){
		return (is_array($this->data) ? reset($this->data) : null);
	}
	
	/**
	 * Tests whether the iterator's current index is valid
	 *
	 * @return bool True if the current index is valid; false otherwise
	 */
	public function valid(){
		return (is_array($this->data) ? key($this->data) !== null : false);
	}
	
	public function getParser($extension){
		$parser = null;
		foreach($this->supportedParsers as $p){
			$tempParser = new $p;
			if(in_array($extension, $tempParser->getSupportedExtensions($extension))){
				$parser = $tempParser;
				continue;
			}
		}
		
		if($parser === null){
			throw new UnsupportedFormatException('Unsupported configuration format');
		}
		
		return $parser;
	}
	
	public function getPathFromArray($path){
		$paths = array();
		
		foreach($path as $unverifiedPath){
			try{
				if($unverifiedPath[0] !== '?'){
					$paths = array_merge($paths, $this->getValidPath($path));
					continue;
				}
				$optionalPath = ltrim($unverifiedPath, '?');
				$paths = array_merge($paths, $this->getValidPath($optionalPath));
			}catch(FileNotFoundException $e){
				if($unverifiedPath[0] === '?'){
					continue;
				}
				
				throw $e;
			}
		}
		
		return $paths;
	}
	
	public function getValidPath($path){
		if(is_array($path)){
			return $this->getPathFromArray($path);
		}
		
		if(is_dir($path)){
			$paths = glob($path . '/*.*');
			if(empty($paths)){
				throw new EmptyDirectoryException(sprintf('Configuration directory "%s" is empty.', $path));
			}
			
			return $paths;
		}
		
		if(!file_exists($path)){
			throw new FileNotFoundException(sprintf('Configuration file: "%s" cannot be found.', $path));
		}
		
		return array($path);
	}
	
}