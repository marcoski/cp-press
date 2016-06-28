(function($){
	$(document).on('repeater.add', function(e){
		var $$ = $(e.target);
		$title = $$.find("[name*='[slides][title]'], [name*='[parallax][slides]']");
		if($title.length > 0 && $title.val() !== ""){
		  var label = $title.val(); 
		  $.SliderWidget.changeTitleLabel($$, label);
		}
		$title.on('change', function(e){
			$.SliderWidget.changeTitleLabel($$, $(e.target).val());
		});
	});
	
	$.SliderWidget = {
	  changeTitleLabel: function($el, title){
	   $el.find('.cp-widget-field-repeater-item-top h4').text(title);
	  },
	};
})(jQuery);