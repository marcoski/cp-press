(function($){
	
	var CpGalleryCarousel = function (element){
		this.super = new $.fn.cpcarousel(element);
		CpGalleryCarousel.prototype.constructor = CpGalleryCarousel;
		
		this.init();
	}
	
	CpGalleryCarousel.prototype.init = function(){
		var that = this;
		this.$box = this.super.$element.find('.cp-gallery-image');
		/* set the box height with aspect ratio */
		this.setAspectRatioH(this.$box);
		
		
		/* resize image in the box */
		$galleryImage = this.$box.find('img');
		$galleryImage.css({
			width: this.$box.innerWidth(),
			height: this.$box.innerHeight()
		});
		
		/* uniform thumbs aspect ratio */
		this.super.$element.find('.cp-carousel-mask img').each(function(){
			that.setAspectRatioH($(this), $(this).parent(), 'outer');
			that.setAspectRatioW($(this), $(this).parent(), 'outer');
		});
		
		/* uniform thumb height */
		var $firstThumb = this.super.$element.find('.cp-carousel-mask img').first();
		this.super.$element.find('.cp-carousel-mask img').each(function(){
			$(this).css({
				height: $firstThumb.outerHeight()
			});
		});
		
		this.super.setViewPort();
	};
	
	CpGalleryCarousel.prototype.setAspectRatioH = function($target, $ref, property){
		if(typeof($ref) === 'undefined')
			$ref = $target;
		if(typeof(property) === 'undefined')
			newBoxHeight = parseInt((this.super.elementData.aspectRatioY / this.super.elementData.aspectRatioX) * $ref.width());
		else if(property == 'inner')
			newBoxHeight = parseInt((this.super.elementData.aspectRatioY / this.super.elementData.aspectRatioX) * $ref.innerWidth());
		else if(property == 'outer')
			newBoxHeight = parseInt((this.super.elementData.aspectRatioY / this.super.elementData.aspectRatioX) * $ref.outerWidth());
		$target.css({
			height: newBoxHeight
		});
	}
	
	CpGalleryCarousel.prototype.setAspectRatioW = function($target, $ref, property){
		if(typeof($ref) === 'undefined'){
			$ref = $target;
		}
		if(typeof(property) === 'undefined')
			newBoxWidth = parseInt((this.super.elementData.aspectRatioX / this.super.elementData.aspectRatioY) * $ref.height());
		else if(property == 'inner')
			newBoxWidth = parseInt((this.super.elementData.aspectRatioX / this.super.elementData.aspectRatioY) * $ref.innerHeight());
		else if(property == 'outer')
			newBoxWidth = parseInt((this.super.elementData.aspectRatioX / this.super.elementData.aspectRatioY) * $ref.outerHeight());
		$target.css({
			width: newBoxWidth
		});
	};
	
	CpGalleryCarousel.prototype.setHandler = function(){
		var carousel = this;
		this.super.$element.find('.cp-gallery-carousel a.dot').click(function(event){
			event.preventDefault();
			aData = $(this).data();
			col = aData.column;
			carousel.super.show($(this), col);
		});
		this.super.$element.find('.cp-gallery-carousel .cp-arrow-next a').click(function(event){
			event.preventDefault();
			carousel.super.next($(this));
		});
		this.super.$element.find('.cp-gallery-carousel .cp-arrow-prev a').click(function(event){
			event.preventDefault();

			carousel.super.prev($(this));
		});
	};
	
	
	
	$.fn.cpgallerycarousel = function(){
		return this.each(function ()
		{
			var carousel = new CpGalleryCarousel(this);
			carousel.setHandler();
		});
	};

	$.fn.cpgallerycarousel.Constructor = CpGalleryCarousel;
}(jQuery));