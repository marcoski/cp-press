<?php 
/**
 * @param $timestamp
 *
 * @return string|void
 */
function timeago( $timestamp ) {
	$diff = time() - (int) $timestamp;
	if ($diff == 0)
		return __( 'just now', "cppress" );
	$intervals = array
	(
			1                   => array( 'year',    31556926 ),
			$diff < 31556926    => array( 'month',   2628000 ),
			$diff < 2629744     => array( 'week',    604800 ),
			$diff < 604800      => array( 'day',     86400 ),
			$diff < 86400       => array( 'hour',    3600 ),
			$diff < 3600        => array( 'minute',  60 ),
			$diff < 60          => array( 'second',  1 )
	);
	$value = floor( $diff / $intervals[1][1] );
	$time_unit = $intervals[1][0];
	switch($time_unit) {
		case 'year':
			return sprintf( _n( '1 year ago', '%d years ago', $value, "cppress" ), $value );
			break;
		case 'month':
			return sprintf( _n( '1 month ago', '%d months ago', $value, "cppress" ), $value );
			break;
		case 'week':
			return sprintf( _n( '1 week ago', '%d weeks ago', $value, "cppress" ), $value );
			break;
		case 'day':
			return sprintf( _n( '1 day ago', '%d days ago', $value, "cppress" ), $value );
			break;
		case 'hour':
			return sprintf( _n( '1 hour ago', '%d hours ago', $value, "cppress" ), $value );
			break;
		case 'minute':
			return sprintf( _n( '1 minute ago', '%d minutes ago', $value, "cppress" ), $value );
			break;
		case 'second':
			return sprintf( _n( '1 second ago', '%d seconds ago', $value, "cppress" ), $value );
			break;
		default:
			return sprintf( __( 'Some time ago', "cppress" ) );
			break;
	}


}
echo $args['before_widget'];

$numbers = 5;
if(isset($instance['numberofposts']) && $instance['numberofposts'] !== ''){
	$numbers = $instance['numberofposts'];
}

$excerptLength = 140;
if(isset($instance['excerptlength']) && $instance['excerptlength'] !== ''){
	$excerptLength = $instance['excerptlength'];
}

echo $filter->apply('cppress_widget_facebook_posts_before', '', $instance['wtitle']);

if($posts && !empty($posts)){
	$posts = array_slice($posts, 0, $numbers);
	foreach($posts as $p){
		$shortened = false;
		$content = $p['content'];
		
		if(strlen($content) > $excerptLength){
			$limit = strpos($content, ' ', $excerptLength);
			if($limit){
				$content = substr($content, 0, $limit);
				$shortened = true;
			}
		}
		echo '<' . $filter->apply('cppress_widget_fb_tag', 'div', $instance);
		echo ' class="' . implode(' ', $filter->apply('cppress_widget_fb_classes', array('cpfb-post'))) . '"';
		echo '>';
		
		/** TITLE SECTION */
		echo '<h4 class="cpfb-heading"><a class="cpfb-link" href="' . $p['link'] . '" rel="external nofollow" target="_new">';
		echo $p['name'];
		echo '</a></h4>';
		
		/** CONTENT SECTION */
		echo '<div class="cpfb-text">';
		$content = make_clickable($content, '_new');
		$content = ($shortened) ? $content . $filter->apply('cppress_widget_fb_read_more', '..', $p['url']) : $content;
		$content = $filter->apply('cppress_widget_fb_content', $content, $p['url']);
		
		echo $content;
		echo '</div>';
		
		if(isset($instance['showlinkpreviews']) && isset($p['link_url']) && !empty($p['link_url']) && !empty($p['link_name'])){
			echo '<p class="cpfb-link-wrap">';
			echo '<a class="cpfb-link" href="' . $p['link_url'] . '" rel="external nofollow" target="_new">';
			if(!empty($p['link_image']) && ($filter->apply('cppress_widget_fb_show_link_image', true) !== false)){
				echo '<span class="cpfb-link-image-wrap">';
				echo '<img class="cpfb-link-image" src="' . esc_attr($p['link_image']) . '" width="114" />';
				echo '</span>';
			}
			echo '<span class="cpfb-link-text-wrap">';
			echo '<span class="cpfb-link-name">' . esc_attr($p['link_name']) . '</span>';
			if(isset($p['link_caption'])){
				echo '<span class="cpfb-link-caption">' . esc_html($p['link_caption']) . '</span>';
			}
			if(isset($p['link_description'])){
				echo '<span class="cpfb-link-description">' . esc_html($p['link_description']) . '</span>';
			}
			echo '</span>';
			echo '</a>';
			echo '</p>';
		}
		
		if($instance['imgsize'] !== 'dont_show' && isset($p['image']) && !empty($p['image'])){
			if($p['type'] === 'video' && $instance['imgsize'] == 'normal'){
				$p['image'] = str_ireplace( array( "_s.jpg", "_s.png" ), array( "_n.jpg", "_n.png" ), $p['image'] );
			}
			echo '<p class="cpfb-image-wrap">';
			echo '<a class="cpfb-image-link" target="_new" href="' . $p['url'] . '" rel="external nofollow">';
			$maxImgWidth = (!empty($instance['imgwidth'])) ? $instance['imgwidth'] . 'px' : '100%';
			$maxImgHeight = (!empty($instance['imgheight'])) ? $instance['imgheight'] . 'px' : 'none';
			echo '<img class="cpfb-image" src="' . $p['image'] . '" style="' . esc_attr("max-width: {$maxImgWidth}; max-height: {$maxImgHeight}") . '" alt="" />';
			echo '</a>';
			if($p['type'] === 'video'){
				echo '<span class="cpfb-video-link"></span>';
			}
			echo '</p>';
		}
		
		echo '<p class="cpfb-post-link-wrap">';
		echo '<a target="_new" class="cpfb-post-link" href="' . esc_url($p['url']) . '" rel="external nofollow">';
		if(isset($instance['showlikecount'])){
			echo '<span class="cpfb-like-count">' . absint($p['like_count']);
			echo '<span>' . __('likes', 'cppress') . '</span>';
			echo '</span>';
		}
		
		if(isset($instance['showcommentcount'])){
			echo '<span class="cpfb-comment-count">' . absint($p['comment_count']);
			echo '<span>' . __('comments', 'cppress') . '</span>';
			echo '</span>';
		}
		
		if(isset($instance['showlikecount']) || isset($instance['showcommentcount'])){
			echo '&sdot;';
		}
		echo '<span class="cpfb-timestamp" title="' . sprintf(__('%1$s at %2%s', 'cppress'), date('l, F, j, Y', $p['timestamp']), date('G:i', $p['timestamp'])) . '">';
		echo timeago($p['timestamp']);
		echo '</a>';
		echo '</p>';
		
		echo '</' . $filter->apply('cppress_widget_fb_tag', 'div', $instance) . '>';
		
	}	
}else{
	echo '<p>' . __('No recent Facebook posts to show', 'cppress') . '</p>';
}

echo $filter->apply('cppress_widget_facebook_posts_after', '', $instance['wtitle']);

echo $args['after_widget'];
?>