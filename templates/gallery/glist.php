<?php

$options = $filter->apply('cppress_widget_gallery_options', $options, $items);
$galleryClasses = $filter->apply('cppress_widget_gallery_classes', array('cp-gallery', $options['galleryclass']), $items, $options);
$galleryAttrs = $filter->apply('cppress_widget_gallery_attrs', array(
    'id' => $galleryId,
    'class' => implode(' ', $galleryClasses),
));

if(!empty($items)){
    echo $filter->apply('cppress_widget_gallery_before', '', $items, $options, $galleryId);
    echo '<' . $filter->apply('cppress_widget_gallery_tag', "div", $items, $options);
    foreach($galleryAttrs as $name => $value){
        echo ' ' . $name . '="' . $value . '"';
    }
    echo '>';
    $itemCounter = 0;
    for($i = 0; $i<$rows; $i++){
        echo '<div class="row">';
        for($j=0; $j<$options['tperrow']; $j++, $itemCounter++){
            echo '<div class="col-md-'.$item_per_row_bootstrap.'">';
            if(isset($items[$itemCounter])){
                $item = $items[$itemCounter];
                echo '<div class="cp-gallery-item">';
                echo '<figure>';
                echo $filter->apply('cppress_widget_gallery_before_image', '', $item, $options, $galleryId);
                if(!$item['isvideo']){
                    $itemClassArray[] = 'img-responsive';
                    $imageClasses = $filter->apply('cppress_widget_gallery_item_classes', $itemClassArray, $item, $options);
                    if($options['enablelightbox']){
                        $imgAttrArray = array(
                            'src' => $item['link'],
                            'alt' => $item['caption'],
                            'class' => implode(' ', $imageClasses),
                            'data-gallery' => 'multiimage',
                            'data-toggle' => 'lightbox',
                            'data-target' => '#'.$lightboxId,
                            'data-title' => $options['wtitle'],
                            'data-footer' => $item['caption'],
                        );
                    }else{
                        $imgAttrArray = array(
                            'src' => $item['link'],
                            'alt' => $item['caption'],
                            'class' => implode(' ', $imageClasses)
                        );
                    }
                    $imageAttrs = $filter->apply('cppress_widget_gallery_item_attrs', $imgAttrArray, $item);
                    echo '<img ';
                    foreach($imageAttrs as $name => $value){
                        echo ' ' . $name . '="' . $value . '"';
                    }
                    echo ' />';
                    if(isset($item['caption'])){
                        echo '<figcaption>'.$item['caption'].'</figcaption>';
                    }
                    echo '</figure>';
                }else{
                    $itemClassArray[] = $options['enablelightbox'] ? 'cp-embed-lightbox play-icon' : 'cp-embed-responsive';
                    $divClasses = $filter->apply('cppress_widget_gallery_item_classes', $itemClassArray, $item, $options);
                    if($options['enablelightbox']){
                        $divAttrArray = array(
                            'class' => implode(' ', $divClasses),
                            'data-gallery' => 'multiimage',
                            'data-toggle' => 'lightbox',
                            'data-target' => '#'.$lightboxId,
                            'data-title' => $options['wtitle'],
                            'data-footer' => $item['oembed']->title,
                            'data-type' => strtolower($item['oembed']->provider),
                            'data-remote' => $item['link']
                        );
                    }else{
                        $divAttrArray = array(
                            'class' => implode(' ', $divClasses),
                        );
                    }
                    $divAttrs = $filter->apply('cppress_widget_gallery_item_attrs', $divAttrArray, $item);
                    echo '<div ';
                    foreach($divAttrs as $name => $value){
                        echo ' ' . $name . '="' . $value . '"';
                    }
                    echo '>';
                    if($options['enablelightbox']){
                        echo '<img src="' . $item['oembed']->thumbnail_url .'" alt="' . $item['oembed']->title . '">';
                    }else{
                        echo $item['oembed']->html;
                    }
                    if(isset($item['caption'])){
                        echo '<figcaption>'.$item['caption'].'</figcaption>';
                    }
                    echo '</figure>';
                    echo '</div>';
                }
                echo $filter->apply('cppress_widget_gallery_after_image', '', $item, $options, $galleryId);
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
    }

    echo '</' . $filter->apply('cppress_widget_gallery_tag', "div", $slides, $options) . '>';
    echo $filter->apply('cppress_widget_gallery_after', '', $items, $options, $galleryId);
    if(isset($lightbox)){
        echo $lightbox;
    }
}

