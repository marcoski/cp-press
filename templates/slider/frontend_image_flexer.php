<?php 
	$options = $filter->apply('cppress_widget_slider_options', $options, $slides);
	$sliderClasses = $filter->apply('cppress_widget_slider_classes', array('flexslider'), $slides, $options);
	$sliderId = $filter->apply('cppress_widget_slider_id', 'cppress-carousel-'.md5(serialize($slides)), $slides, $options);
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
	foreach($slides as $i => $slide){
        if(!$template->issetTemplate($templateName)) {
            $active = '';
            if ($i == 0) {
                $active = $filter->apply('cppress_widget_slider_flexactiveclass', '', $slide);
            }
            echo '<li ' . $active . '>';
            /**
             * SLIDE CONTENT MARKUP START
             */
            if ($options['link'] == 'slide' && !empty($slide['link'])) {
                $new = $slide['link']['isext'] ? 'target="_new"' : '';
                echo $filter->apply('cppress_widget_slider_linkwrapper_open', '<a href="' . $slide['link']['url'] . '" ' . $new . '>', $slide['link']['isext'], $slide);
            }

            if (is_object($slide['img'])) {
                $itemClassArray = array('cp-embed-responsive');
                $divClasses = $filter->apply('cppress_widget_slider_embed_classes', $itemClassArray, $slide, $options);
                $divAttrArray = array(
                    'class' => implode(' ', $divClasses),
                );
                $divAttrs = $filter->apply('cppress_widget_slider_embed_classes', $divAttrArray, $slide);
                echo '<div ';
                foreach ($divAttrs as $name => $value) {
                    echo ' ' . $name . '="' . $value . '"';
                }
                echo '>';
                echo $slide['img']->html;
                echo '</div>';
                if ($slide['displaytitle']) {
                    echo '<h4 class="main">' . $slide['title'] . '</h4>';
                }
                if ($slide['displaycontent']) {
                    echo '<p class="description">' . $slide['content'] . '</p>';
                }
            } else {
                $imageClasses = $filter->apply('cppress_widget_slider_image_classes', array(), $slide);
                $imageAttrs = $filter->apply('cppress_widget_slider_image_attrs', array(
                    'src' => $slide['img']['src'][0],
                    'alt' => $slide['img']['title']
                ), $slide);
                echo '<img ';
                foreach ($imageAttrs as $name => $value) {
                    echo ' ' . $name . '="' . $value . '"';
                }
                echo ' />';
                if ($slide['displaytitle'] || $slide['displaycontent']) {
                    $captionClasses = $filter->apply('cppress_widget_slider_caption_classes', array('caption', 'caption-align-' . $slide['captionalign']), $slide, $options);
                    echo '<p class="' . implode(' ', $captionClasses) . '">';
                }
                if ($slide['displaytitle']) {
                    echo $filter->apply('cppress_widget_slider_title', '<span class="main">' . $slide['title'] . '</span>', $slide);
                }
                if ($slide['displaycontent']) {
                    $content = $slide['content'];
                    $content = preg_replace('#<p(.*?)>(.*?)</p>#is', '$2<br/>', $content);
                    echo '<br>';
                    echo $filter->apply('cppress_widget_slider_content', '<span class="secondary clearfix">' . $slide['content'] . '</span>', $slide);
                }
                if ($slide['displaytitle'] || $slide['displaycontent']) {
                    echo '</p>';
                }
            }

            if ($options['link'] == 'slide' && !empty($slide['link'])) {
                echo $filter->apply('cp_widget_slider_linkwrapper_close', '</a>', $slide);
            }
            /**
             * SLIDE CONTENT MARKUP END
             */
            echo '</li>';
        }else{
            echo $filter->apply(
                'cppress_widget_slider_image_template_content',
                $template->inc(
                    $templateName,
                    array(
                        'slide' => $slide,
                        'active' => $active,
                    )
                ),
                get_post(),
                $options
            );
        }
	}
	echo '</ul>';
	echo '</' . $filter->apply('cppress_widget_slider_tag', "div", $slides, $options) . '>';
	echo $filter->apply('cppress_widget_slider_after', '', $slides, $options, $sliderId);