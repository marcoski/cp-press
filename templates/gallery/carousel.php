<?php
	$options = $filter->apply('cppress_widget_gallery_options', $options, $items);
	$galleryClasses = $filter->apply('cppress_widget_gallery_classes', array('carousel', 'slide', 'cp-gallery', $options['galleryclass']), $items, $options);
	$galleryAttrs = $filter->apply('cppress_widget_gallery_attrs', array(
			'id' => $galleryId,
			'class' => implode(' ', $galleryClasses),
			'data-ride' => 'carousel'
	), $slides);
	if(!empty($items)){
		echo $filter->apply('cppress_widget_gallery_before', '', $items, $options, $galleryId);
		echo '<' . $filter->apply('cppress_widget_gallery_tag', "div", $items, $options);
		foreach($galleryAttrs as $name => $value){
			echo ' ' . $name . '="' . $value . '"';
		}
		echo '>';
        if($options['thumbindicators']){
            echo '<div class="carousel-outer">';
        }
		if(!$options['hideindicators']){
			echo '<ol class="carousel-indicators">';
			foreach($items as $i => $item){
				$active = ''; if($i==0){ $active = 'class="active"'; }
				echo '<li 
						' . $filter->apply('cppress_carousel_indicators_styles', '') . '
						data-target="#' . $galleryId .'" 
						data-slide-to="' . $i . '" ' . $active . '>
					</li>';
			}
			echo '</ol>';
		}
		echo '<div class="carousel-inner">';
		for($i=0; $i<count($items); $i++){
			$item = $items[$i];
			$active = ''; if($i==0){ $active = 'active'; }
			if($options['tperrow'] > 1){
				echo '<div class="item '. $active . '">';
			}else{
				$itemClassArray = array('item', $active);
			}
			/**
			 * SLIDE CONTENT MARKUP START
			 */
			echo '<figure class="'.implode(' ', $itemClassArray).'">';
			if(!$item['isvideo']){
				$imgClassArray[] = 'img-responsive';
				$imageClasses = $filter->apply('cppress_widget_gallery_item_classes', $imgClassArray, $item, $options);
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
				echo '</div>';
			}
			if(isset($item['caption']) && $item['caption'] !== ''){
			    echo '<figcaption>'.$item['caption'].'</figcaption>';
            }
			echo '</figure>';
			/**
			 * SLIDE CONTENT MARKUP END
			 */
			if($options['tperrow'] > 1){
				echo '</div>';
			}
				
		}
		echo '</div>';

		
        echo '<a class="left carousel-control" href="#' . $galleryId . '" role="button" data-slide="prev">';
        $leftClasses = $filter->apply(
                'cppress_carousel_control_left_classes',
                array('glyphicon glyphicon-chevron-left'),
                $galleryId,
                $items
        );
        echo '<span class="' . implode(' ', $leftClasses) . '" aria-hidden="true"></span>';
        echo '<span class="sr-only">' . __('Previous', 'cppress') . '</span>';
        echo '</a>';
        echo '<a class="right carousel-control" href="#' . $galleryId . '" role="button" data-slide="next">';
        $rightClasses = $filter->apply(
                'cppress_carousel_control_right_classes',
                array('glyphicon glyphicon-chevron-right'),
                $galleryId,
                $slides,
                $options
        );
        echo '<span class="' . implode(' ', $rightClasses) . '" aria-hidden="true"></span>';
        echo '<span class="sr-only">' . __('Next', 'cppress') . '</span>';
        echo '</a>';

        if($options['thumbindicators']){
            echo '</div>';

            echo '<ol class="carousel-indicators">';
            foreach($items as $i => $item){
                $active = ''; if($i==0){ $active = 'class="active"'; }
                echo '<li 
                    ' . $filter->apply('cppress_carousel_indicators_styles', '') . '
                    data-target="#' . $galleryId .'" 
                    data-slide-to="' . $i . '" ' . $active . '>
                    <img class="thumb-indicators img-responsive" src="'.$item['link'].'" />
                </li>';
            }
            echo '</ol>';

        }
		
		echo '</' . $filter->apply('cppress_widget_gallery_tag', "div", $slides, $options) . '>';
		echo $filter->apply('cppress_widget_gallery_after', '', $items, $options, $galleryId);
	}