(function($){
	$(document).on('repeater.add', function(e){
			var $$ = $(e.target);
			if($$.find('input.cp-widget-portfolio-title').length > 0){
				$$.find('.cp-widget-field-repeater-item-top h4').text(
		  		$$.find('input.cp-widget-portfolio-title').val()
		  	);
			 }		
	});
	$(document).on('linker.change', function(e){
		var $$ = $(e.target);
		var cpAjax = $.fn.cpajax(this);
		if($$.filter("[name*='[portfolioitems]']").length > 0){
			var $item = $$.parents('.cp-widget-field-repeater-item');
			var $itemForm = $$.parents('.cp-widget-field-repeater-item-form');
			if($itemForm.find('.cp-widget-field-repeater-item-change').length === 0){
				$itemForm.append($('<div>').addClass('cp-widget-field-repeater-item-change cp-widget-field'));
			}
			$itemForm.addClass('loading');
			$itemChange = $itemForm.find('.cp-widget-field-repeater-item-change');
			$itemChange.empty();
			cpAjax.call('widget_portfolio_show', function(html){
		  	$itemChange.append(html.data);
		  	$item.find('.cp-widget-field-repeater-item-top h4').text(
		  		$itemChange.find('input.cp-widget-portfolio-title').val()
		  	);
		  	$itemChange.removeClass('loading');
			}, {id: $$.val()});
		}
		
	});
})(jQuery);
