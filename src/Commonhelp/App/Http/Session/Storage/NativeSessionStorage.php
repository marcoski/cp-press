<?php
namespace Commonhelp\App\Http\Session\Storage;

use Commonhelp\App\Http\Session\SessionBagInterface;
use Commonhelp\App\Http\Session\Storage\Proxy\AbstractProxy;
use Commonhelp\App\Http\Session\Storage\Handler\NativeSessionHandler;
use Commonhelp\App\Http\Session\Storage\Proxy\SessionHandlerProxy;
use Commonhelp\App\Http\Session\Storage\Proxy\Commonhelp\App\Http\Session\Storage\Proxy;

class NativeSessionStorage implements SessionStorageInterface{
	
	/**
	 * 
	 * @var SessionBagInterface[]
	 */
	protected $bags;
	
	protected $started = false;
	
	protected $closed = false;
	
	/**
	 * 
	 * @var AbstractProxy
	 */
	protected $saveHandler;
	
	/**
	 * 
	 * @var MetadataBag
	 */
	protected $metadataBag;
	
	/**
	 * Constructor.
	 *
	 * Depending on how you want the storage driver to behave you probably
	 * want to override this constructor entirely.
	 *
	 * List of options for $options array with their defaults.
	 *
	 * @see http://php.net/session.configuration for options
	 * but we omit 'session.' from the beginning of the keys for convenience.
	 *
	 * ("auto_start", is not supported as it tells PHP to start a session before
	 * PHP starts to execute user-land code. Setting during runtime has no effect).
	 *
	 * cache_limiter, "" (use "0" to prevent headers from being sent entirely).
	 * cookie_domain, ""
	 * cookie_httponly, ""
	 * cookie_lifetime, "0"
	 * cookie_path, "/"
	 * cookie_secure, ""
	 * entropy_file, ""
	 * entropy_length, "0"
	 * gc_divisor, "100"
	 * gc_maxlifetime, "1440"
	 * gc_probability, "1"
	 * hash_bits_per_character, "4"
	 * hash_function, "0"
	 * name, "PHPSESSID"
	 * referer_check, ""
	 * serialize_handler, "php"
	 * use_cookies, "1"
	 * use_only_cookies, "1"
	 * use_trans_sid, "0"
	 * upload_progress.enabled, "1"
	 * upload_progress.cleanup, "1"
	 * upload_progress.prefix, "upload_progress_"
	 * upload_progress.name, "PHP_SESSION_UPLOAD_PROGRESS"
	 * upload_progress.freq, "1%"
	 * upload_progress.min-freq, "1"
	 * url_rewriter.tags, "a=href,area=href,frame=src,form=,fieldset="
	 *
	 * @param array                                                            $options Session configuration options.
	 * @param AbstractProxy|NativeSessionHandler|\SessionHandlerInterface|null $handler
	 * @param MetadataBag                                                      $metaBag MetadataBag.
	 */
	public function __construct(array $options = array(), $handler = null, MetadataBag $metaBag = null){
		session_cache_limiter('');
		ini_set('session.use_cookies', 1);
		
		session_register_shutdown();
		
		$this->setMetadataBag($metaBag);
		$this->setOptions($options);
		$this->setSaveHandler($handler);
	}
	
	public function getSaveHandler(){
		return $this->saveHandler;
	}
	
	public function start(){
		if($this->started){
			return true;
		}
		
		if(\PHP_SESSION_ACTIVE === session_status()){
			throw new \RuntimeException('Failed to start the session: already started by PHP');
		}
		
		if(ini_get('session.use_cookies') && headers_sent($file, $line)){
			throw new \RuntimeException(sprintf('Failed to start the session because headers have already been sent by "%s" at line %d.', $file, $line));
		}
		
		if(!session_start()){
			throw new \RuntimeException('Failed to start the session');
		}
		
		$this->loadSession();
		
		return true;
	}
	
	public function getId(){
		return $this->saveHandler->getId();
	}
	
	public function setId($id){
		$this->saveHandler->setId($id);
	}
	
	public function getName(){
		return $this->saveHandler->getName();
	}
	
	public function setName($name){
		$this->saveHandler->setName($name);
	}
	
	public function regenerate($destroy = false, $lifetime = null){
		if(\PHP_SESSION_ACTIVE !== session_start()){
			return false;
		}
		
		if(null !== $lifetime){
			ini_set('session.cookie_lifetime', $lifetime);
		}
		
		if($destroy){
			$this->metadataBag->stampNew();
		}
		
		$isRegenerated = session_regenerate_id($destroy);
		$this->loadSession();
		
		return $isRegenerated;
	}
	
	public function save(){
		session_write_close();
		
		$this->closed = true;
		$this->started = false;
	}
	
	public function clear(){
		foreach($this->bags as $bag){
			$bag->clear();
		}
		
		$_SESSION = array();
		
		$this->loadSession();
	}
	
	public function registerBag(SessionBagInterface $bag){
		if($this->started){
			throw new \LogicException('Cannot register a bag when the session is already started.');
		}
		
		$this->bags[$bag->getName()] = $bag;
	}
	
	public function getBag($name){
		if(!isset($this->bags[$name])){
			throw new \InvalidArgumentException(sprintf('The SessionBagInterface %s is not registered.', $name));
		}
		
		if($this->saveHandler->isActive() && !$this->started){
			$this->loadSession();
		}else if(!$this->started){
			$this->start();
		}
		
		return $this->bags[$name];
	}
	
	public function setMetadataBag(MetadataBag $metaBag = null){
		if(null === $metaBag){
			$metaBag = new MetadataBag();
		}
		
		$this->metadataBag = $metaBag;
	}
	
	public function getMetadataBag(){
		return $this->metadataBag;
	}
	
	public function isStarted(){
		return $this->started;
	}
	
	/**
	 * Sets session.* ini variables.
	 *
	 * For convenience we omit 'session.' from the beginning of the keys.
	 * Explicitly ignores other ini keys.
	 *
	 * @param array $options Session ini directives array(key => value).
	 *
	 * @see http://php.net/session.configuration
	 */
	public function setOptions(array $options){
		$validOptions = array_flip(array(
				'cache_limiter', 'cookie_domain', 'cookie_httponly',
				'cookie_lifetime', 'cookie_path', 'cookie_secure',
				'entropy_file', 'entropy_length', 'gc_divisor',
				'gc_maxlifetime', 'gc_probability', 'hash_bits_per_character',
				'hash_function', 'name', 'referer_check',
				'serialize_handler', 'use_cookies',
				'use_only_cookies', 'use_trans_sid', 'upload_progress.enabled',
				'upload_progress.cleanup', 'upload_progress.prefix', 'upload_progress.name',
				'upload_progress.freq', 'upload_progress.min-freq', 'url_rewriter.tags',
		));
		foreach ($options as $key => $value) {
			if (isset($validOptions[$key])) {
				ini_set('session.'.$key, $value);
			}
		}
	}
	
	/**
	 * Registers session save handler as a PHP session handler.
	 *
	 * To use internal PHP session save handlers, override this method using ini_set with
	 * session.save_handler and session.save_path e.g.
	 *
	 *     ini_set('session.save_handler', 'files');
	 *     ini_set('session.save_path', '/tmp');
	 *
	 * or pass in a NativeSessionHandler instance which configures session.save_handler in the
	 * constructor, for a template see NativeFileSessionHandler or use handlers in
	 * composer package drak/native-session
	 *
	 * @see http://php.net/session-set-save-handler
	 * @see http://php.net/sessionhandlerinterface
	 * @see http://php.net/sessionhandler
	 * @see http://github.com/drak/NativeSession
	 *
	 * @param AbstractProxy|NativeSessionHandler|\SessionHandlerInterface|null $saveHandler
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setSaveHandler($saveHandler = null){
		if(!$saveHandler instanceof AbstractProxy &&
			 !$saveHandler instanceof NativeSessionHandler &&
			 !$saveHandler instanceof \SessionHandlerInterface &&
			 null !== $saveHandler){
			throw new \InvalidArgumentException('Must be instance of AbstractProxy or NativeSessionHandler; implement \SessionHandlerInterface; or be null');
		}
		
		if(!$saveHandler instanceof AbstractProxy && $saveHandler instanceof  \SessionHandlerInterface){
			$saveHandler = new SessionHandlerProxy($saveHandler);
		}else if(!$saveHandler instanceof AbstractProxy){
			$saveHandler = new SessionHandlerProxy(new \SessionHandler());
		}
		
		$this->saveHandler = $saveHandler;
		
		if($this->saveHandler instanceof \SessionHandlerInterface){
			session_set_save_handler($this->saveHandler, false);
		}
		
	}
	
	protected function loadSession(array &$session = null){
		if(null === $session){
			$session = &$_SESSION;
		}
		
		$bags = array_merge($this->bags, array($this->metadataBag));
		
		foreach($bags as $bag){
			$key = $bag->getStorageKey();
			$session[$key] = isset($session[$key]) ? $session[$key] : array();
			$bag->initialize($session[$key]);
		}
		
		$this->started = true;
		$this->closed = false;
	}
	
}