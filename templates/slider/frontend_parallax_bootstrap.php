<?php 
	$options = $filter->apply('cppress_widget_slider_options', $options, $slides);
	
	if($slides['displayoverlay']){
		echo '<div class="parallax-overlay"></div>';
	}
	$parallaxClasses = $filter->apply('cppress_widget_slider_parallax_main_classes', array('parallax-content text-center'), $slides);
	echo '<div class="' . implode(' ', $parallaxClasses) .'">';	
	echo '<div class="container">';
	echo $filter->apply('cppress_widget_slider_parallax_logo', '', $slides);
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
	echo '<div class="carousel-inner">';
	foreach($slides as $i => $slide){
		$active = ''; if($i==0){ $active = 'active'; }
			echo '<div class="item '. $active . '">';
			/**
			 * SLIDE CONTENT MARKUP START
			 */
			$parallaxClasses = $filter->apply('cppress_widget_slider_parallax_classes', array(), $slide);
			$parallaxAttrs = $filter->apply('cppress_widget_slider_parallax_attrs', array(), $slide);
			echo '<' .$filter->apply('cppress_widget_slider_parallax_tag', "h1", $slide) ;
			foreach($imageAttrs as $name => $value){
				echo ' ' . $name . '="' . $value . '"';
			}
			echo $filter->apply('cppress_widget_slider_parallax_text', $slide);
			echo '</' . $filter->apply('cppress_widget_parallax_tag', "h1", $slide) . '>';
			/**
			 * SLIDE CONTENT MARKUP END
			 */
			echo '</div>';
			
	}
	echo '</div>';
	echo '</' . $filter->apply('cppress_widget_slider_tag', "section", $slides) . '>';
	if($slides['displaytitle']){
		$stitleClasses = $filter->apply('cppress_widget_slider_parallax_subtitle_classes', array('slide-btm-text'), $slides);
		$stitleAttrs = $filter->apply('cppress_widget_slider_parallax_subtitle_attrs', array(), $slides);
		echo '<' . $filter->apply('cppress_widget_slider_parallax_subtitle_tag', "h2", $slides);
		foreach($stitleAttrs as $name => $value){
			echo ' ' . $name . '="' . $value . '"';
		}
		echo $slides['subtitle'];
		echo '</' . $filter->apply('cppress_widget_slider_parallax_subtitle_tag', "section", $slides) . '>';
	}
	if($slides['nextlink'] != ''){
		$nextClasses = $filter->apply('cppress_widget_slider_parallax_next_classes', array('next-link text-center'), $slides);
		$nextAttrs = $filter->apply('cppress_widget_slider_parallax_next_attrs', array(), $slides);
		echo '<' . $filter->apply('cppress_widget_slider_parallax_next_tag', "div", $slides);
		foreach($nextAttrs as $name => $value){
			echo ' ' . $name . '="' . $value . '"';
		}
		echo '<p class="srcollto">';
		$nextAClasses = $filter->apply('cppress_widget_slider_parallax_next_aclasses', array('btn btn-lg btn-theme-color'), $slides);
		echo '<a href="#' . $slides['nextlink'] .'" class="'. implode(' ', $nextAClasses) . '">';
		echo $filter->apply('cppress_widget_slider_parallax_next_text', "Start", $slides);
		echo '</a>';
		echo '</p>';
		echo '</' . $filter->apply('cppress_widget_slider_parallax_subtitle_tag', "section", $slides) . '>';
	}
	echo '</div>';
	echo '</div>';