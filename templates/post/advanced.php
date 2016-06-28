<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Advanced options', 'cppress')?></label>
	<div class="cp-widget-section">
		<?php if(isset($values['id']['enableadvanced'])): ?>
		<div class="cp-widget-field">
			<label for="<?= $values['id']['enableadvanced']; ?>">
				<input 
					id="<?= $values['id']['enableadvanced']; ?>"
					name="<?= $values['name']['enableadvanced']; ?>" 
					type="checkbox" 
					value="1" <?php checked( '1', $values['value']['enableadvanced'] ); ?> />
				<?php _e('Enable advanced options', 'cppress')?>
			</label>
		</div>
		<?php endif; ?>
		<?php if(isset($values['id']['posttype'])): ?>
		<div class="cp-widget-field">
			<label><?php _e('Select post type', 'cppress')?>:</label>
			<select  id="<?= $values['id']['posttype'] ?>"
				name="<?= $values['name']['posttype']; ?>">
					<?php foreach( $posttypes as $posttype ) { ?>
						<option value="<?php echo $posttype; ?>" 
							<?php selected($posttype, $values['value']['posttype']); ?>><?php echo ucfirst($posttype); ?></option>
					<?php } ?>
			</select>
		</div>
		<?php endif; ?>
		<? if(!$single): ?>
		<div class="cp-widget-field">
			<label for="<?= $values['id']['limit'] ?>"><?php _e('Limit', 'cppress')?>:</label>
			<input type="number" min="1" id="<?= $values['id']['limit'] ?>"
				name="<?= $values['name']['limit']; ?>"  
				value="<? $values['value']['limit'] != '' ? e($values['value']['limit']) : e('1') ?>"/>
		</div>
		<? endif; ?>
		<div class="cp-widget-field">
			<label <?= $values['id']['offset'] ?>"><?php _e('Offset', 'cppress')?>:</label>
			<input type="number" min="0" id="<?= $values['id']['offset'] ?>"
				name="<?= $values['name']['offset']; ?>"  
				value="<? $values['value']['offset'] != '' ? e($values['value']['offset']) : e('0') ?>"/>
				<div class="cp-widget-field-description">
					<?php _e('The number of post to skip', 'cppress')?>
				</div>
		</div>
		<div class="cp-widget-field">
			<label><?php _e('Order', 'cppress')?>:</label>
			<select style="width:100%;" id="<?= $values['id']['order'] ?>"
				name="<?= $values['name']['order']; ?>" >
				<option value="DESC" <?php selected( $values['value']['order'], 'DESC' ); ?>><?php _e('Descending', 'cppress'); ?></option>
				<option value="ASC" <?php selected( $values['value']['order'], 'ASC' ); ?>><?php _e('Ascending', 'cppress'); ?></option>
			</select>
		</div>
		<div class="cp-widget-field">
			<label><?php _e('Orderby', 'cppress')?>:</label>
			<select class="widefat" style="width:100%;" id="<?= $values['id']['orderby'] ?>"
				name="<?= $values['name']['orderby']; ?>" >
				<option value="ID" <?php selected( $values['value']['orderby'], 'ID' ); ?>><?php _e('ID', 'cppress'); ?></option>
				<option value="author" <?php selected( $values['value']['orderby'], 'author' ); ?>><?php _e('Author', 'cppress'); ?></option>
				<option value="title" <?php selected( $values['value']['orderby'], 'title' ); ?>><?php _e('Title', 'cppress'); ?></option>
				<option value="date" <?php selected( $values['value']['orderby'], 'date' ); ?>><?php _e('Date' , 'cppress'); ?></option>
				<option value="modified" <?php selected( $values['value']['orderby'], 'modified' ); ?>><?php _e('Modified', 'cppress'); ?></option>
				<option value="rand" <?php selected( $values['value']['orderby'], 'rand' ); ?>><?php _e('Random', 'cppress'); ?></option>
				<option value="comment_count" <?php selected( $values['value']['orderby'], 'comment_count' ); ?>><?php _e('Comment Count', 'cppress'); ?></option>
				<option value="menu_order" <?php selected($values['value']['orderby'], 'menu_order' ); ?>><?php _e('Menu Order', 'cppress'); ?></option>
			</select>
		</div>
		<div class="cp-widget-field">
			<label><?php _e('Filter to Category', 'cppress')?>:</label>
			<label class="cp-clear-all"><?php _e('Clear all', 'cppress'); ?></label>
			<select multiple="multiple" style="width:100%;" id="<?= $values['id']['categories'] ?>"
				name="<?= $values['name']['categories']; ?>[]">
				<optgroup label="Categories">
					<?php $categories = get_terms('category', array('hide_empty' => false)); ?>
					<?php foreach( $categories as $category ) { ?>
						<option value="<?php echo $category->term_id; ?>" 
							<?php if (is_array( $values['value']['categories']) && 
									in_array( $category->term_id, $values['value']['categories'])){
										echo ' selected="selected"';
									} ?>><?php echo $category->name; ?></option>
					<?php } ?>
				</optgroup>
			</select>
		</div>
		<div class="cp-widget-field">
			<label><?php _e('Exclude to Category', 'cppress')?>:</label>
			<label class="cp-clear-all"><?php _e('Clear all', 'cppress'); ?></label>
			<select multiple="multiple" style="width:100%;" id="<?= $values['id']['excludecat'] ?>"
				name="<?= $values['name']['excludecat']; ?>[]">
				<optgroup label="Categories">
					<?php $categories = get_terms('category', array('hide_empty' => false)); ?>
					<?php foreach( $categories as $category ) { ?>
						<option value="<?php echo $category->term_id; ?>" 
							<?php if (is_array( $values['value']['excludecat']) && 
									in_array( $category->term_id, $values['value']['excludecat'])){
										echo ' selected="selected"';
									} ?>><?php echo $category->name; ?></option>
					<?php } ?>
				</optgroup>
			</select>
		</div>
		<div class="cp-widget-field">
			<label><?php _e('Filter to Tag', 'cppress')?>:</label>
			<label class="cp-clear-all"><?php _e('Clear all', 'cppress'); ?></label>
			<select style="width:100%;" multiple="multiple" id="<?= $values['id']['tags'] ?>"
				name="<?= $values['name']['tags']; ?>[]">
				<optgroup label="Tags">
					<?php $tags = get_terms('post_tag', array('hide_empty' => false)); ?>
					<?php foreach( $tags as $post_tag ) { ?>
						<option value="<?php echo $post_tag->term_id; ?>" 
							<?php if (is_array($values['value']['tags']) && 
									in_array($post_tag->term_id, $values['value']['tags'])){
										echo ' selected="selected"'; 
									} ?>><?php echo $post_tag->name; ?></option>
					<?php } ?>
				</optgroup>
			</select>
		</div>
		<div class="cp-widget-field">
			<label><?php _e('Exclude to Tag', 'cppress')?>:</label>
			<label class="cp-clear-all"><?php _e('Clear all', 'cppress'); ?></label>
			<select style="width:100%;" multiple="multiple" id="<?= $values['id']['excludetags'] ?>"
				name="<?= $values['name']['excludetags']; ?>[]">
				<optgroup label="Tags">
					<?php $tags = get_terms('post_tag', array('hide_empty' => false)); ?>
					<?php foreach( $tags as $post_tag ) { ?>
						<option value="<?php echo $post_tag->term_id; ?>" 
							<?php if (is_array($values['value']['excludetags']) && 
									in_array($post_tag->term_id, $values['value']['excludetags'])){
										echo ' selected="selected"'; 
									} ?>><?php echo $post_tag->name; ?></option>
					<?php } ?>
				</optgroup>
			</select>
		</div>
	</div>
</div>
<?php if($show_view_options): ?>
<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('View options', 'cppress')?></label>
	<div class="cp-widget-section cp-widget-section-hide">
		<div class="cp-widget-field">
	    <label for="<?= $values['id']['linktitle']; ?>">
	    	<input class="widefat"
		      id="<?= $values['id']['linktitle']; ?>"
		      name="<?= $values['name']['linktitle']; ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $values['value']['linktitle'] ); ?> />&nbsp;
	    	<?php _e('Link title', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Apply a link to the post title', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field cp-widget-input">
			<label for="<?php echo $values['id']['postspercolumn']; ?>"><?php _e( 'Posts per column:', 'cppress' ); ?></label>
				<input type="number" min="0" max="20"
           id="<?php echo  $values['id']['postspercolumn']; ?>" 
           name="<?php echo  $values['name']['postspercolumn']; ?>"
           value="<?php echo  $values['value']['postspercolumn'] == '' ? 0 :  $values['value']['postspercolumn']; ?>"> 
      <div class="cp-widget-field-description">
				<?php  _e( 'Set to 0 to auto column', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $values['id']['showinfo']; ?>">
	    	<input class="widefat"
		      id="<?= $values['id']['showinfo']; ?>"
		      name="<?= $values['name']['showinfo']; ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $values['value']['showinfo'] ); ?> />&nbsp;
	    	<?php _e('Show post info', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Show post publish date and post author', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $values['id']['showmeta']; ?>">
	    	<input class="widefat"
		      id="<?= $values['id']['showmeta']; ?>"
		      name="<?= $values['name']['showmeta']; ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $values['value']['showmeta'] ); ?> />&nbsp;
	    	<?php _e('Show post meta info', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Show post categories and tags', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $values['id']['showexcerpt']; ?>">
	    	<input class="widefat"
		      id="<?= $values['id']['showexcerpt']; ?>"
		      name="<?= $values['name']['showexcerpt']; ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $values['value']['showexcerpt'] ); ?> />&nbsp;
	    	<?php _e('Show excerpt', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Show the post excerpt instead the post content', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $values['id']['hidecontent']; ?>">
	    	<input class="widefat"
		      id="<?= $values['id']['hidecontent']; ?>"
		      name="<?= $values['name']['hidecontent']; ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $values['value']['hidecontent'] ); ?> />&nbsp;
	    	<?php _e('Hide content', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Hide post content and post excerpt', 'cppress' ); ?>
			</div>
		</div>
		<div class="cp-widget-field">
			<label for="<?= $values['id']['showthumbnail']; ?>">
	    	<input class="widefat"
		      id="<?= $values['id']['showthumbnail']; ?>"
		      name="<?= $values['name']['showthumbnail']; ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $values['value']['showthumbnail'] ); ?> />&nbsp;
	    	<?php _e('Show post thumbnail', 'cppress')?>
	    </label>
	  </div>
	  <div class="cp-widget-field">
	    <label for="<?= $values['id']['linkthumbnail']; ?>">
	    	<input class="widefat"
		      id="<?= $values['id']['linkthumbnail']; ?>"
		      name="<?= $values['name']['linkthumbnail']; ?>"
		      type="checkbox"
		      value="1" <?php checked( '1', $values['value']['linkthumbnail'] ); ?> />&nbsp;
	    	<?php _e('Link thumbnail', 'cppress')?>
	    </label>
	    <div class="cp-widget-field-description">
				<?php  _e( 'Apply a link to the thumbnail if is visible', 'cppress' ); ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>