jQuery(document).ready(function(){
	var $ = jQuery;

	$('.chpress_header_colorpick, .cp-color-picker').wpColorPicker();
	$('#cppress_logo_button').click(function() {
		formfield = $('#cppress_logo').attr('name');
		tb_show('', 'media-upload.php?type=image&TB_iframe=true');
		return false;
	});

	window.send_to_editor = function(html) {
		imgurl = $('img',html).attr('src');
		$('#cppress_logo').val(imgurl);
		$('#cppress_logo_img').attr('src', imgurl);
		tb_remove();
	};

});

(function($){

	var CpDragDrop = function (element, $dropable){
		this.$element = $(element);
		this.$dropable = $dropable;
		this.dragOptions = {
			containment		: 'document',
			cursor			: 'move',
			zIndex			: 100,
			revert			: true,
			opacity			: 0.35
		};
		this.dropOptions = {
			accept			: '.cp-draggable',
			activeClass		: "cp-droppable-active",
			hoverClass		: "cp-droppable-hover",
			tollerance		: 'pointer'
		};
	};

	CpDragDrop.prototype.drag = function(options){
		var dOpt = $.extend({}, this.dragOptions, options);
		this.$element.draggable(dOpt);
	};

	CpDragDrop.prototype.drop = function(options){
		var that = this;
		var dOpt = $.extend({}, this.dropOptions, options);
		this.$dropable.each(function(){
			$(this).droppable(dOpt);
		});
	};

	CpDragDrop.prototype.setDropable = function($dropable){
		this.$dropable = $dropable;
	};

	$.fn.cpdragdrop = function($droppable){
		draggableObj = [];
		this.each(function ()
		{
			var draggable = new CpDragDrop(this, $droppable);
			draggableObj.push(draggable);
		});

		return draggableObj;
	};

	$.fn.cpdragdrop.Constructor = CpDragDrop;
}(jQuery));

(function($){

	var CpSortable = function (element){
		var that = this;
		this.$element = $(element);
		this.options = {
			tolerance: 'pointer',
			start: function(ev, ui){
				that.startEv(that, ev, ui);
			},
			helper: function(e, el){
				var helper = el.clone()
					.css({
						'position': 'absolute'
					});

				return helper;
			}
		};
	};

	CpSortable.prototype.sort = function(options){
		var sOpt = $.extend({}, this.options, options);
		this.$element.sortable(sOpt);

	};

	CpSortable.prototype.setPlaceHolder = function(placeholder){
		this.options.placeholder = placeholder;
	};

	CpSortable.prototype.startEv = function(that, ev, ui){
		ui.placeholder.css({
			height: ui.item.height()
		});
	};

	CpSortable.prototype.refresh = function(){
		if(this.$element !== null){
			this.$element.sortable("refresh");
		}
	};

	CpSortable.prototype.destroy = function(){
		if(this.$element !== null){
			this.$element.sortable("destroy");
		}
	}

	$.fn.cpsortable = function(){
		return new CpSortable(this);
	};

	$.fn.cpsortable.Constructor = CpSortable;

}(jQuery));



(function($){

	$.fn.cpremove = function(){
		$el = $(this);
		$el.translate(
			{x: 0, y: 0, z: 0, scale: 0},
			300,
			'ease',
			function(){$el.remove();}
		);
	};

	$.fn.translate = $.fn.translate3d =  function(translations, speed, easing, complete){
        var opt = $.speed(speed, easing, complete);
        opt.easing = opt.easing || 'ease';
        return this.each(function() {
            var $this = $(this);

			$this.css({
				transitionDuration: opt.duration + 'ms',
				transitionTimingFunction: opt.easing,
				transform: 'translate3d(' + translations.x + 'px, ' + translations.y + 'px, ' + translations.z + 'px) scale('+translations.scale+')'
			});



            setTimeout(function() {
                $this.css({
                    transitionDuration: '0s',
                    transitionTimingFunction: 'ease'
                });

                opt.complete();
            }, opt.duration);


        });
    };
}(jQuery));

(function($){
	var CpItem = function(element){
		this.$element = $(element);
		this.$container = this.$element.parent().parent();
		this.$deleteDialogContent = $();
		this.deleteInfo = {};
		this.$dialog = $('<div class="cp_section_modal"></div>');
		this.dialogOptions = {
			resizable: false,
			width: '50%',
			height: $(window).width()/4,
			buttons: {
				Close: function(){
					$(this).dialog('close');
				}
			},
			close: function(){
				$(this).remove();
			}
		};
		this.sortObj = this.$element.find('tbody').cpsortable();
		this.sortObj.setPlaceHolder("cp-item-placeholder");
		this.sortObj.sort({
			handle	: ".cp-row-move",
		});
		this.cpAjax = null;
	};

	CpItem.prototype.delete = function($element){
		var that = this;
		var id = $element.attr('id').split('-')[3];
		var containerId = this.$element.attr('id').split('_')[1];
		this.cpAjax.setData({id: this.slideId, container_id: this.sliderId});
		if(!this.$element.hasClass('confirm')){
			this.$dialog.html(this.$deleteDialogContent);
			this.dialog(this.deleteInfo.title, {
				resizable: false,
				height:140,
				modal: true,
				buttons: {
					"Delete": function() {
						that.cpAjax.call(that.deleteInfo.action, that.deleteDOM, {id: id, container_id: containerId});
						$( this ).dialog( "close" );
					}
				}
			});
		}else{
			that.cpAjax.call(this.deleteInfo.action, this.deleteDom, {id: id, container_id: containerId});
		}
	};

	CpItem.prototype.deleteDOM = function(response, context){
		var data = response;
		var selector = context.super.deleteInfo.selector.replace('%s', data.id);
		context.super.$element.find(selector).cpremove();
	};

	CpItem.prototype.dialog = function(title, options){
		this.dialogOptions.title = title;
		dopt = $.extend(true, {}, this.dialogOptions, options);
		this.$dialog.dialog(dopt);
	};

	$.fn.cpitem = function(element){
		return new CpItem(element);
	};

	$.fn.cpitem.Constructor = CpItem;
}(jQuery));

(function($){
	var CpAjax = function(scope){
		this.url = ajaxurl;
		this.scope = scope;
	};

	CpAjax.prototype.setData = function(data){
		this.data = $.extend({}, this.data, data);
	};

	CpAjax.prototype.call = function(action, callback, args){
		var that = this;
		this.data = {
			action:		action,
		};

		data = $.extend({}, this.data, args);

		$.post(this.url, data, function(response){
			callback(response, that.scope);
		}, 'json');
	};

	$.fn.cpajax = function(scope){
		return new CpAjax(scope);
	};

	$.fn.cpajax.Constructor = CpAjax;
}(jQuery));

(function($){

	var CpMedia = function(title, options){
		var that = this;
		this.selectedObj = null;
		this.options = {
			// Modal title
			title: title,
			// Enable/disable multiple select
			multiple: true,
			// Library WordPress query arguments.
			library: {
				order: 'ASC',
				// [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo',
				// 'id', 'post__in', 'menuOrder' ]
				orderby: 'title',
				// mime type. e.g. 'image', 'image/jpeg'
				type: 'image',
				// Searches the attachment title.
				search: null,
				// Attached to a specific post (ID).
				uploadedTo: null
			},
			button: {
				text: 'Set profile background'
			}
		};
		this.options = $.extend(true, {}, this.options, options);
		this.mediaFrame = new wp.media.view.MediaFrame.Select();

		// Fires when a user has selected attachment(s) and clicked the select button.
		// @see media.view.MediaFrame.Post.mainInsertToolbar()
		this.mediaFrame.on( 'select', function(){
			that.onSelect(that);
		});
	};

	CpMedia.prototype.state = function(){
		return this.mediaFrame.state();
	};

	CpMedia.prototype.lastState = function(){
		return this.mediaFrame.lastState();
	};

	CpMedia.prototype.open = function(){
		this.mediaFrame.open();
	};

	CpMedia.prototype.onSelect = function(that){
		var sel = that.state().get('selection');
		sel.map(function(a){
			that.selectedObj = a.toJSON();
		});
	};

	$.fn.cpmedia = function(title, options){
		return new CpMedia(title, options);
	};

	$.fn.cpmedia.Constructor = CpMedia;

}(jQuery));
