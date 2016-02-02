<?php 
	$options = $filter->apply('cppress_widget_slider_options', $options, $slides);
	$sliderClasses = $filter->apply('cppress_widget_slider_classes', array('flexslider'), $slides);
	$sliderAttrs = $filter->apply('cppress_widget_slider_attrs', array(
			'id' => 'cppress-carousel-'.md5(serialize($slides)),
			'class' => implode(' ', $sliderClasses),
			'data-interval' => $options['timeout'],
			'data-speed' => $options['speed'],
			'data-navcolor' => $filter->apply('cppress_widget_slider_navcolor', '', $options['navcolor'])
	), $slides);
	
	echo '<' . $filter->apply('cppress_widget_slider_tag', "div", $slides);
	foreach($sliderAttrs as $name => $value){
		echo ' ' . $name . '="' . $value . '"';
	}
	echo '>';
	echo '<ul class="slides">';
	foreach($slides as $i => $slide){
		$active = ''; if($i==0){ $active = $filter->apply('cppress_widget_slider_flexactiveclass', '', $slide); }
			echo '<li ' . $active . '>';
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
				echo '<p class="' . implode(' ', $captionClasses) . '">';
			}
			if($slide['displaytitle']){
				echo '<span class="main">' . $slide['title'] . '</span>';
			}
			if($slide['displaycontent']){
				echo '<span class="secondary clearfix">' . $slide['content'] . '</span>';
			}
			if($slide['displaytitle'] || $slide['displaycontent']){
				echo '</p>';
			}
			if($options['link'] == 'slide' && !empty($slide['link']) ){
				echo '</a>';
			}
			/**
			 * SLIDE CONTENT MARKUP END
			 */
			echo '</li>';
			
	}
	echo '</ul>';
	echo '</' . $filter->apply('cppress_widget_slider_tag', "div", $slides) . '>';