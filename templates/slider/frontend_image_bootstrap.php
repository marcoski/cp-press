<?php 
	$options = $filter->apply('cppress_widget_slider_options', $options, $slides);
	$sliderClasses = $filter->apply('cppress_widget_slider_classes', array('carousel', 'slide'), $slides, $options);
	$sliderId = $filter->apply('cppress_widget_slider_id', 'cppress-carousel-'.md5(serialize($slides)), $slides, $options);
	$sliderAttrs = $filter->apply('cppress_widget_slider_attrs', array(
			'id' => $sliderId,
			'class' => implode(' ', $sliderClasses),
			'data-interval' => $options['timeout']
	), $slides);
	echo $filter->apply('cppress_widget_slider_before', '', $slides, $options, $sliderId);
	echo '<' . $filter->apply('cppress_widget_slider_tag', "section", $slides, $options);
	foreach($sliderAttrs as $name => $value){
		echo ' ' . $name . '="' . $value . '"';
	}
	echo '>';
	if(!$options['hideindicators']){
		echo '<ol class="carousel-indicators">';
		foreach($slides as $i => $slide){
			$active = ''; if($i==0){ $active = 'class="active"'; }
			echo '<li 
					' . $filter->apply('cppress_carousel_indicators_styles', '', $options['navcolor']) . '
					data-target="#' . $sliderAttrs['id'] .'" 
					data-slide-to="' . $i . '" ' . $active . '>
				</li>';
		}
		echo '</ol>';
	}
	echo '<div class="carousel-inner">';
	foreach($slides as $i => $slide){
		$active = ''; if($i==0){ $active = 'active'; }
		if(!$template->issetTemplate($templateName)){
				echo '<div class="item '. $active . '">';
				/**
				 * SLIDE CONTENT MARKUP START
				 */
				if($options['link'] == 'slide' && !empty($slide['link']) ){
					$new = $slide['link']['isext'] ? 'target="_new"' : '';
					echo $filter->apply('cppress_widget_slider_linkwrapper_open', '<a href="' . $slide['link']['url'] . '" ' . $new . '>', $slide['link']['isext'], $slide);
				}
				if(is_object($slide['img'])){
					$itemClassArray = array('cp-embed-responsive');
					$divClasses = $filter->apply('cppress_widget_slider_embed_classes', $itemClassArray, $slide, $options);
					$divAttrArray = array(
							'class' => implode(' ', $divClasses),
					);
					$divAttrs = $filter->apply('cppress_widget_slider_embed_classes', $divAttrArray, $slide);
					echo '<div ';
					foreach($divAttrs as $name => $value){
						echo ' ' . $name . '="' . $value . '"';
					}
					echo '>';
					echo $slide['img']->html;
					echo '</div>';
					if($slide['displaytitle']){
						echo '<h4 class="main">' . $slide['title'] . '</h4>';
					}
					if($slide['displaycontent']){
						echo '<p class="description">' . $slide['content'] . '</p>';
					}
				}else{
					$imageClasses = $filter->apply('cppress_widget_slider_image_classes', array(), $slide);
					$imageAttrs = $filter->apply('cppress_widget_slider_image_attrs', array(
						'src' => $slide['img']['src'][0],
						'alt' => $slide['img']['title']
					), $slide);
					echo '<img ';
					foreach($imageAttrs as $name => $value){
						echo ' ' . $name . '="' . $value . '"';
					}
					echo ' />';
					if($slide['displaytitle'] || $slide['displaycontent']){
						echo $filter->apply('cppress_widget_slider_before_caption', '', $slide, $sliderId);
						$captionClasses = $filter->apply('cppress_widget_slider_caption_classes', array('caption', 'caption-align-'.$slide['captionalign']), $slide);
						echo '<div class="' . implode(' ', $captionClasses) . '">';
					}
					if($slide['displaytitle']){
						echo $filter->apply('cppress_widget_slider_title', '<span class="main">' . $slide['title'] . '</span>', $slide);
					}
					if($slide['displaycontent']){
						echo $filter->apply('cppress_widget_slider_content', '<span class="secondary clearfix">' . $slide['content'] . '</span>', $slide);
					}
					if($slide['displaytitle'] || $slide['displaycontent']){
						echo '</div>';
						echo $filter->apply('cppress_widget_slider_after_caption', '', $slide, $sliderId);
					}
				}
				if($options['link'] == 'slide' && !empty($slide['link']) ){
					echo $filter->apply('cp_widget_slider_linkwrapper_close', '</a>', $slide);
				}
				/**
				 * SLIDE CONTENT MARKUP END
				 */
				echo '</div>';
		}else{
			echo $filter->apply(
				'cppress_widget_slider_image_template_content',
				$template->inc(
						$templateName,
						array(
								'slide' => $slide,
								'active' => $active,
						)
				),
				get_post(),
				$options
			);
		}
			
	}
	echo '</div>';
	
	if(!$options['hidecontrol']){
		echo '<a class="left carousel-control" href="#' . $sliderId . '" role="button" data-slide="prev">';
		$leftClasses = $filter->apply(
				'cppress_carousel_control_left_classes', 
				array('glyphicon glyphicon-chevron-left'),
				$sliderId,
				$slides
		);
		echo '<span class="' . implode(' ', $leftClasses) . '" aria-hidden="true"></span>';
		echo '<span class="sr-only">' . __('Previous', 'cppress') . '</span>';
		echo '</a>';
		echo '<a class="right carousel-control" href="#' . $sliderId . '" role="button" data-slide="next">';
		$rightClasses = $filter->apply(
				'cppress_carousel_control_right_classes',
				array('glyphicon glyphicon-chevron-right'),
				$sliderId,
				$slides
		);
		echo '<span class="' . implode(' ', $rightClasses) . '" aria-hidden="true"></span>';
		echo '<span class="sr-only">' . __('Next', 'cppress') . '</span>';
		echo '</a>';
	}
	
	echo '</' . $filter->apply('cppress_widget_slider_tag', "section", $slides, $options) . '>';
	echo $filter->apply('cppress_widget_slider_after', '', $slides, $options, $sliderId);