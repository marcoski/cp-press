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
	
	public function glist($id, $lid, $gallery, $options){
        $itemPerRowBootstrap = round(12/$options['tperrow']);
        if($itemPerRowBootstrap < 1){
            $itemPerRowBootstrap = 1;
        }
        $rows = ceil(count($gallery['items'])/$options['tperrow']);

		$this->assign('items', $gallery['items']);
		$this->assign('options', $options);
		$this->assign('filter', $this->filter);
        $this->assign('galleryId', $id);
        $this->assign('lightboxId', $lid);
        $this->assign('item_per_row_bootstrap', $itemPerRowBootstrap);
        $this->assign('rows', $rows);
	}
	
	public function lightbox($id, $lid, $item, $options){
		$salt = md5(serialize($item).$options['title']);
		$this->assign('lightboxId', $lid);
		$this->assign('galleryId', $id);
		$this->assign('gallery_title', $options['title']);
		$this->assign('item', $item);
        $this->assign('filter', $this->filter);
	}
	
	
}