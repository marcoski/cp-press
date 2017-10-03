<div class="row filters-info-box">
	<div class="col-md-12">
		<div class="filters-active is-empty">
			<h2><?php echo $filter->apply('cppress_filters_active_title', __('My filters', 'cppress')); ?></h2>
			<ul class="tags">
				<li class="tags-item tags-item-clear-all">
					<a href="#" data-filter-remove-all><?php echo $filter->apply('cppress_filters_active_clear_all', __('Clear All', 'cppress')); ?></a>
				</li>
			</ul>
		</div>
	</div>
	<div class="col-md-6">
		<h2 class="filters-result-count">
			<span class="filters-result-count-number"><?php echo $query->found_posts ?></span>
			<span class="filters-result-count-label"><?php echo __('results', 'cppress'); ?></span>
		</h2>
	</div>
</div>