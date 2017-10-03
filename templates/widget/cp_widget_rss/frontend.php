<?php 
	echo $args['before_widget'];
	
	if(isset($instance['showtitle']) && $instance['showtitle']){
		echo $filter->apply('cppress_widget_the_title',
				'<h1>' .$instance['wtitle'].'</h1>', $instance['wtitle']);
	}
	
	$atomContainerClasses = $filter->apply('cppress_widget_rss_container_classes', array('cp-widget-rss'), $instance['wtitle']);
	$atomContainerAttrs = $filter->apply('cppress_widget_rss_container_attrs', array(
			'class' => implode(' ', $atomContainerClasses),
	), $instance['wtitle']);
	echo '<div ';
	foreach($atomContainerAttrs as $name => $value){
		echo ' ' . $name . '="' . $value .'"';
	}
	echo 	'>';
	$atomListClasses = $filter->apply('cppress_widget_rss_list_classes', array('cp-widget-rss'), $instance['wtitle']);
	$atomListAttrs = $filter->apply('cppress_widget_rss_list_attrs', array(
			'class' => implode(' ', $atomListClasses),
	), $instance['wtitle']);
	echo '<' . $filter->apply('cppress_widget_rss_list_tag', 'ul', $instance['wtitle']);
	foreach($atomListAttrs as $name => $value){
		echo ' ' . $name . '="' . $value .'"';
	}
	echo '>';
	if(!empty($items)){
		$items = $feeds->getItems();
		for($i=0; $i<$instance['rssitems']; $i++){
			$atomItemClasses = $filter->apply('cppress_widget_rss_item_classes', array(), $i, $items[$i], $instance['wtitle']);
			$atomListAttrs = $filter->apply('cppress_widget_rss_item_attrs', array(
					'class' => implode(' ', $atomItemClasses),
			));
			echo '<' . $filter->apply('cppress_widget_rss_item_tag', 'li', $i, $items[$i], $instance['wtitle']);
			foreach($atomListAttrs as $name => $value){
				echo ' ' . $name . '="' . $value .'"';
			}
			echo '>';
			echo $filter->apply('cppress_widget_rss_item_the_title', 
					'<h4><a href="' . $items[$i]->getUrl() . '">' . $items[$i]->getTitle() . '</a></h4>', 
					'<h4>', '</h4>', $items[$i]->getTitle(), $items[$i]->getUrl());
			if(isset($instance['showdate']) && $instance['showdate']){
				echo $filter->apply('cppress_widget_rss_item_the_date', '', $items[$i]->getDate());
			}
			if(isset($instance['showauthor']) && $instance['showauthor']){
				echo $filter->apply('cppress_widget_rss_item_the_author', '', $items[$i]->getAuthor());
			}
			echo $filter->apply('cppress_widget_rss_item_the_content', $items[$i]->getContent());
			echo '</' . $filter->apply('cppress_widget_rss_item_tag', 'li', $i, $items[$i], $instance['wtitle']) . '>';
		}
	}
	
	echo '</' . $filter->apply('cppress_widget_rss_list_tag', 'ul', $instance['wtitle']) . '>';
	echo '</div>';
	
	echo $args['after_widget'];
?>