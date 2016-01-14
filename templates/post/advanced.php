<div class="cp-widget-field cp-widget-type-section ">
	<label class="section"><?php _e('Advanced options', 'cppress')?></label>
	<div class="cp-widget-section">
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
			<label><?php _e('Filter to Tag', 'cppress')?>:</label>
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
	</div>
</div>