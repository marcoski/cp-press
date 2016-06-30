(function($){
	$(document).on('repeater.add', function(e){
			var $$ = $(e.target);
			var title = $$.find('.cp-widget-field-repeater-item-top h4').text();
			if($$.find('select option:selected').text() !== ''){
				$$.find('.cp-widget-field-repeater-item-top h4').text(
		  		title + ': ' + $$.find('select option:selected').text()
		  	);
			}
			$$.find('select').on('change', function(e){
				if($(this).find('option:selected').text() !== ''){
					$$.find('.cp-widget-field-repeater-item-top h4').text(
			  		title + ': ' + $(this).find('option:selected').text()
			  	);
				}
			});	
	});
	$(document).on('widget.loaded', function(e){
		var $$ = $(e.target);
		var cpAjax = $.fn.cpajax(e.target);
		$$.find("[name*='[posttype]']").on('change', function(e){
			var postType = $(this).find('option:selected').val();
			var re = /(cp_widget_[A-Za-z0-9_]+)/;
			var widgetName; 
			if((m = re.exec($(this).attr('name'))) !== null){
				widgetName = m[0];
			}
			cpAjax.call('change_taxonomy', function(response){
				$$.find('.cp-widget-taxonomy-filters').empty();
				if(response.length > 0){
					$.each(response, function(key, repeater){
						$$.find('.cp-widget-taxonomy-filters').append(repeater);
					});
					$.CpField.fn.enable($$.find('.cp-widget-taxonomy-filters'));
				}
			}, {post_type: postType, widget: widgetName});
		});
	});
})(jQuery);