jQuery(document).ready(function(){
	var $ = jQuery;
	$('#cppress_slider_logo_button').click(function() {
		formfield = $('#cppress_slider_logo').attr('name');
		tb_show('', 'media-upload.php?type=image&TB_iframe=true');
		return false;
	});
	
	
	
	window.send_to_editor = function(html) {
		imgurl = $('img',html).attr('src');
		$('#cppress_slider_logo').val(imgurl);
		$('#cppress_slider_logo_img').attr('src', imgurl);
		tb_remove();
	};
	
	var $sliderTable = $('table.cp-slider');
	if($sliderTable.length){
		var slider = $sliderTable.cpslideritem();
		slider.$element.on('click.delete', '.cp-row-delete', function(){
			slider.super.delete($(this));
		});
		slider.$element.on('click.link', '.cp-row-link', function(){
			slider.link($(this));
		});
		slider.$element.find('.add-slide').on('click.addSlide', function(event){
			event.preventDefault();
			if(!$(this).hasClass('disabled')){
				slider.addSlide($(this));
			}
		});
		$('#slider_type_select').on('change.sliderType', {context: slider}, slider.changeSliderType);
	}
});

(function($){
	var CpSliderItem = function(element){
		this.super = new $.fn.cpitem(element);
		CpSliderItem.prototype.constructor = CpSliderItem;
		
		this.init();
	};
	
	CpSliderItem.prototype.init = function(){
		this.$element = this.super.$element;
		this.$container = $('#cp-slider-container');
		this.sliderId = this.$element.attr('id').split('_')[1];
		this.$slides = this.super.$element.find('.cp-slide');
		this.file = wp.media.frames.file_frame = wp.media({
			multiple:'add',
			frame: 'post',
			library: {type: 'image'}
		});
		this.$dialog = this.super.$dialog;
		this.super.$deleteDialogContent = $("<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>These slide will be deleted from this slider. Are you sure?</p>");
		this.super.deleteInfo = {
			title		: 'Delete Slide',
			action		: 'delete_slide',
			selector	: 'tr.cp-slide[data-slide=%s]'
		};
		this.super.cpAjax = $.fn.cpajax(this);
		
		if(this.super.$element.find('#parallax-slider-background').length > 0){
			this.super.$element.find('#parallax-slider-background').addClass('disabled');
		}
	}
	
	CpSliderItem.prototype.changeSliderType = function(event){
		var that = event.data.context;
		var selectedVal = $(this).val();
		that.super.cpAjax.call('create_'+selectedVal, function(response){
			that.$container.html(response.data);
			that.$container.find('.add-slide').on('click.addSlide', function(event){
				event.preventDefault();
				if(!$(this).hasClass('disabled')){
					that.addSlide($(this));
				}
			});
		}, {slider_id: that.sliderId});
		
		/*that.super.$container.find(".hideable:visible").hide();
		if($(this).attr('name') === ''){
			that.super.$container.find("#"+selectedVal+"-box").show();
		}else if($(this).attr('name') === 'cp-press-slider[type]'){
			that.super.$container.find("#"+selectedVal+"-slider").show();
		}*/
	};
	

	CpSliderItem.prototype.addSlide = function($element){
		if($element.attr('id') == 'parallax-slider-add'){
			this.action = 'parallax';
			this.parallaxAddSlide();
		}else if($element.attr('id') == 'parallax-slider-background'){
			this.action = 'parallax';
			this.addMedia('add_parallax_bg', this.addParallaxBgDOM);
		}else if($element.attr('id') == 'bootstrap-slider-add'){
			this.action = 'bootstrap';
			this.addMedia('add_slide', this.addSlideDOM);
		}else{
			this.action = 'cppress';
			this.addMedia('add_slide', this.addSlideDOM);
		}
	};
	
	CpSliderItem.prototype.link = function($element){
		var that = this;
		var slideId = $element.attr('id').split('-')[3];
		var sliderId = this.super.$element.attr('id').split('_')[1];
		var $link = this.super.$element.find('.cp-slide[data-slide='+slideId+']').find('input[data-input=link]');
		var linkUri = '';
		var buttons = {
			"Add Link": function(){ that.actionAddLink(that, $(this)); }
		};
		if($link.length > 0){
			linkUri = $link.val();
			buttons = {
				"Delete Link": function(){ that.actionDeleteLink(that, $(this)); }
			};
		}
		this.super.cpAjax.call('slide_link_modal', function(response){
			that.$dialog.html(response.data);
			that.super.dialog('Add Link', {
				height: $(window).width()/6,
				buttons: buttons
			});
			$('#select_ctype').change(function(ev){
				that.super.cpAjax.call('slide_link_content', function(response){
					$('#cp-linker-content').html(response.data);
					$('select#slide_link').change(function(){
						$('.cp-linker-permalink').html('<span class="description">'+$(this).val()+'</span>');
					});
				}, {type: $(this).val()});
			});
		}, {link: linkUri, slide_id: slideId, slider_id: sliderId});
	};
	
	CpSliderItem.prototype.actionAddLink = function(that, $dialog){
		var link
		if($('select#slide_link').length > 0){
			link = $('select#slide_link').find(':selected').val();
		}else if($('input#slide_link').length > 0){
			link = $('input#slide_link').val();
		}
		var slideId = $('#linker_table').data('slide');
		var $afterEl = that.super.$element.find('input[data-slide='+slideId+']');
		$afterEl.after('<label for="cp-press-slider">LINK </label><input type="text" readonly data-input="link" name="cp-press-slider['+slideId+'][link]" value="'+link+'"/>');
		$dialog.dialog('close');
	};
	
	CpSliderItem.prototype.actionDeleteLink = function(that, $dialog){
		var slideId = $('#linker_table').data('slide');
		var sliderId = $('#linker_table').data('slider');
		var $afterEl = that.super.$element.find('input[data-slide='+slideId+']');
		var $input = $afterEl.siblings('input[data-input=link]');
		that.super.cpAjax.call('slide_link_delete', function(response){
			$input.prev().remove();
			$input.cpremove();
			$dialog.dialog('close');
		}, {slide_id: slideId, slider_id: sliderId});
	}
	
	CpSliderItem.prototype.addSlideDOM = function(response, context) {
		$("table."+context.action+"-append-slide").append(response.data);
	};
	
	CpSliderItem.prototype.addParallaxBgDOM = function(response, context){
		$("table."+context.action+"-append-slide").append(response.data);
		$('#parallax-slider-background').addClass('disabled');
	}
	
	CpSliderItem.prototype.parallaxAddSlide = function(){
		this.slideId = this.uuid();
		this.super.cpAjax.call('add_parallax_slide', this.addSlideDOM, {slide_id: this.slideId, slide_num: this.getSlideNum()});
	};
	
	CpSliderItem.prototype.addMedia = function(action, callback){
		var that = this;
		this.file.on('insert', function() {
			var selection = that.file.state().get('selection');

			selection.map( function( attachment ) {
				attachment = attachment.toJSON();
				that.slideId = attachment.id;
				that.super.cpAjax.call(action, callback, {slide_id: that.slideId});
			});
		});

		this.file.open();	
	};
	
	CpSliderItem.prototype.uuid = function(){
		return (new Date()).getTime();
	};
	
	CpSliderItem.prototype.getSlideNum = function(){
		return $('tr.cp-slide:visible').not('#parallax-background').length;
	};
	
	$.fn.cpslideritem = function(){
		return new CpSliderItem(this);
	};

	$.fn.cpslideritem.Constructor = CpSliderItem;
}(jQuery));