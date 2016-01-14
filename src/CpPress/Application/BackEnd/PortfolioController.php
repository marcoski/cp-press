<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Query\Query;
use CpPress\Application\WP\Admin\PostMeta;
use Commonhelp\Util\Hash;
use CpPress\Application\WP\Admin\Settings;
use CpPress\Application\WP\Admin\Options;
use CpPress\Application\WP\MetaType\PostType;
use CpPress\Application\BackEndApplication;

class PortfolioController extends WPController{
	
	private $query;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Query $query){
		parent::__construct($appName, $request, $templateDirs);
		$this->query = $query;
	}
	
	public function show($id){
		if(preg_match("/([a-zA-Z]*):\s([0-9]+)/", $id, $match)){
			$id = $match[2];
			$type = $match[1];
		}else{
			return array();
		}
		$item = array_pop($this->query->find(
				array(
						'p' => $id,
						'post_type' => $type
				)
		));
		$thumb_id = get_post_thumbnail_id($item->ID);
		$item_img_thumb = wp_get_attachment_image_src($thumb_id, array('150', '150'));
		$item_img_full = wp_get_attachment_image_src($thumb_id, 'full');
		$this->assign('item_img_thumb', $item_img_thumb);
		$this->assign('item_img_full', $item_img_full);
		$this->assign('item_link', get_permalink($item->ID));
		$this->assign('item_title', $item->post_title);
		$this->assign('item_content', $item->post_excerpt);
	}
	
	/**
	 * @responder wpjson
	 */
	public function xhr_show(){
		$id = $this->getParam('id');
		$this->show($id);
	}
	
	/**
	 * @responder wpjson
	 */
	public function xhr_add($link, $item=''){
		$id = $this->getParam( 'id' );
		$name = $this->getParam( 'name' );
		$values = $this->getParam('values', array());
		if(!empty($values)){
			$this->assign('enable_link', $values['enablelink']);
		}else{
			$this->assign('enable_link', '');
		}
		$this->assign('item', $item);
		$this->assign('link', $link);
		$this->assign('name', $name);
		$this->assign('id', $id);
	}
	
}