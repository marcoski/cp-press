<?php
/**
 * Backbone Templates
* This file contains all of the HTML used in our dialog and the workflow itself.
*
* Each template is wrapped in a script block ( note the type is set to "text/html" ) and given an ID prefixed with
* 'tmpl'. The wp.template method retrieves the contents of the script block and converts these blocks into compiled
* templates to be used and reused in your application.
*/
/**
 * The Modal Window, including sidebar and content area.
* Add menu items to ".navigation-bar nav ul"
* Add content to ".cppress_dialog-main article"
*/
?>
<script type="text/html" id='tmpl-cppress-dialog-window'>
	<div class="cppress_dialog">
		<div class="cppress_dialog-content">
			<section class="cppress_dialog-main" role="main">
				<header>
					<h3>{{ data.title }}</h3>
					<a class="cppress_dialog-close dashicons dashicons-no" href="#" title="<? _e( 'Close', 'cppress' ); ?>">
						<span class="screen-reader-text"><? _e( 'Close', 'cppress' ); ?></span>
					</a>
				</header>
				<div class="navigation-bar">
					<nav></nav>
				</div>
				<article>
					<div class="cp-panel-dialog"></div>
				</article>
				<footer>
					{{{ data.button }}}
				</footer>
			</section>
		</div>
	</div>
</script>

<?php
/**
 * The Modal Backdrop
 */
?>
<script type="text/html" id='tmpl-cppress-dialog-backdrop'>
	<div class="cppress_dialog-backdrop">&nbsp;</div>
</script>
<?php
/**
 * Base template for a navigation-bar menu item ( and the only *real* template in the file ).
 */
?>
<script type="text/html" id='tmpl-cppress-dialog-menu-item'>
	<li class="nav-item"><a href="{{ data.url }}">{{ data.name }}</a></li>
</script>
<?php
/**
 * A menu item separator.
 */
?>
<script type="text/html" id='tmpl-cppress-dialog-menu-item-separator'>
	<li class="separator">&nbsp;</li>
</script>

<script type="text/html" id='tmpl-cppress-dialog-save-cancel'>
	<div class="inner text-right">
		<button id="btn-cancel" class="button button-large"><? _e( 'Cancel', 'cppress' ); ?></button>
		<button id="btn-ok" class="button button-primary button-large"><?php _e( 'Save', 'cppress' ); ?></button>
	</div>
</script>

<script type="text/html" id='tmpl-cppress-dialog-add'>
	<div class="inner text-right">
		<button id="btn-ok" class="button button-primary button-large"><?php _e( 'Add', 'cppress' ); ?></button>
	</div>
</script>