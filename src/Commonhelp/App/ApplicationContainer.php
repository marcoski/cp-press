<?php
namespace Commonhelp\App;

use Commonhelp\DI\SimpleContainer;
use Commonhelp\Orm\DataLayer\PdoDataLayer;
use Commonhelp\App\Exception\DatabaseException;
use Commonhelp\App\Http\Request;
use Commonhelp\App\Http\Http;
use Commonhelp\Util\Security\SecureRandom;

class ApplicationContainer extends SimpleContainer{
	
	
	private $middleWares = array();
	
	public function __construct($appName='', $urlParams=array()){
		parent::__construct();
		$this['appName'] = $appName;
		$this['urlParams'] = $urlParams;
		
		$this->registerService('SystemConfig', function($c){
			return new SystemConfig();
		});
		$this->registerAlias('ControllerMethodAnnotations', 'Commonhelp\Util\Annotations\ControllerMethodAnnotations');
		
		$middleWares = &$this->middleWares;
		$this->registerService('MiddlewareDispatcher', function($c) use (&$middleWares) {
			$dispatcher = new MiddlewareDispatcher();
			//PRE REGISTER MIDDELWARES
			
			foreach($middleWares as $middleWare) {
				$dispatcher->registerMiddleware($c[$middleWare]);
			}
			
			//POST REGISTER MIDDELWARES
			
			return $dispatcher;
		});
		
		$this->registerServices();
	}
	
	public function registerServices(){
		$this->registerService('DatabaseLayer', function(ApplicationContainer $c){
			$systemConfig = $c->getSystemConfig();
			$conOptions = array(
					'dsn' 		=> $systemConfig->getDbDsn(),
					'username' 	=> $systemConfig->getDbUsername(),
					'password'	=> $systemConfig->getDbPassword()
			);
			if(PdoDataLayer::isValidType($systemConfig->getDbType())){
				throw new DatabaseException('Invalid database type');
			}
				
			return PdoDataLayer::instance($conOptions);
		});
		
		$this->registerService('Request', function($c){
			$urlParams = $this['urlParams'];
			if (defined('PHPUNIT_RUN') && PHPUNIT_RUN
					&& in_array('fakeinput', stream_get_wrappers())
			) {
				$stream = 'fakeinput://data';
			} else {
				$stream = 'php://input';
			}
				
			return new Request(
					[
							'get' => $_GET,
							'post' => $_POST,
							'files' => $_FILES,
							'server' => $_SERVER,
							'env' => $_ENV,
							'cookies' => $_COOKIE,
							'method' => (isset($_SERVER) && isset($_SERVER['REQUEST_METHOD']))
							? $_SERVER['REQUEST_METHOD']
							: null,
							'urlParams' => $urlParams
					],
					$this->getSecureRandom(),
					$this->getSystemConfig(),
					$stream
			);
		});
	
		$this->registerService('SecureRandom', function(ApplicationContainer $c){
			return new SecureRandom();
		});
	
		$this->registerService('Protocol', function($c){
			if(isset($_SERVER['SERVER_PROTOCOL'])){
				return new Http($_SERVER, $_SERVER['SERVER_PROTOCOL']);
			}else{
				return new Http($_SERVER);
			}
		});
		
		$this->registerService('Form', function($c){
			return $this->resolve('Commonhelp\Form\FormCreator');
		});
	}
	
	public function getSystemConfig(){
		return $this->query('SystemConfig');
	}
	
	public function getDatabaseLayer(){
		return $this->query('DatabaseLayer');
	}
	
	public function getRequest(){
		return $this->query('Request');
	}
	
	public function getSecureRandom(){
		return $this->query('SecureRandom');
	}
	
	public function registerMiddleWare($middleWare) {
		array_push($this->middleWares, $middleWare);
	}
	
}