<ul class="cp-ctype">
	<? foreach($widgets as $widget): ?>
		<li
			id="<?= $widget->id_base ?>"
			class="cp-widget-button cp-draggable"
			data-widget-icon="<?= $widget->getIcon(); ?>"
			data-widget-title="<?= $widget->name ?>"
			data-widget-description="<?= $widget->widget_options['description'] ?>"
			data-widget-classname="<?= $widget->widget_options['classname']?>"
		>
			<div class="cp-widget-wrapper">
				<span class="cp-widget-icon dashicons-before <?= $widget->getIcon(); ?>"></span>
				<h3><?= $widget->name ?></h3>
				<small class="description"><?= $widget->widget_options['description'] ?></small>
			</div>
		</li>
	<? endforeach; ?>
</ul>
<div style="clear: both"></div>
