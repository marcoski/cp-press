<?php
namespace Commonhelp\App\Http\Session;

use Commonhelp\App\Http\Session\Storage\SessionStorageInterface;
use Commonhelp\App\Http\Session\Attribute\AttributeBagInterface;
use Commonhelp\App\Http\Session\Flash\FlashBagInterface;
use Commonhelp\App\Http\Session\Storage\NativeSessionStorage;
use Commonhelp\App\Http\Session\Attribute\AttributeBag;
use Commonhelp\App\Http\Session\Flash\FlashBag;

class Session implements SessionInterface, \IteratorAggregate, \Countable{
	
	/**
	 * Storage driver
	 * 
	 * @var SessionStorageInterface
	 */
	protected $storage;
	
	private $flashName;
	
	private $attributeName;
	
	/**
	 * Constructor
	 * 
	 * @param SessionStorageInterface $storage
	 * @param AttributeBagInterface $attibutes
	 * @param FlashBagInterface $flashes
	 */
	public function __construct(SessionStorageInterface $storage = null, AttributeBagInterface $attibutes = null, FlashBagInterface $flashes = null){
		$this->storage = $storage ?: new NativeSessionStorage();
		$attibutes = $attibutes ?: new AttributeBag();
		$this->attributeName = $attibutes->getName();
		$this->registerBag($attibutes);
		
		$flashes = $flashes ?: new FlashBag();
		$this->flashName = $flashes->getName();
		$this->registerBag($flashes);
	}
	
	public function start(){
		return $this->storage->start();
	}
	
	public function has($name){
		return $this->storage->getBag($this->attributeName)->has($name);
	}
	
	public function get($name, $default = null){
		return $this->storage->getBag($this->attributeName)->get($name, $default);
	}
	
	public function set($name, $value){
		$this->storage->getBag($this->attributeName)->set($name, $value);
	}
	
	public function all(){
		return $this->storage->getBag($this->attributeName)->all();
	}
	
	public function replace(array $attributes){
		$this->storage->getBag($this->attributeName)->replace($attributes);
	}
	
	public function remove($name){
		return $this->storage->getBag($this->attributeName)->remove($name);
	}
	
	public function clear(){
		$this->storage->getBag($this->attributeName)->clear();
	}
	
	public function isStarted(){
		return $this->storage->isStarted();
	}
	
	public function getIterator(){
		return new \ArrayIterator($this->storage->getBag($this->attributeName)->all());
	}
	
	public function count(){
		return count($this->storage->getBag($this->attributeName)->all());
	}
	
	public function invalidate($lifetime = null){
		$this->storage->clear();
		
		return $this->migrate(true, $lifetime);
	}
	
	public function migrate($destroy=false, $lifetime=null){
		return $this->storage->regenerate($destroy, $lifetime);
	}
	
	public function save(){
		$this->storage->save();
	}
	
	public function getId(){
		return $this->storage->getId();
	}
	
	public function setId($id){
		$this->storage->setId($id);
	}
	
	public function getName(){
		return $this->storage->getName();
	}
	
	public function setName($name){
		$this->storage->setName($name);
	}
	
	public function getMetadataBag(){
		return $this->storage->getMetadataBag();
	}
	
	public function registerBag(SessionBagInterface $bag){
		$this->storage->registerBag($bag);
	}
	
	public function getBag($name){
		return $this->storage->getBag($name);
	}
	
	public function getFlashBag(){
		return $this->getBag($this->flashName);
	}
	
}