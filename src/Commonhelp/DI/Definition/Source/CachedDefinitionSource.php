<?php
namespace Commonhelp\DI\Definition\Source;

use Commonhelp\DI\Definition\DefinitionInterface;
use Doctrine\Common\Cache\Cache;
use Commonhelp\DI\Definition\CachableDefinitionInterface;

class CachedDefinitionSource implements DefinitionSourceInterface{
	
	const CACHE_PREFIX = 'Commonhelp\\DI\\Definition\\';
	
	/**
	 * @var DefinitionSourceInterface
	 */
	private $source;
	
	/**
	 * @var Cache
	 */
	private $cache;
	
	public function __construct(DefinitionSourceInterface $source, Cache $cache){
		$this->source = $source;
		$this->cache = $cache;
	}
	
	public function getDefinition($name){
		$definition = $this->fetchFromCache($name);
		if($definition === false){
			$definition = $this->source->getDefinition($name);
			
			if($definition === null || ($definition instanceof CachableDefinitionInterface)){
				$this->saveToCache($name, $definition);
			}
		}
		
		return $definition;
	}
	
	public function getCache(){
		return $this->cache;
	}
	
	private function fetchFromCache($name){
		$cacheKey = self::CACHE_PREFIX . $name;
		$data = $this->cache->fetch($cacheKey);
		
		if($data !== false){
			return $data;
		}
		
		return false;
	}
	
	private function saveToCache($name, DefinitionInterface $definition = null){
		$cacheKey = self::CACHE_PREFIX . $name;
		
		$this->cache->save($cacheKey, $definition);
	}
}