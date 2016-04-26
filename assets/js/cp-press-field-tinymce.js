(function($){
	var setup = function($field, index){
		if(typeof tinyMCEPreInit !== 'undefined'){
			if(tinyMCEPreInit.mceInit.hasOwnProperty('undefined')){
				delete tinyMCEPreInit.mceInit['undefined'];
			}
			if(tinyMCEPreInit.qtInit.hasOwnProperty('undefined')){
				delete tinyMCEPreInit.qtInit['undefined'];
			}
			if(QTags.instances.hasOwnProperty('undefined')){
				delete QTags.instances['undefined'];
			}
			
			var $container = $field.find('.cp-widget-tinymce-container');
			var $textarea = $container.find('textarea');
			var id = $textarea.attr('id');
			console.log($textarea);
			if(typeof tinymce != 'undefined'){
				var mceSettings = tinyMCEPreInit.mceInit['cp-widget-fake-editor'];
				var setupEditor = function(editor){
					editor.on('change', function(){
						tinymce.get(id).save();
						$textarea.trigger('change');
						$textarea.val(window.switchEditors.pre_wpautop(editor.getContent()));
					});
					
					$textarea.on('keyup', function(){
						editor.setContent(window.switchEditors.wpautop($textarea.val()));
					});
					
				};
				
				mceSettings = $.extend({}, mceSettings, {selector: '#' + id, setup: setupEditor});
				tinyMCEPreInit.mceInit[id] = mceSettings;
				var $wrap = $container.find('div#wp-' + id + '-wrap');
				if($wrap.hasClass('tmce-active')){
					if($field.is(':visible')){
						tinymce.init(tinyMCEPreInit.mceInit[id]);
					}else{
						var intervalId = setInterval(function(){
							if($field.is(':visible')){
								tinymce.init(tinyMCEPreInit.mceInit[id]);
								clearInterval();
							}
						}, '500');
					}
				}
				var qtSettings = {};
				qtSettings = $.extend({}, tinyMCEPreInit.qtInit['content'], qtSettings, {id: id});
				tinyMCEPreInit.qtInit[id] = qtSettings;
				$container.find('.quicktags-toolbar').remove();
				quicktags(tinyMCEPreInit.qtInit[id]);
				
				QTags._buttonsInit();
			}
		}else{
			setTimeout(function(){
				setup($field, index);
			}, 500);
		}
	};
	
	$(document).on('cpsetupform', function(e, fields){
		var $form = $(e.target);
		var $fields = $form.find('.cp-widget-field');
		$fields.each(function(index, el){
			if($(el).is('.cp-widget-field-repeater')){
				if($(el).is(':visible')){
					
				}else{
					
				}
			}else if($(el).is('.cp-widget-tinymce')){
				setup($(el), index);
			}
		});
	});
	
})(jQuery);