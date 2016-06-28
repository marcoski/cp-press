<?php

	echo $args['before_widget'];
	if(isset($instance['showtitle']) && $instance['showtitle']){
		echo $filter->apply('cppress_widget_the_title',
				'<h1>' .$instance['wtitle'].'</h1>', $instance['wtitle']);
	}
	while($wpQuery->have_posts()){
		$wpQuery->the_post();
		$articleClasses = $filter->apply('cppress_widget_news_classes', array($instance['iconclass']), get_post(), $instance);
		echo '<article class="' . implode( ' ', $articleClasses ) . '">';
		if(isset($instance['icon']) && $instance['icon'] != ''){
			echo $filter->apply('cppress_widget_icon_container_open', '', $instance);
				$icon = $filter->apply('cppress_widget_icon', $instance['icon'], $instance);
				echo '<i class="' . $icon . '"></i>';
			echo $filter->apply('cppress_widget_icon_container_close', '', $instance);
		}
		if(isset($instance['linktitle']) && $instance['linktitle']){
			$the_title_open_tag = $filter->apply('cppress_widget_news_the_title_open_tag',
					'<h2><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', get_post(), true, $instance['wtitle']
			);
			$the_title_close_tag = $filter->apply('cppress_widget_news_the_title_close_tag', '</a></h2>', get_post(), true, $instance['wtitle']);
		}else{
			$the_title_open_tag = $filter->apply('cppress_widget_news_the_title_open_tag', '<h2>', get_post(), false, $instance['wtitle']);
			$the_title_close_tag = $filter->apply('cppress_widget_news_the_title_close_tag', '</h2>', get_post(), false, $instance['wtitle']);
		}
		
		echo $filter->apply('cppress_widget_news_title_before', '', get_post(), $instance['wtitle']);
		echo $filter->apply(
				'cppress_widget_news_the_title',
				the_title($the_title_open_tag, $the_title_close_tag, false),
				$the_title_open_tag,
				$the_title_close_tag
		);
		echo $filter->apply('cppress_widget_news_title_after', '', get_post(), $posts['wtitle']);
		echo '<div class="single-info">';
			echo '<i class="icon-time"></i> ';
			the_time(get_option( 'date_format' ));
			echo '</span>';
		echo '</div>';
		if(!isset($instance['hidecontent'])){
			if(isset($instance['showexcerpt']) && $instance['showexcerpt']){
				echo '<p>';
				the_excerpt();
				echo '</p>';
				echo $filter->apply(
						'cppress_widget_news_readmore',
						'<a href="' . get_the_permalink() . '" rel="bookmark" class="btn btn-default">' . __('Read more', 'cppress') . '</a>',
						get_post()
				);
			}else{
				the_content();
			}
		}
		echo '</article>';
	}
	
	wp_reset_postdata();
	echo $args['after_widget'];
