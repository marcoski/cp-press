<?php 
	$options = $filter->apply('cppress_widget_slider_options', $options, $wpQuery);
	$sliderClasses = $filter->apply('cppress_widget_slider_classes', array('carousel', 'slide'), $wpQuery);
	$sliderId = $filter->apply('cppress_widget_slider_id', 'cppress-carousel-'.md5(serialize($slides)), $slides);
	$sliderAttrs = $filter->apply('cppress_widget_slider_attrs', array(
			'id' => $sliderId,
			'class' => implode(' ', $sliderClasses),
			'data-interval' => $options['timeout']
	), $slides);
	
	echo '<' . $filter->apply('cppress_widget_slider_tag', "section", $wpQuery);
	foreach($sliderAttrs as $name => $value){
		echo ' ' . $name . '="' . $value . '"';
	}
	echo '>';
	if(!$options['hideindicators']){
		echo '<ol class="carousel-indicators">';
		for($i=0; $i<$wpQuery->found_posts; $i++){
			$active = ''; if($i==0){ $active = 'class="active"'; }
			echo '<li 
					' . $filter->apply('cppress_widget_slider_navcolor', '', $options['navcolor']) . '
					data-target="#' . $sliderAttrs['id'] .'" 
					data-slide-to="' . $i . '" ' . $active . '>
				</li>';
		}
		echo '</ol>';
	}
	echo '<div class="carousel-inner">';
	$i=0;
	while($wpQuery->have_posts()){
		$wpQuery->the_post();
		$active = ''; if($i==0){ $active = 'active'; }
		$width = array(
			'content' => 12
		);
		if(has_post_thumbnail()){
			$width = $filter->apply('cppress_widget_slider_post_column_width', array('content' => 6, 'thumb' => 6));
		}
		echo '<article class="item '. $active . '">';
			echo '<div class="row">';
			if(has_post_thumbnail() && (isset($posts['showthumbnail']) && $posts['showthumbnail'])){
				echo '<div class="col-md-' . $width['thumb'] . ' col-md-push-' . $width['thumb'] . '">';
				$thumbClasses = $filter->apply('cppress_widget_slider_post_thumb_classes', array(
					'img-responsive',
					'hidden-xs',
					'hidden-sm'
				));
				$thumb = $filter->apply('cppress_widget_slider_post_thumb', 'post-thumbnail');
				if(isset($posts['linkthumbnail']) && $posts['linkthumbnail']){
					echo '<a href="' . get_the_permalink() . '" >'; 
				}
				the_post_thumbnail($thumb, array('class' => implode(' ', $thumbClasses)));
				if(isset($posts['linkthumbnail']) && $posts['linkthumbnail']){
					echo '</a>';
				}
					echo '</div>';
				}
				echo '<div class="col-md-' . $width['content'] . ' col-md-pull-' . $width['content'] . '">';
					echo '<div class="carousel-caption">';
					if($posts['linktitle']){
						$the_title_open_tag = $filter->apply('cppress_widget_slider_post_the_title_open_tag', 
							'<h2><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">'
						);
						$the_title_close_tag = $filter->apply('cppress_widget_slider_post_the_title_close_tag', '</a></h2>');
					}else{
						$the_title_open_tag = $filter->apply('cppress_widget_slider_post_the_title_open_tag', '<h2>');
						$the_title_close_tag = $filter->apply('cppress_widget_slider_post_the_title_close_tag', '</h2>');
					}
					echo $filter->apply(
						'cppress_widget_slider_post_the_title',
						the_title($the_title_open_tag, $the_title_close_tag, false),
						$the_title_open_tag,
						$the_title_close_tag
					);
					if(isset($posts['showinfo']) && $posts['showinfo']){
						$info = '<div class="single-info">';
							$info .= '<span class="date"><i class="icon-time"></i>';
							$info .= get_the_time();
							$info .= '</span>';
							$info .= $filter->apply('cppress_post_info_divider', ' | ');
							$info .= '<span class="author"><i class="icon-user"></i>';
							$info .= get_the_author();
							$info .= '</span>';
							echo $filter->apply('cppress_post_info', $info);		
						echo '</div>';
					}
					if(isset($posts['showexcerpt']) && $posts['showexcerpt']){
						the_excerpt();
					}else{
						the_content();
					}
					
					if(isset($potst['showmeta']) && $posts['showmeta']){
						echo '<p class="postmetadata">';
						echo __( 'Posted in', 'cppress') . ' ' . the_category( ', ' );
						echo '</p>';
					}
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</article>';
			
			$i++;
	}
	wp_reset_postdata();
	echo '</div>';
	
	if(!$options['hidecontrol']){
		echo '<a class="left carousel-control" href="#' . $sliderId . '" role="button" data-slide="prev">';
		$leftClasses = $filter->apply(
				'cppress_slider_bootstrap_control_left_classes',
				array('glyphicon glyphicon-chevron-left'),
				$sliderId,
				$slides
		);
		echo '<span class="' . implode(' ', $leftClasses) . '" aria-hidden="true"></span>';
		echo '<span class="sr-only">' . __('Previous', 'cppress') . '</span>';
		echo '</a>';
		echo '<a class="right carousel-control" href="#' . $sliderId . '" role="button" data-slide="next">';
		$rightClasses = $filter->apply(
				'cppress_slider_bootstrap_control_left_classes',
				array('glyphicon glyphicon-chevron-right'),
				$sliderId,
				$slides
		);
		echo '<span class="' . implode(' ', $rightClasses) . '" aria-hidden="true"></span>';
		echo '<span class="sr-only">' . __('Next', 'cppress') . '</span>';
		echo '</a>';
	}
	
	echo '</' . $filter->apply('cppress_widget_slider_tag', "section", $slides) . '>';