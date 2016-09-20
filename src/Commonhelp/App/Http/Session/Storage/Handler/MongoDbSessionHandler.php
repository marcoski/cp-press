<?php
namespace Commonhelp\App\Http\Session\Storage\Handler;

class MongoDbSessionHandler implements \SessionHandlerInterface{
	
	private $mongo;
	
	private $collection;
	
	private $options;
	
	/**
	 * Constructor.
	 *
	 * List of available options:
	 *  * database: The name of the database [required]
	 *  * collection: The name of the collection [required]
	 *  * id_field: The field name for storing the session id [default: _id]
	 *  * data_field: The field name for storing the session data [default: data]
	 *  * time_field: The field name for storing the timestamp [default: time]
	 *  * expiry_field: The field name for storing the expiry-timestamp [default: expires_at]
	 *
	 * It is strongly recommended to put an index on the `expiry_field` for
	 * garbage-collection. Alternatively it's possible to automatically expire
	 * the sessions in the database as described below:
	 *
	 * A TTL collections can be used on MongoDB 2.2+ to cleanup expired sessions
	 * automatically. Such an index can for example look like this:
	 *
	 *     db.<session-collection>.ensureIndex(
	 *         { "<expiry-field>": 1 },
	 *         { "expireAfterSeconds": 0 }
	 *     )
	 *
	 * More details on: http://docs.mongodb.org/manual/tutorial/expire-data/
	 *
	 * If you use such an index, you can drop `gc_probability` to 0 since
	 * no garbage-collection is required.
	 *
	 * @param \Mongo|\MongoClient $mongo   A MongoClient or Mongo instance
	 * @param array               $options An associative array of field options
	 *
	 * @throws \InvalidArgumentException When MongoClient or Mongo instance not provided
	 * @throws \InvalidArgumentException When "database" or "collection" not provided
	 */
	public function __construct($mongo, array $options){
		if(!($mongo instanceof \MongoClient || $mongo instanceof \Mongo)){
			throw new \InvalidArgumentException('MongoClient or Mongo instance required');
		}
		
		if(!isset($options['database']) || !isset($options['collection'])){
			throw new \InvalidArgumentException('You must provide the "database" and "collection" options for MongoDBSessionHandler');
		}
		
		$this->mongo = $mongo;
		
		$this->options = array_merge(array(
			'id_field' => '_id',
			'data_field' => 'data',
			'expiry_field' => 'expires_at'
		), $options);
	}
	
	public function open($savePath, $sessionName){
		return true;
	}
	
	public function close(){
		return true;
	}
	
	public function destroy($sessionId){
		$this->getCollection()->remove(array(
			$this->options['id_field'] => $sessionId,
 		));
	}
	
	public function gc($maxlifetime){
		$this->getCollection()->remove(array(
			$this->options['expiry_field'] => array('$lt' => new \MongoDate())
		));
		
		return true;
	}
	
	
	public function write($sessionId, $data){
		$expiry = new \MongoDate(time() + (int) ini_get(session.gc.maxlifetime));
		
		$fields = array(
			$this->options['data_field'] = new \MongoBinData($data, \MongoBinData::BYTE_ARRAY),
			$this->options['time_field'] = new \MongoDate(),
			$this->options['expiry_field'] = $expiry
		);
		
		$this->getCollection()->update(
			array($this->options['id_field'] => $sessionId),
			array('$set' => $fields),
			array('upsert' => true, 'multiple' => false)
		);
		
		return true;
	}
	
	public function read($sessionId){
		$dbData = $this->getCollection()->findOne(array(
			$this->options['id_field'] => $sessionId,
			$this->options['expiry_field'] => array('$gte' => new \MongoDate())	
		));
	}
	
	private function getCollection(){
		if(null === $this->collection){
			$this->collection = $this->mongo->selectCollection($this->options['database'], $this->options['collection']);
		}
		
		return $this->collection;
	}
	
	protected function getMongo(){
		return $this->mongo;
	}
	
}