<?php 
	$options = $filter->apply('cppress_widget_slider_options', $options, $slides);
	$sliderClasses = $filter->apply('cppress_widget_slider_classes', array('flexslider'), $slides);
	$sliderId = $filter->apply('cppress_widget_slider_id', 'cppress-carousel-'.md5(serialize($slides)), $slides);
	$sliderAttrs = $filter->apply('cppress_widget_slider_attrs', array(
			'id' => $sliderId,
			'class' => implode(' ', $sliderClasses),
			'data-interval' => $options['timeout'],
			'data-speed' => $options['speed'],
			'data-navcolor' => $filter->apply('cppress_widget_slider_navcolor', '', $options['navcolor'])
	), $slides);
	echo $filter->apply('cppress_widget_slider_before', '', $slides, $options, $sliderId);
	echo '<' . $filter->apply('cppress_widget_slider_tag', "div", $slides, $options);
	foreach($sliderAttrs as $name => $value){
		echo ' ' . $name . '="' . $value . '"';
	}
	echo '>';
	echo '<ul class="slides">';
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
		if(($i % $pColumn) == 0){
			echo '<li class="item '. $active . '">';
		}
		$itemClasses = $filter->apply('cppress_widget_slider_post_item_classes', 
					array('col-sm-' . $col['sm'], 'col-md-' . $col['md'], 'col-lg-' . $col['lg']), $slides, $options);
		echo '<div class="' . implode(' ', $itemClasses) . '">';
		if(!$template->issetTemplate($templateName)){
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
		}else{
			echo $filter->apply(
				'cppress_widget_slider_post_template_content',
				$template->inc(
						$templateName,
						array(
								'posts' => $posts,
								'active' => $active,
						)
				),
				get_post(),
				$options
			);
		}
		echo '</div>';
		if(($i % $pColumn) == ($pColumn-1)){
			echo '</li>';
		}
			
		$i++;
	}
	wp_reset_postdata();
	echo '</ul>';
	echo '</' . $filter->apply('cppress_widget_slider_tag', "div", $slides, $options) . '>';
	echo $filter->apply('cppress_widget_slider_after', '', $slides, $options, $sliderId);