<?php 
	echo $args['before_widget'];
	if(isset($instance['showtitle']) && $instance['showtitle']){
		echo $filter->apply('cppress_widget_the_title',
				'<h1>' .$instance['wtitle'].'</h1>', $instance['wtitle']);
	}
	echo $filter->apply('cppress_widget_portfolio_row_before', '<div class="row cp-widget-portfolio">', $instance['wtitle']);
	
	while($wpQuery->have_posts()){
		$wpQuery->the_post();
		$itemClasses = $filter->apply('cppress_widget_portfolio_item_classes', array('col-md-' . $instance['rowclass']), $instance['wtitle']);
		echo '<div class="' . implode(' ', $itemClasses) . '">';
		echo $filter->apply('cppress_widget_portfolio_item_before', '', $instance['wtitle']);
		if(has_post_thumbnail()){
			$thumbClasses = $filter->apply('cppress_widget_portfolio_thumb_classes', array('caption'), get_post(), $instance['wtitle']);
			echo '<' . $filter->apply('cppress_widget_portfolio_thumb_tag', 'figure', $instance['wtitle'], get_post()). ' ';
			echo 'class="' . implode(' ', $thumbClasses) . '">';
			if(isset($instance['linkthumbnail']) && $instance['linkthumbnail']){
				$ltClasses = $filter->apply('cppress_widget_post_thumb_link_classes', array(), get_post(), $instance['wtitle']);
				echo '<a href="' . get_the_permalink() . '" class="' . implode(' ', $ltClasses) . '" >';
			}
			the_post_thumbnail('post-thumbnail');
			if(isset($instance['linkthumbnail']) && $instance['linkthumbnail']){
				echo '</a>';
			}
			$captionClasses = $filter->apply('cppress_widget_portfolio_caption_classes', array(), get_post(), $instance['wtitle']);
			echo '<' . $filter->apply('cppress_widget_portfolio_caption_tag', 'figcaption', $instance['wtitle'], get_post()). ' ';
			echo 'class="' . implode(' ', $captionClasses) . '">';
			if(isset($instance['linktitle']) && $instance['linktitle']){
				$the_title_open_tag = $filter->apply('cppress_widget_portfolio_the_title_open_tag',
						'<h3><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', get_post(), true, $instance['wtitle']
				);
				$the_title_close_tag = $filter->apply('cppress_widget_portfolio_the_title_close_tag', '</a></h3>', get_post(), true, $instance['wtitle']);
			}else{
				$the_title_open_tag = $filter->apply('cppress_widget_portfolio_the_title_open_tag', '<h3>', get_post(), false, $instance['wtitle']);
				$the_title_close_tag = $filter->apply('cppress_widget_portfolio_the_title_close_tag', '</h3>', get_post(), false, $instance['wtitle']);
			}
			echo $filter->apply(
					'cppress_widget_poportflio_the_title',
					the_title($the_title_open_tag, $the_title_close_tag, false),
					$the_title_open_tag,
					$the_title_close_tag
			);
			echo $filter->apply('cppress_widget_portfolio_item_before_content', '<div>', $instance['wtitle']);
			$itemContentClasses = $filter->apply('cppress_widget_item_content_classes', array(), $instance['wtitle']);
			echo '<' . $filter->apply('cppress_widget_portfolio_item_content_tag', 'span', $instance['wtitle']). ' ';
			echo 'class="' . implode(' ', $itemContentClasses) . '">';
			the_excerpt();
			echo '</' . $filter->apply('cppress_widget_portfolio_item_content_tag', 'span', $instance['wtitle'], get_post()) . '>';
			echo $filter->apply('cppress_widget_portfolio_item_link_before', '', $instance['wtitle']);
			echo $filter->apply('cppress_widget_portfolio_item_link', 
					'<a href="' . get_the_permalink() . '" class="btn">'. __('Take a look', 'cppress') . '</a>', $instance['wtitle']
			);
			echo $filter->apply('cppress_widget_portfolio_item_link_after', '', $instance['wtitle']);
			echo $filter->apply('cppress_widget_portfolio_item_after_content', '</div>', $instance['wtitle']);
			echo '</' . $filter->apply('cppress_widget_portfolio_caption_tag', 'figure', $instance['wtitle'], get_post()) . '>';
		}
		echo $filter->apply('cppress_widget_portfolio_item_after', '', $instance['wtitle']);
		echo '</div>';
	}
	
	echo $filter->apply('cppress_widget_portfolio_row_after', '</div>', $instance['wtitle']);
	echo $args['after_widget'];
?>