<?php
	echo $args['before_widget'];
	if(isset($instance['showtitle']) && $instance['showtitle']){
		echo $filter->apply('cppress_widget_the_title',
				'<h1>' .$instance['wtitle'].'</h1>', $instance['wtitle']);
	}
	echo wp_nav_menu(array( 
			'menu' => $instance['navmenu'], 
			'container' => $filter->apply('cppress_widget_menu_container', 'div', $instance['wtitle']), 
			'container_class' => $filter->apply('cppress_widget_menu_container_class', '', $instance['wtitle']), 
			'container_id' => $filter->apply('cppress_widget_menu_container_id', '', $instance['wtitle']), 
			'menu_class' => $filter->apply('cppress_widget_menu_class', 'menu', $instance['wtitle']), 
			'menu_id' => $filter->apply('cppress_widget_menu_id', '', $instance['wtitle']),
			'echo' => false, 
			'fallback_cb' => $filter->apply('cppress_widget_menu_fallback_cb', 'wp_page_menu', $instance['wtitle']), 
			'before' => $filter->apply('cppress_widget_menu_before', '', $instance['wtitle']), 
			'after' => $filter->apply('cppress_widget_menu_after', '', $instance['wtitle']), 
			'link_before' => $filter->apply('cppress_widget_menu_link_before', '', $instance['wtitle']), 
			'link_after' => $filter->apply('cppress_widget_menu_link_after', '', $instance['wtitle']), 
			'items_wrap' => $filter->apply('cppress_widget_menu_items_wrap', '<ul id="%1$s" class="%2$s">%3$s</ul>', $instance['wtitle']),
			'depth' => $filter->apply('cppress_widget_menu_depth', 0, $instance['wtitle']), 
			'walker' => $filter->apply('cppress_widget_menu_walker', '', $instance['wtitle']),'', 
			'theme_location' => $filter->apply('cppress_widget_menu_theme_location', '', $instance['wtitle']),
	));
	
	echo $args['after_widget'];
?>