<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 29/09/16
 * Time: 16:00
 */

namespace CpPress\Application\WP\Theme\Feature;


use Commonhelp\WP\WPContainer;
use CpPress\Application\WP\Admin\MetaBox;
use CpPress\Application\WP\Asset\Scripts;
use CpPress\Application\WP\Hook\Filter;
use CpPress\Application\WP\Hook\Hook;
use CpPress\Application\WP\MetaType\PostType;

/**
 * Class BaseFeature
 * @package CpPress\Application\WP\Theme\Feature
 */
abstract class BaseFeature implements FeatureInterface {

	protected $options;
	protected $defaults;

	/**
	 * @var Scripts
	 */
	protected $scripts;

	/**
	 * @var Filter
	 */
	protected $filter;

	/**
	 * @var Hook
	 */
	protected $hook;

	/**
	 * @var MetaBox
	 */
	protected $metaBox;

	/**
	 * @var WPContainer
	 */
	protected $container;


	/**
	 * BaseFeature constructor.
	 *
	 * @param Hook $hook
	 * @param Filter $filter
	 * @param Scripts $scripts
	 * @param array $options
	 * @param WPContainer $container
	 */
	public function __construct(Hook $hook, Filter $filter, Scripts $scripts, $options = array(), WPContainer $container = null){
		$this->scripts = $scripts;
		$this->filter = $filter;
		$this->hook = $hook;
		$this->container = $container;

		$this->defaults = array(
			'label' => null,
			'id' => null,
			'post_type' => 'post',
			'priority' => 'low',
			'context' => 'side'
		);
		$this->options = wp_parse_args($options, $this->defaults);

	}

	/**
	 * @param $option
	 *
	 * @return bool
	 */
	public function has($option){
		return isset($this->options[$option]);
	}

	/**
	 * @param $option
	 *
	 * @return null|string
	 */
	public function get($option){
		if($this->has($option)){
			return $this->options[$option];
		}

		return null;
	}

	public function getPostTypeName(){
		if($this->get('post_type') instanceof PostType){
			return $this->get('post_type')->getPostTypeName();
		}

		return $this->get('post_type');
	}

	/**
	 * @param $option
	 * @param $value
	 *
	 * @return $this|null
	 */
	public function set($option, $value){
		if('post_type' === $option && isset($this->options['post_type'])){
			$oldPostType = $this->options['post_type'];
			$this->options['post_type'] = [$oldPostType, $value];
		}
		if(array_key_exists($option, $this->defaults)){
			$this->options[$option] = $value;
			return $this;
		}

		return null;
	}

	/**
	 * @param array $options
	 *
	 * @return $this
	 */
	public function setOptions(array $options){
		$this->options = wp_parse_args($options, $this->defaults);
		return $this;
	}

	/**
	 * @return array
	 */
	public function all(){
		return $this->options;
	}

	public function adminEnqueueScripts($hook){
	}

	public function hooks(){
		$this->hook->register('add_meta_boxes', array($this, 'add'));
		$this->hook->execAll();
	}

	public function add() {
		$this->metaBox = new MetaBox();
		$this->metaBox->setCallback(array($this, 'render'))
			->setId($this->get('id'))
			->setTitle($this->get('label'))
			->setPostType($this->get('post_type'))
			->setPriority($this->get('priority'))
			->setContext($this->get('context'))
			->add();
	}

	abstract public function getMetaKey();
	abstract public function render();

}