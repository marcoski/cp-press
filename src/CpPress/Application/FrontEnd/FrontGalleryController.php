<?php
namespace CpPress\Application\FrontEnd;

use \Commonhelp\WP\WPController;
use Commonhelp\WP\WPTemplateResponse;
use CpPress\Application\FrontEndApplication;
use CpPress\Application\WP\Hook\Filter;
use Commonhelp\App\Http\RequestInterface;
use CpPress\CpPress;

class FrontGalleryController extends WPController{
	
	private $filter;
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array(), Filter $frontEndFilter){
		parent::__construct($appName, $request, $templateDirs);
		$this->filter = $frontEndFilter;
	}

	public function shortcode($attrs)
    {
        $salt = md5(serialize($attrs));

        if(!empty($attrs['include'])){
            $_attachments = get_posts([
                'include' => $attrs['include'],
                'post_status' => 'inherit',
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'order' => $attrs['order'],
                'orderby' => $attrs['orderby']
            ]);
            $attachments = [];
            foreach($_attachments as $key => $val){
                $attachments[$val->ID] = $_attachments[$key];
            }
        }else if(!empty($attrs['exclude'])){
            $attachments = get_children([
                'post_parent' => $attrs['id'],
                'exclude' => $attrs['exclude'],
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'order' => $attrs['order'],
                'orderby' => $attrs['orderby']
            ]);
        }else{
            $attachments = get_children([
                'post_parent' => $attrs['id'],
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'order' => $attrs['order'],
                'orderby' => $attrs['orderby']
            ]);
        }

        if(empty($attachments)){
            return '';
        }

        $items = [];
        foreach($attachments as $attId => $attachment){
            $image = wp_get_attachment_image_src($attId, $attrs['size']);
            $item = [
                'link' => $image[0],
                'isvideo' => false,
                'caption' => wp_get_attachment_caption($attId)
            ];
            $items[] = $item;
        }

        $options = array(
            'enablelightbox' => false,
            'tperrow' => 1,
            'title' => isset($attrs['title']) ? $attrs['title']: '',
            'galleryclass' => isset($attrs['galleryclass']) ? $attrs['galleryclass']: '',
            'thumbindicators' => isset($attrs['thumbindicators']) ? $attrs['thumbindicators'] : false,
            'hideindicators' => isset($attrs['hideindicators']) ? $attrs['hideindicators'] : false
        );
        $lightboxId = $this->filter->apply(
            'cppress_widget_gallery_lightbox_id', 'cppress-carousel-lightbox-'.$salt, $attrs['id'], $options
        );
        $this->assign('lightboxId', $lightboxId);
        $this->assign('filter', $this->filter);
        $this->assign('items', $items);
        if($attrs['columns'] > 1){
            $galleryId = $this->filter->apply(
                'cppress_widget_gallery_id', 'cppress-glist-'. $salt , $attrs, $options
            );
            $options['tperrow'] = $attrs['columns'];
            $options['enablelightbox'] = true;
            $this->assign('galleryId', $galleryId);
            $this->assign('options', $options);

            $itemPerRowBootstrap = round(12/$options['tperrow']);
            if($itemPerRowBootstrap < 1){
                $itemPerRowBootstrap = 1;
            }
            $rows = ceil(count($items)/$options['tperrow']);
            $this->assign('rows', $rows);
            $this->assign('item_per_row_bootstrap', $itemPerRowBootstrap);
            $this->assign('lightbox', FrontEndApplication::part(
                'Gallery',
                'lightbox',
                CpPress::$App->getContainer(),
                [$galleryId, $lightboxId, $items[0], $options]
            ));
            return new WPTemplateResponse($this, 'glist');
        }
        $this->assign('options', $options);
        $this->assign('galleryId', $this->filter->apply(
            'cppress_widget_gallery_id', 'cppress-carousel-'. $salt , $attrs, $options
        ));

        return new WPTemplateResponse($this, 'carousel');
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
		$this->assign('lightboxId', $lid);
		$this->assign('galleryId', $id);
		$this->assign('gallery_title', $options['title']);
		$this->assign('item', $item);
        $this->assign('filter', $this->filter);
	}
	
	
}