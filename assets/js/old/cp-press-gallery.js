jQuery(window).load(function(){
	var $ = jQuery;
	
	/*$('.cp-gallery-image-wrapper').first().find('img').addClass('opacize');
	$('.cp-gallery-image-wrapper').click(callbacks.galleryCp);
	$('.lightbox-image-wrapper').click(callbacks.galleryLightBox);*/
	
	$('.cp-gallery[id^="cp-gallery"]').each(function(key){
		$(this).cpgallery();
	});
	
	$('.cp-gallery-thumb-only[id^="cp-gallery"]').each(function(key){
		$(this).cpgallerythumbsonly();
	});
	
	$('.cp-gallery-viewcarousel').cpgallerycarousel();
	
});

(function($){
	
	var CpGallery = function (element){
		this.$element  = $(element)
		this.opacizer = true;
		if(this.$element.hasClass('cp-gallery-viewlist'))
			this.opacizer = false;
	}
	
	CpGallery.prototype.start = function(){
		if(this.opacizer){
			this.$element.find('.cp-gallery-image-wrapper').first().find('img').addClass('opacize');
			this.opacizeLightbox();
		}
	};
	
	CpGallery.prototype.opacizeLightbox = function($oldThumb){
		$lightBoxSelected = this.$element.find('.lightbox-image-wrapper').find('img[src="'+$('.lightbox-image').find('img').attr('src')+'"]');
		this.opacize($lightBoxSelected, $oldThumb);
	};
		
	CpGallery.prototype.opacize = function($thumb, $oldThumb){
		if(this.opacizer){
			if($thumb.hasClass('opacize')){
				$thumb.removeClass('opacize');
				$thumb.addClass('unopacize');
			}else if($thumb.hasClass('unopacize')){
				$thumb.removeClass('unopacize');
				$thumb.addClass('opacize');
			}else{
				$thumb.addClass('opacize');
			}
			if(typeof($oldThumb) !== "undefined"){
				if($oldThumb.hasClass('opacize')){
					$oldThumb.removeClass('opacize');
					$oldThumb.addClass('unopacize');
				}else if($oldThumb.hasClass('unopacize')){
					$oldThumb.removeClass('unopacize');
					$oldThumb.addClass('opacize');
				}
			}
		}
	};
	
	CpGallery.prototype.galleryCp = function($target){
		this.gallery($target, '.cp-gallery');
	};

	CpGallery.prototype.galleryLightBox = function($target){
		this.gallery($target, '.lightbox');
	};

	CpGallery.prototype.gallery = function($that, imageClass){
		$lightBoxImg = this.$element.find('.lightbox-image').find('img');
		lightBoxImageSrc = $lightBoxImg.attr('src');
		$thumb = $that.find('img');
		thumbData = $thumb.data();
		if($that.hasClass('cp-video')){
			$image = this.$element.find(imageClass+'-video').find('iframe');
			oldSrc = this.$element.find(imageClass+'-video').find('iframe').attr('src');
			if(this.$element.find(imageClass+'-image').is(':visible')){
				oldSrc = this.$element.find(imageClass+'-image').find('img').attr('src');
			}
			if(!this.$element.find(imageClass+'-video').is(':visible')){
				this.$element.find(imageClass+'-video').show();
				this.$element.find(imageClass+'-image').hide();
			}
		}else{
			$image = this.$element.find(imageClass+'-image').find('img');
			oldSrc = this.$element.find(imageClass+'-image').find('img').attr('src');
			if(this.$element.find(imageClass+'-video').is(':visible')){
				oldSrc = this.$element.find(imageClass+'-video').find('iframe').attr('src');
			}
			if(!this.$element.find(imageClass+'-image').is(':visible')){
				this.$element.find(imageClass+'-image').show();
				this.$element.find(imageClass+'-video').hide();
			}
		}
		imgSrc = thumbData.src;
		$oldThumb = this.$element.find(imageClass+'-thumbs').find('img[data-src="'+oldSrc+'"]');
		$image.attr('src', imgSrc);
		this.changeLightBox($lightBoxImg, imgSrc, thumbData);
		if(imageClass === '.lightbox'){
			this.opacizeLightbox(this.$element.find('.lightbox-image-wrapper').find('img[src="'+lightBoxImageSrc+'"]'));
		}else if(imageClass === '.cp-gallery'){
			this.opacize($thumb, $oldThumb);
		}
	};
	
	CpGallery.prototype.changeLightBox = function($img, $src, thumbData){
		if (typeof $src == 'string' || $src instanceof String)
			$img.attr('src', $src);
		else
			$img.attr('src', $src.attr('src'));
		$img.attr('width', thumbData.originalWidth);
		$img.attr('height', thumbData.originalHeight);
		if(!(typeof $src == 'string' || $src instanceof String)){
			$lightBoxImg = this.$element.find('.lightbox-image');
			$lightBoxVideo = this.$element.find('.lightbox-video');
			if($src.hasClass('cp-video')){	
				$lightBoxVideoIframe = $lightBoxVideo.find('iframe');
				videoData = $src.data();
				$lightBoxVideoIframe.attr('src', videoData.src);
				$lightBoxVideoIframe.attr('width', thumbData.originalWidth);
				$lightBoxVideoIframe.attr('height', thumbData.originalHeight);
				this.adjustVideoSize($lightBoxVideo);
				if($lightBoxImg.is(':visible')){
					$lightBoxImg.hide();
					$lightBoxVideo.show();
				}
			}else{
				if(!$lightBoxImg.is(':visible')){
					$lightBoxImg.show();
					$lightBoxVideo.hide();
				}
			}
		}
	};
	
	CpGallery.prototype.lightBoxLoadNext = function(){
		$lightBoxImg = this.$element.find('.lightbox-image').find('img');
		lightBoxImageSrc = $lightBoxImg.attr('src');
		$gallery = this.$element.find('.lightbox-image-container img');
		$cur = null;
		$gallery.each(function(){
			if($(this).attr('src') === lightBoxImageSrc){
				$cur = $(this);
				return;
			}
		});
		$next = $cur.next();
		if($next.length == 0){
			$next = this.$element.find('.lightbox-image-container img').first();
		}
		nextData = {
			originalWidth:	$next.attr('width'),
			originalHeight:	$next.attr('height')
		};
		this.changeLightBox($lightBoxImg, $next, nextData);
	};
	
	CpGallery.prototype.lightBoxLoadPrev = function($link){
		$lightBoxImg = this.$element.find('.lightbox-image').find('img');
		lightBoxImageSrc = $lightBoxImg.attr('src');
		$gallery = this.$element.find('.lightbox-image-container img');
		$cur = null;
		$gallery.each(function(){
			if($(this).attr('src') === lightBoxImageSrc){
				$cur = $(this);
				return;
			}
		});
		$prev = $cur.prev();
		if($prev.length == 0){
			$prev = this.$element.find('.lightbox-image-container img').last();
		}
		prevData = {
			originalWidth:	$prev.attr('width'),
			originalHeight:	$prev.attr('height')
		};
		this.changeLightBox($lightBoxImg, $prev, prevData);
	};

	CpGallery.prototype.adjustVideoSize = function($video){
		var that = this;
		padTop    = parseInt( that.$element.find('.lightbox-content').css('padding-top')    , 10);
		padBottom = parseInt( that.$element.find('.lightbox-content').css('padding-bottom') , 10);
		lightBoxHeight = parseInt( that.$element.find('.lightbox-content').css('height'), 10);
		
		$video.css({
			'padding-bottom': lightBoxHeight - (padTop + padBottom)
		});
		
	};

	$.fn.cpgallery = function (option, _relatedTarget)
	{
		return this.each(function ()
		{
			var gallery = new CpGallery(this);
			gallery.start();
			$(this).find('.cp-gallery-image-wrapper').click(function(){
				gallery.galleryCp($(this));
			});
			$(this).find('.lightbox-image-wrapper').click(function(){
				gallery.galleryLightBox($(this));
			});
			$(this).find('.lightbox').find('.cp-arrow-next').click(function(){
				gallery.lightBoxLoadNext();
			});
			$(this).find('.lightbox').find('.cp-arrow-prev').click(function(){
				gallery.lightBoxLoadPrev();
			});
		})
	};
	
	$.fn.cpgallerythumbsonly = function(option, _relatedTarget){
		$('.cp-gallery-thumb-only .cp-gallery-thumbs img').each(function(){
			$(this).click(function(){
				$('.cp-gallery-thumb-only lightbox').lightbox();
			});
		});
	};

	$.fn.cpgallery.Constructor = CpGallery;
}(jQuery));