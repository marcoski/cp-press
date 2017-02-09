<?php
echo $args['before_widget'];
if(!$template_theme->issetTemplate($templateName)) {
	$mainClass = $instance['maincontainerclass'] != '' ? 'class="' . $instance['maincontainerclass'] . '"' : '';
	echo $filter->apply( 'cppress_widget_text_maincontainer_open', '<div ' . $mainClass . '>', $instance['wtitle'] );
	if ( ! isset( $instance['iconposition'] ) || $instance['iconposition'] === 'top' ) {
		echo $template->inc( '/templates/widget/widget-parts/frontend/icon', array(
			'instance' => $instance,
			'filter'   => $filter
		) );
	}
	$textContainerOpen  = '';
	$textContainerClose = '';
	if ( isset( $instance['containerclass'] ) && $instance['containerclass'] !== '' ) {
		$textContainerOpen  = '<div class="' . $instance['containerclass'] . '">';
		$textContainerClose = '</div>';
	}
	echo $filter->apply( 'cppress_widget_text_container_open', $textContainerOpen, $instance );
	if ( isset( $instance['showtitle'] ) && $instance['showtitle'] ) {
		if ( isset( $instance['iconposition'] ) && $instance['iconposition'] === 'before-title' ) {
			echo $template->inc( '/templates/widget/widget-parts/frontend/icon', array(
				'instance' => $instance,
				'filter'   => $filter
			) );
		}
		if ( isset( $instance['linktitle'] ) ) {
			echo $filter->apply( 'cppress_widget_the_title',
				'<h1><a href="' . $instance['link'] . '">' . $instance['wtitle'] . '</a>', '', $instance );
		} else {
			$title = $filter->apply( 'cppress_widget_the_title',
				'<h1>' . $instance['wtitle'] . '</h1>', $instance['wtitle'] );
			echo $filter->apply( 'cppress_widget_text_the_title',
				$title, $instance['wtitle'], $instance );
		}
		if ( isset( $instance['iconposition'] ) && $instance['iconposition'] === 'after-title' ) {
			echo $template->inc( '/templates/widget/widget-parts/frontend/icon', array(
				'instance' => $instance,
				'filter'   => $filter
			) );
		}
	}
	if ( isset( $instance['iconposition'] ) && $instance['iconposition'] === 'before-content' ) {
		echo $template->inc( '/templates/widget/widget-parts/frontend/icon', array(
			'instance' => $instance,
			'filter'   => $filter
		) );
	}
	echo $filter->apply( 'cppress_widget_text_before_the_content', '', $instance );
	$text = $instance['text'];

	echo $filter->apply( 'cppress_widget_text_the_content', $text, $instance );
	echo $filter->apply( 'cppress_widget_text_after_the_content', '', $instance );
	if ( isset( $instance['iconposition'] ) && $instance['iconposition'] === 'after-content' ) {
		echo $template->inc( '/templates/widget/widget-parts/frontend/icon', array(
			'instance' => $instance,
			'filter'   => $filter
		) );
	}
	if ( isset( $instance['linkbutton'] ) ) {
		$button         = '<a class="btn btn-default" href="' . $instance['link'] . '">%s</a>';
		$linkButtonText = $filter->apply( 'cppress_widget_text_read_more', __( 'Read more', 'cppress' ) );
		if ( isset( $instance['linkbuttontext'] ) && $instance['linkbuttontext'] !== '' ) {
			$linkButtonText = $instance['linkbuttontext'];
		}
		echo sprintf( $button, $linkButtonText );
	}
	echo $filter->apply( 'cppress_widget_text_container_close', $textContainerClose, $instance['containerclass'], $instance );
	if ( isset( $instance['iconposition'] ) && $instance['iconposition'] === 'bottom' ) {
		echo $template->inc( '/templates/widget/widget-parts/frontend/icon', array(
			'instance' => $instance,
			'filter'   => $filter
		) );
	}
	echo $filter->apply( 'cppress_widget_text_maincontainer_close', '</div>', $instance['wtitle'] );
}else{
	echo $filter->apply(
		'cppress_widget_text_template_content',
		$template_theme->inc(
			$templateName,
			array(
				'instance' => $instance,
			)
		),
		get_post()
	);
}

echo $args['after_widget'];