<?php
namespace Commonhelp\DI\Proxy;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Configuration;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use ProxyManager\Factory\ProxyManager\Factory;

class ProxyFactory{
	
	private $writeProxiesToFile;
	
	private $proxyDirectory;
	
	/**
	 * 
	 * @var  LazyLoadingValueHolderFactory|null
	 */
	private $proxyManager;
	
	public function __construct($writeProxiesToFile, $proxyDirectory = null){
		$this->writeProxiesToFile = $writeProxiesToFile;
		$this->proxyDirectory = $proxyDirectory;
	}
	
	/**
	 * 
	 * @param string $className
	 * @param \Closure $initializer
	 * @return \ProxyManager\Proxy\LazyLoadingInterface
	 */
	public function createProxy($className, \Closure $initializer){
		$this->createProxyManager();
		return $this->proxyManager->createProxy($className, $initializer);
	}
	
	private function createProxyManager(){
		if($this->proxyManager !== null){
			return;
		}
		
		if(!class_exists('ProxyManager\Configuration')){
			throw new \RuntimeException('The ocramius/proxy-manager library is not installed. Lazy injection requires that library. Install it with composer');
		}
		
		$config = new Configuration();
		if($this->writeProxiesToFile){
			$config->setProxiesTargetDir($this->proxyDirectory);
			spl_autoload_register($config->getProxyAutoloader());
		}else{
			$config->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
		}
		
		$this->proxyManager = new LazyLoadingValueHolderFactory($config);
	}
	
}