<?php
namespace CpPress\Application\WP\Admin\SettingsSection\Section;

use CpPress\Application\WP\Admin\SettingsField\Field\AccordionField;
use CpPress\Application\WP\Admin\SettingsSection\SettingsSectionInterface;
use CpPress\Application\WP\Admin\SettingsField\SettingsFieldFactoryInterface;
use Commonhelp\Util\Collections\OrderedHashMap;

abstract class BaseSection implements \IteratorAggregate, SettingsSectionInterface{
	
	protected $id;
	protected $title;
	protected $page;

	protected $silent;
	
	/**
	 * @var SettingsSectionInterface;
	 */
	protected $parent;
	
	/**
	 * @var SettingsSectionInterface[]
	 */
	protected $children;
	
	/**
	 * @var SettingsFieldFactoryInterface;
	 */
	protected $settingsFieldFactory;
	
	public function __construct(SettingsFieldFactoryInterface $settingsFieldFactory, SettingsSectionInterface $parent = null){
		$this->settingsFieldFactory = $settingsFieldFactory;
		$this->parent = null;
		$this->children = new OrderedHashMap();
		$this->silent = false;
	}
	
	public function addSection(){
		add_settings_section(
			$this->id, 
			$this->title, 
			array($this, 'render'),
			$this->page
		);
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id = $id;
		return $this;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function setTitle($title){
		$this->title = $title;
		return $this;
	}
	
	public function getPage(){
		return $this->page;
	}
	
	public function setPage($page){
		$this->page = $page;
		return $this;
	}

    /**
     * @return SettingsFieldFactoryInterface
     */
	public function getSettingsFieldFactory(){
		return $this->settingsFieldFactory;
	}
	
	public function sanitize(array $inputs){
		$outputs = array();
		foreach($inputs as $name => $value){
			foreach((array) self::getAllFields($this) as $field){
                if($field instanceof AccordionField){
                    $outputs[$name] = $field->sanitize($value);
                }else{
                    if($field->getName() === $name){
                        $outputs[$name] = $field->sanitize($value);
                    }
                }
			}
		}
		return $outputs;
	}
	
	private static function getAllFields(SettingsSectionInterface $section, $fields = array()){
		if(count($section) > 0){
			foreach($section as $child){
				$fields = array_merge($fields, $child->getSettingsFieldFactory()->all());
			}
		}
		return array_merge($fields, (array) $section->getSettingsFieldFactory()->all());;
	}
	
	public function getParent(){
		return $this->parent;
	}
	
	public function setParent(SettingsSectionInterface $parent = null){
		if(null !== $parent && '' === $this->getPage()){
			throw new \LogicException('An options section with an empty page cannot have a parent section');
		}
		
		$this->parent = $parent;
		
		return $this;
	}
	
	public function getRoot(){
		return $this->parent ? $this->parent->getRoot() : $this;
	}
	
	public function isRoot(){
		return null === $this->parent;
	}
	
	public function add(SettingsSectionInterface $child){
		$child->setPage($this->getPage());
		$child->setParent($this);
		$this->children[] = $child;
		
		return $this;
	}
	
	public function remove($id){
		if(isset($this->children[$id])){
			$this->children[$id]->setParent(null);
			unset($this->children[$id]);
		}
		
		return $this;
	}
	
	public function has($id){
		return isset($this->children[$id]);
	}
	
	public function get($id){
		if($this->has($id)){
			return $this->children[$id];
		}
		
		return null;
	}
	
	public function all(){
		return iterator_to_array($this->children);
	}
	
	public function offsetExists($id){
		return $this->has($id);
	}
	
	public function offsetGet($id){
		return $this->get($id);
	}
	
	public function offsetSet($id, $child){
		if(!($child instanceof SettingsSectionInterface)){
			throw new \LogicException('You must set a SettingsSectionInterface instance as Section child');
		}
		
		$this->add($child);
	}
	
	public function offsetUnset($id){
		$this->remove($id);
	}
	
	public function getIterator(){
		return $this->children;
	}
	
	public function count(){
		return count($this->children);
	}

	public function isSilent(){
	    return $this->silent;
    }
	
	abstract public function render();
}