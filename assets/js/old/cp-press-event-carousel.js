(function($){
	
	var CpEventCarousel = function (element){
		this.super = new $.fn.cpcarousel(element);
		CpEventCarousel.prototype.constructor = CpEventCarousel;
		this.init();
	}
	
	CpEventCarousel.prototype.init = function(){
		this.super.setViewPort(50);
	};
	
	CpEventCarousel.prototype.setHandler = function(){
		var carousel = this;
		this.super.$element.find('.cp-events-carousel a.dot').click(function(event){
			event.preventDefault();
			aData = $(this).data();
			col = aData.column;
			carousel.super.show($(this), col);
		});
		this.super.$element.find('.cp-events-carousel .cp-arrow-next a').click(function(event){
			event.preventDefault();
			carousel.super.next($(this));
		});
		this.super.$element.find('.cp-events-carousel .cp-arrow-prev a').click(function(event){
			event.preventDefault();

			carousel.super.prev($(this));
		});
	};
	
	
	$.fn.cpeventcarousel = function(settings){
		return this.each(function ()
		{
			var $el = $(this);
			var carousel = new CpEventCarousel(this);
			carousel.setHandler();
			setInterval(function(){
				carousel.super.next($el);
			}, settings.scroll_time*1000);
		});
	};

	$.fn.cpeventcarousel.Constructor = CpEventCarousel;
}(jQuery));