<?php
namespace CpPress\Application\FrontEnd;

use \Commonhelp\WP\WPController;
use CpPress\Application\WP\Hook\Filter;
use Commonhelp\App\Http\RequestInterface;

class FrontGalleryController extends WPController{
	
	private $filter;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $frontEndFilter){
		parent::__construct($appName, $request, $templateDirs);
		$this->filter = $frontEndFilter;
	}
	
	public function carousel($id, $lid, $gallery, $options){
		$options = wp_parse_args($options, array(
				'tperrow' => 1,
				'hideindicators' => true,
				'enablelightbox' => true
		));
		$this->assign('items', $gallery['items']);
		$this->assign('options', $options);
		$this->assign('filter', $this->filter);
		$this->assign('galleryId', $id);
		$this->assign('lightboxId', $lid);
	}
	
	public function glist($gallery, $options){
		$this->assign('gallery', $gallery);
		$this->assign('options', $options);
		$this->assign('filter', $this->filter);
	}
	
	public function lightbox($id, $lid, $item, $options){
		$salt = md5(serialize($item).$options['wtitle']);
		$this->assign('lightboxId', $lid);
		$this->assign('galleryId', $id);
		$this->assign('title', $options['wtitle']);
		$this->assign('item', $item);
	}
	
	
}