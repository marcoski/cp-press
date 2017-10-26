<?php 
use Commonhelp\Util\Inflector;

if(!function_exists('render_widget')){
	function render_widget($widgetsFactory, $instance, $widget, $section, $grid, $cell, $panel, $isFirst, $isLast, $postId=false){
		$widget = $widget['class'];
		if(isset($widgetsFactory[$widget])){
			$the_widget = $widgetsFactory[$widget];
			$the_widget = apply_filters( 'cppress_widget_object', $the_widget, $widget, $instance );
			
			$classes = array('cpwidget');
			if(!empty($the_widget) && !empty($the_widget->id_base)){
				$classes[] = 'cpwidget-'.$the_widget->id_base;
			}
			if($isFirst){
				$classes[] = 'cpwidget-first_child';
			}
			if($isLast){
				$classes[] = 'cpwidget-last-child';
			}
			$id = 'cpwidget-' . $postId . '-' . $section . '-' . $grid . '-' . $cell . '-' . $panel;
			$classes = apply_filters( 'cppress_layout_widget_classes', $classes, $widget, $instance);
			$classes = explode( ' ', implode( ' ', $classes ) );
			$classes = array_filter( $classes );
			$classes = array_unique( $classes );
			$classes = array_map( 'sanitize_html_class', $classes );
			$before = apply_filters('cppress_widget_before', '', $classes, $id, $instance, $section) . 
				apply_filters('cppress_widget_before_' . $the_widget->id_base, '', $classes, $id, $instance, $section);
			$after = apply_filters('cppress_widget_after', '', $classes, $id, $instance, $section) .
				apply_filters('cppress_widget_after_' . $the_widget->id_base, '', $classes, $id, $instance, $section);
			$args = array(
				'before_widget' => $before,
				'after_widget' => $after,
				'before_title' => apply_filters('cppress_widget_before_title', '', $instance, $section),
				'after_title' => apply_filters('cppress_widget_after_title', '', $instance, $section),
				'widget_id' => 'widget-' . $grid . '-' . $cell . '-' . $panel
			);
			$args = apply_filters('cppress_layout_widget_args', $args);
			if(!empty($the_widget) && is_a($the_widget, 'WP_Widget')){

				echo $the_widget->widget($args, $instance);
			}else{
				echo apply_filters('cppress_layout_missing_widget', $args['before_widget'] . $args['after_widget'], $widget, $args , $instance);
			}
			
		}
	}
}

if(!function_exists('get_style_attr')){
	function get_style_attr($styleArray){
		if(empty($styleArray)){
			return "";
		}
		$css = "";
		foreach($styleArray as $property => $value){
			$css .= $property.': '.$value.';';
		}

		return $css;
	}
}


echo $filter->apply('cppress_layout_before_content', "", $sections, $post->ID);

foreach($sections as $skey => $grids){
	$section = $grids['data'];
	$sectionClasses = $filter->apply('cppress_layout_section_classes', array('cp-section-' . $section['slug'], $section['slug']), $post->ID, $section);
	$sectionAttrs = $filter->apply('cppress_layout_section_attrs', array(
			'id' => 'cpsection-' . $post->ID . '-' . $skey,
			'class' => implode(' ', $sectionClasses)
	), $section);
	echo $filter->apply('cppress_layout_before_section', "", $section, $sectionAttrs);
	echo '<' . $filter->apply('cppress_layout_section_tag', "section", $section);
	foreach($sectionAttrs as $name => $value){
		echo ' ' . $name . '="' . $value . '"';
	} 
	echo '>';
	unset($grids['data']);
	if($section['title'] != ''){
		echo $filter->apply('cppress-layout_section_title', '', $section, $section['slug']);
	}
	echo $filter->apply('cppress_layout_grid_container_open', '<div class="container">', $section['slug']);
	foreach($grids as $gkey => $cells){
		$grid = $cells['data'];
		$gridClasses = $filter->apply(
				'cppress_layout_grid_classes', array_merge(array('row'), $grid['classes']), $post->ID, $grid, $section['slug']);
		$gridAttrs = $filter->apply('cppress_layout_grid_attrs', array(
				'class' => implode(' ', $gridClasses)
		), $grid, $section['slug']);
		$gridStyle = get_style_attr($filter->apply('cppress_layout_grid_style', $grid['style'], $grid, $section['slug']));
		if($gridStyle !==  ''){
			$gridAttrs['style'] = $gridStyle;
		}
		echo $filter->apply('cppress_layout_before_grid', '', $grid, $gridAttrs, $section['slug']);
		if($filter->apply('cppress_layout_print_grid_open', true, $grid, $section['slug'])) {
			echo '<div';
			foreach ( $gridAttrs as $name => $value ) {
				echo ' ' . $name . '="' . $value . '"';
			}
			echo '>';
			echo $filter->apply('cppress_layout_before_grid_container', "", $grid, $gridAttrs, $section['slug']);
		}
		unset($cells['data']);
		foreach($cells as $ckey => $widgets){
			$cell = $widgets['data'];
			$cClasses = $cell['classes'];
			$cClasses[] = 'col-md-'.$cell['weight'];
			$cellClasses = $filter->apply('cppress_layout_cell_classes', $cClasses, $post->ID, $cell, $section['slug']);
			$cellAttrs = $filter->apply('cppress_layout_cell_attrs', array(
					'class' => implode(' ', $cellClasses)
			), $cell, $section['slug']);
			$cellStyle = get_style_attr($filter->apply('cppress_layout_cell_style', $cell['style'], $cell, $section['slug']));
			if($cellStyle !==  ''){
				$cellAttrs['style'] = $cellStyle;
			}
			if($filter->apply('cppress_layout_print_cell_open', true, $cell, $section['slug'])) {
				echo $filter->apply( 'cppress_layout_before_cell', "", $cell, $cellAttrs, $section['slug'] );
				echo '<div';
				foreach ( $cellAttrs as $name => $value ) {
					echo ' ' . $name . '="' . $value . '"';
				}
				echo '>';
			}
			
			unset($widgets['data']);
			echo $filter->apply('cppress_layout_widget_before', '', $cell, $section['slug']);
			foreach($widgets as $wkey => $widget){
				render_widget($widgetsFactory, $widget, $widget['widget_info'], $skey, $gkey, $ckey, $wkey, $wkey == 0, $wkey == count( $widgets ) - 1, $post->ID);
			}
			echo $filter->apply('cppress_layout_widget_after', '', $cell, $section['slug']);
			if($filter->apply('cppress_layout_print_grid_close', true, $cell, $section['slug'])) {
				echo '</div>';
				echo $filter->apply( 'cppress_layout_after_cell', "", $cell, $cellAttrs, $section['slug'] );
			}
		}
		echo $filter->apply('cppress_layout_after_grid_container', "", $grid, $gridAttrs, $section['slug']);
		if($filter->apply('cppress_layout_print_grid_close', true, $grid, $section['slug'])) {
			echo '</div>';
			echo $filter->apply( 'cppress_layout_after_grid', "", $grid, $gridAttrs, $section['slug'] );
		}
	}
	echo $filter->apply('cppress_layout_grid_container_close', '</div>', $section['slug']);
	
	echo '</' . $filter->apply('cppress_layout_section_tag', "section", $section) . '>';
	echo $filter->apply('cppress_layout_after_section', "", $section, $sectionAttrs);
}

echo $filter->apply('cppress_layout_after_content', "", $sections, $post->ID);
