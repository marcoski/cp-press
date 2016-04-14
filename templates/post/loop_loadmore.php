<?php
	echo $filter->apply('cppress_widget_loop_row_before', '<div class="row">', $posts['wtitle']);
	if(!$template->issetTemplate($templateName)){
		while($wpQuery->have_posts()){
			$wpQuery->the_post();
			$thumbHtml = '';
			if(has_post_thumbnail() && (isset($posts['showthumbnail']) && $posts['showthumbnail'])){
				$thumbHtml .= $filter->apply('cppress_widget_post_thumb_container_before', '', get_post(), $posts['wtitle']);
				$thumbClasses = $filter->apply('cppress_widget_post_thumb_classes', array('img-responsive'), get_post(), $posts['wtitle']);
				$thumb = $filter->apply('cppress_widget_post_thumb', 'post-thumbnail', get_post(), $post['wtitle']);
				if(isset($posts['linkthumbnail']) && $posts['linkthumbnail']){
					$ltClasses = $filter->apply('cppress_widget_post_thumb_link_classes', array(), get_post(), $posts['wtitle']);
					$thumbHtml .= '<a href="' . get_the_permalink() . '" class="' . implode(' ', $ltClasses) . '" >';
				}
				$thumbHtml .= get_the_post_thumbnail(get_the_ID(), $thumb, array('class' => implode(' ', $thumbClasses)));
				if(isset($posts['linkthumbnail']) && $posts['linkthumbnail']){
					$thumbHtml .= '</a>';
				}
				$thumbHtml.= $filter->apply('cppress_widget_post_thumb_container_after', '', get_post(), $posts['wtitle']);
			}
			$itemClasses = $filter->apply('cppress_widget_loop_item_classes', array('col-md-' . $postWidth), $posts['wtitle']);
			echo '<div class="' . implode(' ', $itemClasses) . '">';
			echo $filter->apply('cppress_widget_loop_item_before', '', $posts['wtitle']);
			
			if($posts['linktitle']){
				$the_title_open_tag = $filter->apply('cppress_widget_post_the_title_open_tag',
						'<h3><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', get_post(), true, $posts['wtitle']
				);
				$the_title_close_tag = $filter->apply('cppress_widget_post_the_title_close_tag', '</a></h3>', get_post(), true, $posts['wtitle']);
			}else{
				$the_title_open_tag = $filter->apply('cppress_widget_post_the_title_open_tag', '<h3>', get_post(), false, $posts['wtitle']);
				$the_title_close_tag = $filter->apply('cppress_widget_post_the_title_close_tag', '</h3>', get_post(), false, $posts['wtitle']);
			}
			
			echo $filter->apply('cppress_widget_post_title_before', $thumbHtml, get_post(), $posts['wtitle']);
			echo $filter->apply(
					'cppress_widget_post_the_title',
					the_title($the_title_open_tag, $the_title_close_tag, false),
					$the_title_open_tag,
					$the_title_close_tag
			);
			echo $filter->apply('cppress_widget_post_title_after', '', get_post(), $posts['wtitle']);
			if(isset($posts['showinfo']) && $posts['showinfo']){
				$info = '<div class="single-info">';
				$info .= '<span class="date"><i class="icon-time"></i> ';
				$info .= get_the_time( get_option('date_format') );
				$info .= '</span>';
				$info .= $filter->apply('cppress_post_info_divider', ' | ');
				$info .= '<span class="author"><i class="icon-user"></i> ';
				$info .= get_the_author();
				$info .= '</span>';
				$info .= '</div>';
				echo $filter->apply('cppress_post_info', $info, get_post(), $posts['wtitle']);
			}
			echo $filter->apply('cppress_widget_post_content_before', '', get_post(), $posts['wtitle'], $thumbHtml);
			if(!isset($posts['hidecontent'])){
				if(isset($posts['showexcerpt']) && $posts['showexcerpt']){
					the_excerpt();
				}else{
					the_content();
				}
			}
			echo $filter->apply('cppress_widget_post_content_after', '', get_post(), $posts['wtitle'], $thumbHtml);
			echo $filter->apply('cppress_widget_loop_item_after', '', $posts['wtitle']);
			echo '</div>';
		}
	}else{
		echo $filter->apply(
				'cppress_widget_loop_loadmore_template_content',
				$template->inc(
						$templateName,
						array(
								'wpQuery' => $wpQuery,
								'options' => $posts,
						)
				),
				get_post(),
				$options
		);
	}
	
	echo $filter->apply('cppress_widget_loop_row_after', '</div>', $posts['wtitle']);