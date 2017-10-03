+function ($) { "use strict";

	// LIGHTBOX CLASS DEFINITION
	// ======================

	var Lightbox = function (element, options){
		this.options = options;
		this.$element  = $(element);
		this.$backdrop = null;
		this.isShown   = null;
		this.$modal = this.$element.find('.lightbox-modal');
		this.$modal_content = this.$modal.find('.modal-content').first();
		this.$modal_dialog = this.$modal.find('.cp-modal-dialog').first();
		this.$modal_header = this.$modal.find('.modal-header').first();
		this.$modal_body = this.$modal.find('.modal-body').first();
		this.$modal_arrows = null;
		this.$lightbox_container = this.$modal.find('.cp-lightbox-container').first();
		this.$lightbox_body = this.$lightbox_container.find('> div:first-child').first();
		this.$lighbox_image = this.$element.find('.lightbox-content').find('img:first');
		this.border = {
			top: parseFloat(this.$modal_dialog.css('border-top-width')) + parseFloat(this.$modal_content.css('border-top-width')) + parseFloat(this.$modal_body.css('border-top-width')),
			right: parseFloat(this.$modal_dialog.css('border-right-width')) + parseFloat(this.$modal_content.css('border-right-width')) + parseFloat(this.$modal_body.css('border-right-width')),
			bottom: parseFloat(this.$modal_dialog.css('border-bottom-width')) + parseFloat(this.$modal_content.css('border-bottom-width')) + parseFloat(this.$modal_body.css('border-bottom-width')),
			left: parseFloat(this.$modal_dialog.css('border-left-width')) + parseFloat(this.$modal_content.css('border-left-width')) + parseFloat(this.$modal_body.css('border-left-width'))
		};
		this.padding = {
			top: parseFloat(this.$modal_dialog.css('padding-top')) + parseFloat(this.$modal_content.css('padding-top')) + parseFloat(this.$modal_body.css('padding-top')),
			right: parseFloat(this.$modal_dialog.css('padding-right')) + parseFloat(this.$modal_content.css('padding-right')) + parseFloat(this.$modal_body.css('padding-right')),
			bottom: parseFloat(this.$modal_dialog.css('padding-bottom')) + parseFloat(this.$modal_content.css('padding-bottom')) + parseFloat(this.$modal_body.css('padding-bottom')),
			left: parseFloat(this.$modal_dialog.css('padding-left')) + parseFloat(this.$modal_content.css('padding-left')) + parseFloat(this.$modal_body.css('padding-left'))
		};
		var that = this;
		this.$element.on('show.bs.lightbox', this.options.onShow.bind(this)).on('shown.bs.lightbox', function() {
			that.modal_shown();
			return that.options.onShown.call(that);
	    }).on('hide.bs.lightbox', this.options.onHide.bind(this)).on('hidden.bs.lightbox', function() {
	    	return that.options.onHidden.call(that);
        });
 	};

	// We depend upon Twitter Bootstrap's Modal library to simplify things here
	Lightbox.prototype = $.extend({},$.fn.modal.Constructor.prototype);

	Lightbox.prototype.constructor = Lightbox;

	Lightbox.DEFAULTS = {
		backdrop: true,
		keyboard: true,
		show: true,
		gallery_selector: 'document.body',
	    directional_arrows: true,
	    type: null,
	    always_show_close: true,
	    loadingMessage: 'Loading...',
	    onShow: function() {},
	    onShown: function() {},
	    onHide: function() {},
	    onHidden: function() {},
	    onContentLoaded: function() {}
	};
	
	Lightbox.prototype.modal_shown = function(){
		var videoId;
		var that = this;
		this.gallery = this.$target.data('gallery');
		if(this.gallery){
			if(this.options.gallery_selector === 'document.body' || 
					this.options.gallery_selector === '') {
				this.gallery_items = 
					$(document.body).find('*[data-toggle="lightbox"][data-gallery="' + this.gallery + '"]');
			}else{
				this.gallery_items = 
					this.$element.parent()
					.find(this.options.gallery_selector + ' *[data-toggle="lightbox"][data-gallery="' + this.gallery + '"]');
			}
			this.gallery_index = this.gallery_items.index(this.$target);
			$(document).on('keydown.lightbox', this.navigate.bind(this));
			if (this.options.directional_arrows && this.gallery_items.length > 1){
				this.$modal_arrows = this.$lightbox_container.find('div.cp-lightbox-nav-overlay').first();
				this.$lightbox_container.find('a.lightbox-nav-left').on('click', function(event) {
					event.preventDefault();
					return that.navigate_left();
				});
				this.$lightbox_container.find('a.lightbox-nav-right').on('click', function(event) {
					event.preventDefault();
					return that.navigate_right();
				});
			}
			this.navigateTo(this.gallery_index);
		}
		
		
	};
	
	Lightbox.prototype.navigate = function(event){
		event = event || window.event;
		if(event.keyCode === 39 || event.keyCode === 37){
			if (event.keyCode === 39) {
				return this.navigate_right();
			} else if (event.keyCode === 37){
				return this.navigate_left();
			}
		}
	};
	
	Lightbox.prototype.navigateTo = function(index){
		var $next, $nextTarget, src;
		if(index < 0 || index > this.gallery_items.length - 1){
			return this;
		}
		this.showLoading();
		this.gallery_index = index;
		this.$target = $(this.gallery_items.get(this.gallery_index));
		
			this.$target_img = this.$target;
		
		this.updateInfo();
		src = this.$target_img.attr('data-remote') || this.$target_img.attr('src');
		this.detectRemoteType(src, this.$target_img.attr('data-type') || false);
		if (this.gallery_index + 1 < this.gallery_items.length) {
			$nextTarget = $(this.gallery_items.get(this.gallery_index + 1), false);
			$next = $nextTarget;
			src = $next.attr('data-remote') || $next.attr('src');
			if($next.attr('data-type') === 'image' || this.isImage(src)){
				return this.preloadImage(src, false);
			}
		}
	};
	
	Lightbox.prototype.showLoading = function(){
		this.$lightbox_body.html('<div class="modal-loading">' + this.options.loadingMessage + '</div>');
		return this;
	};
	
	Lightbox.prototype.navigate_left = function(){
		if(this.gallery_items.length === 1){
			return;
		}
		if(this.gallery_index === 0){
			this.gallery_index = this.gallery_items.length - 1;
		}else{
			this.gallery_index--;
		}
		var e = $.Event('navigate.bs.lightbox', {galleryIndex: this.gallery_index, direction: 'left'});
		this.$element.trigger(e);
		return this.navigateTo(this.gallery_index);
	};
	
	Lightbox.prototype.navigate_right = function(){
		if(this.gallery_items.length === 1){
			return;
		}
		if(this.gallery_index === this.gallery_items.length - 1){
			this.gallery_index = 0;
		}else{
			this.gallery_index++;
		}
		var e = $.Event('navigate.bs.lightbox', {galleryIndex: this.gallery_index, direction: 'right'});
		this.$element.trigger(e);
		return this.navigateTo(this.gallery_index);
	};
	
	Lightbox.prototype.updateInfo = function(){
		var caption, footer, header, title;
		header = this.$modal_content.find('.modal-header');
		footer = this.$modal_content.find('.modal-footer');
		title = this.$target.data('title') || "";
		caption = this.$target.data('footer') || "";
		if(title || this.options.always_show_close){
			header.css('display', '').find('.modal-title').html(title || "&nbsp;");
		}else{
			header.css('display', 'none');
		}
		if(caption){
			footer.css('display', '').html(caption);
		}else{
			footer.css('display', 'none');
		}
		return this;
	};
	
	Lightbox.prototype.detectRemoteType = function(src, type){
		var video_id;
		type = type || false;
		if(type === 'image' || this.isImage(src)){
			this.options.type = 'image';
			return this.preloadImage(src, true);
		}else if(type === 'youtube' || (video_id = this.getYoutubeId(src))){
			this.options.type = 'youtube';
			return this.showYoutubeVideo(video_id);
		}else if (type === 'vimeo' || (video_id = this.getVimeoId(src))){
			this.options.type = 'vimeo';
			return this.showVimeoVideo(video_id);
		}else if(type === 'instagram' || (video_id = this.getInstagramId(src))){
			this.options.type = 'instagram';
			return this.showInstagramVideo(video_id);
		}else if (type === 'video'){
			this.options.type = 'video';
			return this.showVideoIframe(video_id);
		}else{
			this.options.type = 'url';
			return this.loadRemoteContent(src);
		}
	};
	
	Lightbox.prototype.isImage = function(str){
		return str.match(/(^data:image\/.*,)|(\.(jp(e|g|eg)|gif|png|bmp|webp|svg)((\?|#).*)?$)/i);
	};
	
	Lightbox.prototype.isSwf = function(str){
		return str.match(/\.(swf)((\?|#).*)?$/i);
	};
	
	Lightbox.prototype.preloadImage = function(src, onLoadShowImage){
		var img,
		that = this;
		img = new Image();
		if((onLoadShowImage == null) || onLoadShowImage === true){
			img.onload = function() {
				var image;
				image = $('<img />');
				image.attr('src', img.src);
				image.addClass('img-responsive');
				that.$lightbox_body.html(image);
				if(that.$modal_arrows){
					that.$modal_arrows.css('display', 'block');
				}
				that.preloadSize();
				return image.load(function(){
					var e = $.Event('contentload.bs.lightbox', {lightbox: that});
					return that.$element.trigger(e);
				});
			};
			img.onerror = function() {
				return that.error('Failed to load image: ' + src);
			};
		}
		img.src = src;
		return img;
	};
	
	Lightbox.prototype.getYoutubeId = function(str){
		var match;
		match = str.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/);
		if(match && match[2].length === 11){
			return match[2];
		}else{
			return false;
		}
	};
	
	Lightbox.prototype.showYoutubeVideo = function(id){
		var height, width;
		width = this.checkDimensions(this.$target.data('width') || 560);
		height = width / (560 / 315);
		return this.showVideoIframe('//www.youtube.com/embed/' + id + '?badge=0&autoplay=1&html5=1', width, height);
	};
	
	Lightbox.prototype.getVimeoId = function(str){
		if(str.indexOf('vimeo') > 0){
			return str;
		}else{
			return false;
		}
	};
	
	Lightbox.prototype.showVimeoVideo = function(id){
		var height, width;
		width = this.checkDimensions(this.$target.data('width') || 560);
		height = width / (500 / 281);
		return this.showVideoIframe(id + '?autoplay=1', width, height);
	};
	
	Lightbox.prototype.getInstagramId = function(src){
		if(str.indexOf('instagram') > 0){
			return str;
		}else{
			return false;
		}
	};
	
	Lightbox.prototype.showInstagramVideo = function(){
		var height, width;
		width = this.checkDimensions(this.$target.data('width') || 612);
		this.resize(width);
		height = width + 80;
		this.$lightbox_body.html('<iframe width="' + width + '" height="' + height + '" src="' + this.addTrailingSlash(id) + 'embed/" frameborder="0" allowfullscreen></iframe>');
		var e = $.Event('contentload.bs.lightbox', {lightbox: that});
		this.$element.trigger(e);
		if(this.$modal_arrows){
			return this.$modal_arrows.css('display', 'none');
		}
	};
	
	Lightbox.prototype.showVideoIframe = function(url, width, height){
		height = height || width;
		this.$lightbox_body.html('<div class="embed-responsive embed-responsive-16by9"><iframe width="' + width + '" height="' + height + '" src="' + url + '" frameborder="0" allowfullscreen class="embed-responsive-item"></iframe></div>');
		this.options.onContentLoaded.call(this);
		if (this.$modal_arrows) {
			this.$modal_arrows.css('display', 'none');
		}
		return this;
	};
	
	Lightbox.prototype.loadRemoteContent = function(){
		
	};
	
	Lightbox.prototype.error = function(message){
		this.$lightbox_body.html(message);
		return this;
	};
	
	Lightbox.prototype.checkDimensions = function(width){
		var body_width, width_total;
		width_total = width + this.border.left + this.padding.left + this.padding.right + this.border.right;
		body_width = document.body.clientWidth;
		if(width_total > body_width){
			width = this.modal_body.width();
			}
		return width;
	};
	
	Lightbox.prototype.show = function (_relatedTarget){
		var that = this;
		this.$target = $(_relatedTarget);
	
		this.$target_img = this.$target;
		var e    = $.Event('show.bs.lightbox', { relatedTarget: _relatedTarget });

		this.$element.trigger(e);

		if (this.isShown || e.isDefaultPrevented()) return;

		this.isShown = true;

		this.escape();

		this.$element.on('click.dismiss.lightbox', '[data-dismiss="lightbox"]', $.proxy(this.hide, this));
		this.updateInfo();
		// This bit is added since we don't display until we have the size
		//  which prevents image jumping
		this.preloadSize(function()
		{
			that.backdrop(function ()
			{
				var transition = $.support.transition && that.$element.hasClass('fade');

				if (!that.$element.parent().length)
				{
					that.$element.appendTo(document.body); // don't move modals dom position
				}
				
				that.$element.removeClass('hide');
				that.$element.show();

				if (transition)
				{
					that.$element[0].offsetWidth; // force reflow
				}

				that.$element
					.addClass('in')
					.attr('aria-hidden', false);

				that.enforceFocus();

				var e = $.Event('shown.bs.lightbox', { relatedTarget: _relatedTarget });

				transition ?
					that.$element.find('.lightbox-dialog') // wait for modal to slide in
						.one($.support.transition.end, function ()
						{
							that.$element.focus().trigger(e);
						})
						.emulateTransitionEnd(300) :
					that.$element.focus().trigger(e);
			});
		});
	};

	Lightbox.prototype.hide = function (e)
	{
		if (e) e.preventDefault();

		e = $.Event('hide.bs.lightbox');

		this.$element.trigger(e);

		if (!this.isShown || e.isDefaultPrevented()) return;

		this.isShown = false;

		this.escape();

		$(document).off('focusin.bs.lightbox');

		this.$element
			.removeClass('in')
			.attr('aria-hidden', true)
			.off('click.dismiss.lightbox');

		$.support.transition && this.$element.hasClass('fade') ?
			this.$element
				.one($.support.transition.end, $.proxy(this.hideModal, this))
				.emulateTransitionEnd(300) :
			this.hideModal();
	};

	Lightbox.prototype.enforceFocus = function () {
		$(document)
			.off('focusin.bs.lightbox') // guard against infinite focus loop
			.on('focusin.bs.lightbox', $.proxy(function (e)
			{
				if (this.$element[0] !== e.target && !this.$element.has(e.target).length)
				{
					this.$element.focus();
				}
			}, this));
	};

	Lightbox.prototype.escape = function ()
	{
		if (this.isShown && this.options.keyboard)
		{
			this.$element.on('keyup.dismiss.bs.lightbox', $.proxy(function (e)
			{
				e.which == 27 && this.hide();
			}, this));
		}
		else if (!this.isShown)
		{
			this.$element.off('keyup.dismiss.bs.lightbox');
		}
	};

	Lightbox.prototype.hideModal = function () 
	{
		var that = this;
		this.$element.hide();
		this.backdrop(function ()
		{
			that.removeBackdrop();
			that.$element.trigger('hidden.bs.lightbox');
			that.$lighbox_image.addClass('hide');
			that.$element.find('.embed-responsive').replaceWith(that.$lighbox_image);
			//that.$lightbox_body.append(that.$target_img);
		});
	};

	Lightbox.prototype.backdrop = function (callback)
	{
		var that    = this;
		var animate = this.$element.hasClass('fade') ? 'fade' : '';
		if (this.isShown && this.options.backdrop)
		{
			var doAnimate = $.support.transition && animate;

			this.$backdrop = $('<div class="modal-backdrop ' + animate + '" />')
				.appendTo(document.body);

			this.$element.on('click.dismiss.lightbox', $.proxy(function (e)
			{
				if (e.target !== e.currentTarget) return;
				this.options.backdrop == 'static'
					? this.$element[0].focus.call(this.$element[0])
					: this.hide.call(this);
			}, this));

			if (doAnimate) this.$backdrop[0].offsetWidth; // force reflow

			this.$backdrop.addClass('in');

			if (!callback) return;

			doAnimate ?
				this.$backdrop
					.one($.support.transition.end, callback)
					.emulateTransitionEnd(150) :
				callback();

		}
		else if (!this.isShown && this.$backdrop)
		{
			this.$backdrop.removeClass('in');

			$.support.transition && this.$element.hasClass('fade')?
				this.$backdrop
					.one($.support.transition.end, callback)
					.emulateTransitionEnd(150) :
				callback();

		}
		else if (callback)
		{
			callback();
		}
	};

	Lightbox.prototype.preloadSize = function(callback)
	{
		var callbacks = $.Callbacks();
		if(callback) callbacks.add( callback );
		var that = this;

		var windowHeight,
			windowWidth,
			padTop,
			padBottom,
			padLeft,
			padRight,
			$image,
			preloader,
			originalWidth,
			originalHeight;
		// Get the window width and height.
		windowHeight = $(window).height();
		windowWidth  = $(window).width();

		// Get the top, bottom, right, and left padding
		/*padTop    = parseInt( that.$element.find('.lightbox-content').css('padding-top')    , 10);
		padBottom = parseInt( that.$element.find('.lightbox-content').css('padding-bottom') , 10);
		padLeft   = parseInt( that.$element.find('.lightbox-content').css('padding-left')   , 10);
		padRight  = parseInt( that.$element.find('.lightbox-content').css('padding-right')  , 10);*/
		padTop    = this.padding.top;
		padBottom = this.padding.bottom;
		padLeft   = this.padding.left;
		padRight  = this.padding.right;
		// Load the image, we have to do this because if the image isn't already loaded we get a bad size
		$image    = that.$element.find('.lightbox-content').find('img:first');
		preloader = new Image();
		preloader.onload = function()
		{
			//$image.width = preloader.width;
			//$image.height = preloader.height;
			//return _this.sizeContainer(preloader.width, preloader.height);

			// The image could be bigger than the window, that is an issue.
			if( (preloader.width + padLeft + padRight) >= windowWidth)
			{
				originalWidth = preloader.width;
				originalHeight = preloader.height;
				preloader.width = windowWidth - padLeft - padRight;
				preloader.height = originalHeight / originalWidth * preloader.width;
			}

			if( (preloader.height + padTop + padBottom) >= windowHeight)
			{
				originalWidth = preloader.width;
				originalHeight = preloader.height;
				preloader.height = windowHeight - padTop - padBottom;
				preloader.width = originalWidth / originalHeight * preloader.height;
			}
			that.$element.css({
				'z-index': 20000
			});


			// We have everything sized!
			callbacks.fire();
		};
		preloader.src = $image.attr('src');
	};


	// LIGHTBOX PLUGIN DEFINITION
	// =======================

	var old = $.fn.lightbox;

	$.fn.lightbox = function (option, _relatedTarget){
		return this.each(function(){
			var $this   = $(this);
			var data    = $this.data('bs.lightbox');
			var options = $.extend(Lightbox.DEFAULTS, {
				gallery_selector: $this.attr('data-gallery-selector'),
		        type: $this.attr('data-type')
			}, $this.data(), typeof option == 'object' && option);
			if (!data) $this.data('bs.lightbox', (data = new Lightbox(this, options)));
			if (typeof option == 'string') data[option](_relatedTarget);
			else if (options.show) data.show(_relatedTarget);
		});
	};

	$.fn.lightbox.Constructor = Lightbox;


	// MODAL NO CONFLICT
	// =================

	$.fn.lightbox.noConflict = function ()
	{
		$.fn.lightbox = old;
		return this;
	};


	// MODAL DATA-API
	// ==============
	$(document).on('click.bs.lightbox.data-api', '[data-toggle="lightbox"]', function (e){
		var $this   = $(this);
		var href    = $this.attr('href');
		var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))); //strip for ie7
		var option  = $target.data('lightbox') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data());
		e.preventDefault();
		$target
			.lightbox(option, this)
			.one('hide', function(){
				$this.is(':visible') && $this.focus();
			});
	});

	$(document)
		.on('show.bs.lightbox',  '.lightbox', function () { $(document.body).addClass('lightbox-open'); })
		.on('hidden.bs.lightbox', '.lightbox', function () { $(document.body).removeClass('lightbox-open'); });

}(window.jQuery);