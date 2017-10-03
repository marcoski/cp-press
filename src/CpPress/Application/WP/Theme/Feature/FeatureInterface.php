<?php
/**
 * Created by PhpStorm.
 * User: marcoski
 * Date: 29/09/16
 * Time: 15:55
 */

namespace CpPress\Application\WP\Theme\Feature;


/**
 * Interface FeatureInterface
 * @package CpPress\Application\WP\Theme\Feature
 */
interface FeatureInterface {

	public function has($option);

	public function get($option);

	public function set($option, $value);

	public function setOptions(array $options);

	public function all();

	public function hooks();

	public function add();

	public function adminEnqueueScripts($hook);

	public function getMetaKey();

	public function render();

}