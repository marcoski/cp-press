<div class="cp-widget-field cp-widget-type-section ">
	<label class="section cp-widget-type-section-visible"><?php _e('Advanced options', 'cppress')?></label>
	<div class="cp-widget-section">
		<div class="cp-widget-field">
			<label><?php _e('Select post type', 'cppress')?>:</label>
			<select  id="<?php echo $widget->get_field_id('posttype'); ?>"
				name="<?php echo $widget->get_field_name('posttype'); ?>">
					<?php foreach( $posttypes as $posttype ) { ?>
						<option value="<?php echo $posttype; ?>" 
							<?php selected($posttype, $instance['posttype']); ?>><?php echo ucfirst($posttype); ?></option>
					<?php } ?>
			</select>
		</div>
		<? if(!$single): ?>
		<div class="cp-widget-field">
			<label for="<?php echo $widget->get_field_id('limit'); ?>"><?php _e('Limit', 'cppress')?>:</label>
			<input type="number" min="1" id="<?php echo $widget->get_field_id('limit'); ?>"
				name="<?php echo $widget->get_field_name('limit'); ?>"  
				value="<? $instance['limit'] != '' ? e($instance['limit']) : e('1') ?>"/>
		</div>
		<? endif; ?>
		<div class="cp-widget-field">
			<label for="<?php echo $widget->get_field_id('offset'); ?>"><?php _e('Offset', 'cppress')?>:</label>
			<input type="number" min="0" id="<?php echo $widget->get_field_id('offset'); ?>"
				name="<?php echo $widget->get_field_name('offset'); ?>"  
				value="<? $instance['offset'] != '' ? e($instance['offset']) : e('0') ?>"/>
				<div class="cp-widget-field-description">
					<?php _e('The number of post to skip', 'cppress')?>
				</div>
		</div>
		<div class="cp-widget-field">
			<label><?php _e('Order', 'cppress')?>:</label>
			<select style="width:100%;" id="<?php echo $widget->get_field_id('order'); ?>"
				name="<?php echo $widget->get_field_name('order'); ?>" >
				<option value="DESC" <?php selected( $instance['order'], 'DESC' ); ?>><?php _e('Descending', 'cppress'); ?></option>
				<option value="ASC" <?php selected( $instance['order'], 'ASC' ); ?>><?php _e('Ascending', 'cppress'); ?></option>
			</select>
		</div>
		<div class="cp-widget-field">
			<label><?php _e('Orderby', 'cppress')?>:</label>
			<select class="widefat" style="width:100%;" id="<?php echo $widget->get_field_id('orderby'); ?>"
				name="<?php echo $widget->get_field_name('orderby'); ?>" >
				<option value="ID" <?php selected( $instance['orderby'], 'ID' ); ?>><?php _e('ID', 'cppress'); ?></option>
				<option value="author" <?php selected( $instance['orderby'], 'author' ); ?>><?php _e('Author', 'cppress'); ?></option>
				<option value="title" <?php selected( $instance['orderby'], 'title' ); ?>><?php _e('Title', 'cppress'); ?></option>
				<option value="date" <?php selected( $instance['orderby'], 'date' ); ?>><?php _e('Date' , 'cppress'); ?></option>
				<option value="modified" <?php selected( $instance['orderby'], 'modified' ); ?>><?php _e('Modified', 'cppress'); ?></option>
				<option value="rand" <?php selected( $instance['orderby'], 'rand' ); ?>><?php _e('Random', 'cppress'); ?></option>
				<option value="comment_count" <?php selected( $instance['orderby'], 'comment_count' ); ?>><?php _e('Comment Count', 'cppress'); ?></option>
				<option value="menu_order" <?php selected($instance['orderby'], 'menu_order' ); ?>><?php _e('Menu Order', 'cppress'); ?></option>
			</select>
		</div>
		<!-- TAXONOMIES FILTER -->
		<div class="cp-widget-taxonomy-filters">
		<?php 
			foreach($taxonomies as $taxonomyFilter){
				echo $taxonomyFilter;
			}
		?>
		</div>
	</div>
</div>
<?php if($show_view_options): ?>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('View options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field">
	    <label for="<?php echo $widget->get_field_id('linktitle'); ?>">
	    	<input class="widefat"
		      id="<?php echo $widget->get_field_id('linktitle'); ?>"
		      name="<?php echo $widget->get_field_name('linktitle'); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['linktitle'] ); ?> />&nbsp;
	    	<?php _e('Link title', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Apply a link to the post title', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?php echo $widget->get_field_id('postspercolumn'); ?>"><?php _e( 'Posts per column:', 'cppress' ); ?></label>
				<input type="number" min="0" max="20"
           id="<?php echo $widget->get_field_id('postspercolumn'); ?>" 
           name="<?php echo $widget->get_field_name('postspercolumn'); ?>"
           value="<?php echo  $instance['postspercolumn'] == '' ? 0 :  $instance['postspercolumn']; ?>"> 
      <div class="cp-widget-field-description">
				<?php  _e( 'Set to 0 to auto column', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?php echo $widget->get_field_id('showinfo'); ?>">
	    	<input class="widefat"
		      id="<?php echo $widget->get_field_id('showinfo'); ?>"
		      name="<?php echo $widget->get_field_name('showinfo'); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['showinfo'] ); ?> />&nbsp;
	    	<?php _e('Show post info', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Show post publish date and post author', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?php echo $widget->get_field_id('showmeta'); ?>">
	    	<input class="widefat"
		      id="<?php echo $widget->get_field_id('showmeta'); ?>"
		      name="<?php echo $widget->get_field_name('showmeta'); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['showmeta'] ); ?> />&nbsp;
	    	<?php _e('Show post meta info', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Show post categories and tags', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?php echo $widget->get_field_id('showexcerpt'); ?>">
	    	<input class="widefat"
		      id="<?php echo $widget->get_field_id('showexcerpt'); ?>"
		      name="<?php echo $widget->get_field_name('showexcerpt'); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['showexcerpt'] ); ?> />&nbsp;
	    	<?php _e('Show excerpt', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Show the post excerpt instead the post content', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?php echo $widget->get_field_id('hidecontent'); ?>">
	    	<input class="widefat"
		      id="<?php echo $widget->get_field_id('hidecontent'); ?>"
		      name="<?php echo $widget->get_field_name('hidecontent'); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['hidecontent'] ); ?> />&nbsp;
	    	<?php _e('Hide content', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Hide post content and post excerpt', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?php echo $widget->get_field_id('showthumbnail'); ?>">
	    	<input class="widefat"
		      id="<?php echo $widget->get_field_id('showthumbnail'); ?>"
		      name="<?php echo $widget->get_field_name('showthumbnail'); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['showthumbnail'] ); ?> />&nbsp;
	    	<?php _e('Show post thumbnail', 'cppress')?>
	    </label>
	  </div>
	  <div class="cp-widget-field">
	    <label for="<?php echo $widget->get_field_id('linkthumbnail'); ?>">
	    	<input class="widefat"
		      id="<?php echo $widget->get_field_id('linkthumbnail'); ?>"
		      name="<?php echo $widget->get_field_name('linkthumbnail'); ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $instance['linkthumbnail'] ); ?> />&nbsp;
	    	<?php _e('Link thumbnail', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Apply a link to the thumbnail if is visible', 'cppress' ); ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>