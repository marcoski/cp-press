(function($){
	$(document).on('repeater.add', function(e){
		var $$ = $(e.target);
		$network = $$.find("[id*='networks_network']");
		if($network.length > 0 && $network.val() !== ""){
		  var label = $network.children("option").filter(':selected').text(); 
		  $.SocialMediaWidget.changeTitleLabel($$, label);
		}
		$network.on('change', {$el: $$}, $.SocialMediaWidget.onChangeNetwork);
	});
	
	$.SocialMediaWidget = {
	  changeTitleLabel: function($el, title){
	   $el.find('.cp-widget-field-repeater-item-top h4').text(title);
	  },
	  
		onChangeNetwork: function(e){
			var $$ = $(e.target);
			var network = $$.val();
			var cpAjax = $.fn.cpajax(this);
			cpAjax.call('widget_social_get_network', function(data){
			  $.SocialMediaWidget.changeTitleLabel(e.data.$el, data.label);
			  var $urlInput = e.data.$el.find(".cp-widget-field-repeater-item-form [id*='_url']");
			  $urlInput.val(data.base_url);
			  
			  var $iconColorPicker = e.data.$el.find(".cp-widget-field-repeater-item-form [id*='_icolor']");
			  $iconColorPicker.wpColorPicker('color', data.icon_color);
			  
			  var $buttonColorPicker = e.data.$el.find(".cp-widget-field-repeater-item-form [id*='_bgcolor']");
			  $buttonColorPicker.wpColorPicker('color', data.button_color);
			  
			}, {network: network});
		}
	};
})(jQuery);