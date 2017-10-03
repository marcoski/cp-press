<?php 
	echo $args['before_widget'];
	
		if(isset($instance['showtitle']) && $instance['showtitle']){
			echo $filter->apply('cppress_widget_the_title', 
					'<h1>' .$instance['wtitle'].'</h1>', $instance['wtitle']);
		}
		echo $filter->apply('cppress_widget_twitter_timeline_before', '', $instance['wtitle']);
			$timeline_open = '<a class="twitter-timeline" 
					width="' . $instance['twitter_width'] .'" 
					height="' . $instance['twitter_height'] . '" 
					href="https://twitter.com/twitterdev" ' . trim( $dataTwitter ) . '>';
			echo $filter->apply('cppress_widget_twitter_timeline_open', $timeline_open, $instance['wtitle']);
			echo $filter->apply('cppress_widget_twitter_timeline_text', 
				sprintf(__( 'Tweets by @ %s', 'cppress' ), $instance['twitter_screen_name']), $instance['wtitle']);
		 	echo $filter->apply('cppress_widget_twitter_timeline_open', '</a>', $instance['wtitle']);
		echo $filter->apply('cppress_widget_twitter_timeline_after', '', $instance['wtitle']);
	
	echo $args['after_widget'];
?>
<script>
	/*!
	 * Twitter Embeddable Widget
	 * https://dev.twitter.com/web/javascript/loading
	 */
	window.twttr = (function (d, s, id) {
	  var t, js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src= "https://platform.twitter.com/widgets.js";
	  fjs.parentNode.insertBefore(js, fjs);
	  return window.twttr || (t = { _e: [], ready: function (f) { t._e.push(f) } });
	}(document, "script", "twitter-wjs"));
</script>