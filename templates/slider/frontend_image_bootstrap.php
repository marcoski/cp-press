<?php 
	$options = $filter->apply('cppress_widget_slider_options', $options, $slides);
	$sliderClasses = $filter->apply('cppress_widget_slider_classes', array('carousel', 'slide'), $slides);
	$sliderId = $filter->apply('cppress_widget_slider_id', 'cppress-carousel-'.md5(serialize($slides)), $slides);
	$sliderAttrs = $filter->apply('cppress_widget_slider_attrs', array(
			'id' => $sliderId,
			'class' => implode(' ', $sliderClasses),
			'data-interval' => $options['timeout']
	), $slides);
	
	echo '<' . $filter->apply('cppress_widget_slider_tag', "section", $slides);
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
			echo '<div class="item '. $active . '">';
			/**
			 * SLIDE CONTENT MARKUP START
			 */
			if($options['link'] == 'slide' && !empty($slide['link']) ){
				$new = $slide['link']['isext'] ? 'target="_new"' : '';
				echo '<a href="' . $slide['link']['url'] . '" ' . $new . '>';
			}
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
				$captionClasses = $filter->apply('cppress_widget_slider_caption_classes', array('caption'), $slide);
				echo '<div class="' . implode(' ', $captionClasses) . '">';
			}
			if($slide['displaytitle']){
				echo '<span class="main">' . $slide['title'] . '</span>';
			}
			if($slide['displaycontent']){
				echo '<span class="secondary clearfix">' . $slide['content'] . '</span>';
			}
			if($slide['displaytitle'] || $slide['displaycontent']){
				echo '</div>';
			}
			if($options['link'] == 'slide' && !empty($slide['link']) ){
				echo '</a>';
			}
			/**
			 * SLIDE CONTENT MARKUP END
			 */
			echo '</div>';
			
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
	
	echo '</' . $filter->apply('cppress_widget_slider_tag', "section", $slides) . '>';