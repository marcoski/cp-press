jQuery(window).load(function(){
	var $ = jQuery;
	var headerSettings = cpPressOptions.header.chpress_header_settings
	$('.wrapper').cp_slider_start(headerSettings.menu_slider_offset);
	slider_section = $('.wrapper').parents('section');
	if(slider_section.length > 0){
		slider_section.css({
			'height'			:	$(window).height(),
			'padding-bottom'	: '0px'
		});
		$(window).resize(function(){
			slider_section.css({
				'height'	:	$(window).height(),
			});
		});
	}
});

(function($){

	var images = [];
	var imgPos = -1;
	var translateTime = cpPressSliderOptions.slider.translatetime === undefined ? 5000 : parseInt(cpPressSliderOptions.slider.translatetime);
	var showTime = cpPressSliderOptions.slider.showtime === undefined ? 1000 : parseInt(cpPressSliderOptions.slider.showtime);
	var imageWidth = cpPressSliderOptions.slider.imgwidth === undefined ? 1920 : cpPressSliderOptions.slider.imgwidth;
	var imageHeight = cpPressSliderOptions.slider.imgheight === undefined ? 920 : cpPressSliderOptions.slider.imgheight;

	var centerVAdjustement = cpPressSliderOptions.slider.cvadjustment === undefined ? -80 : cpPressSliderOptions.slider.cvadjustment;
	var centerHAdjustement = cpPressSliderOptions.slider.chadjustment === undefined ? 0 : cpPressSliderOptions.slider.chadjustment;

	var imageMeta = {
		'width'		: imageWidth,
		'height'	: imageHeight
	};


	$.dimensions = function(){
		ratio = $(window).width()/imageMeta.width;
		return  imageMeta.height*ratio;
	};

	$.fn.cp_slider_start = function(offset){
		var that = this;
		var el = $(this);
		if(images.length > 0){
			$.each(images,function(i, img){
				el.append('<div class="cp-slider-image image"><img class="lazy" data-original="'+img+'" style="opacity: 0;"/></div>');
			});
		}
		el.children('.image').each(function(i, item){
			if(typeof $(item).data('link') !== 'undefined'){
				$(item).find('.cp-slider-title').css({
					cursor: 'pointer'
				});
				$(item).click(function(){
					window.location.href = $(this).data('link');
					return false;
				});
			}
			images[i] = i;
			h = $.dimensions();
			$(item).css({
				'width'					: $(window).width(),
				'height'				: h,
				'position'				: 'absolute',
				'z-index'				: '0'
			});
			$(item).children('img.lazy').css({
				'width'					: $(window).width(),
				'height'				: h
			});
			$(window).resize(function(){
				h = $.dimensions();
				$(item).css({
					'width'			: $(window).width(),
					'height'		: h
				});
				$(item).children('img.lazy').css({
					'width'					: $(window).width(),
					'height'				: h
				});
				if(cpPressSliderOptions.slider.center_logo == 1)
					el.center(offset);
				else if(cpPressSliderOptions.slider.logo_ptop == 1)
					el.ptop(offset);
				else if(cpPressSliderOptions.slider.logo_pbottom == 1)
					el.pbottom();
			});

			$(item).children('img.lazy').lazyload({
				effect : "fadeIn"
			});
		});
		el.children('.image').first().css({
			'z-index'					: '1'
		});
		if(cpPressSliderOptions.slider.center_logo == 1)
			el.center(offset);
		else if(cpPressSliderOptions.slider.logo_ptop == 1)
			el.ptop(offset);
		else if(cpPressSliderOptions.slider.logo_pbottom == 1)
			el.pbottom();
		$.presetImage(el.getSlide());
		el.slide();

		return this;
	};

	$.fn.center = function(offset){
		$info = $(this).find('.cp-slider-info');
		if($info.length > 0){
			$info.each(function(){
				infoLeft = ($(window).width()/2)-($(this).width()/2);
				if(infoLeft < 0)
					infoLeft = ($(this).width()/2)-($(window).width().width/2);
				infoTop = ($(window).height()/2)-($(this).height());
				if(infoTop < 0)
					infoTop = ($(this).height())-($(window).height()/2);

				infoTop += parseInt(offset)+centerVAdjustement;
				infoLeft += centerHAdjustement;
				$(this).css({
					'top'	: infoTop,
					'left'	: infoLeft
				});
			});
		}
	};

	$.fn.ptop = function(offset){
		$info = $(this).find('.cp-slider-info');
		if($info.length > 0){
			$info.each(function(){
				infoLeft = ($(window).width()/2)-($(this).width()/2);
				if(infoLeft < 0)
					infoLeft = ($(this).width()/2)-($(window).width().width/2);
				$(this).css({
					'top'	: offset,
					'left'	: infoLeft
				});
			});
		}
	}

	$.fn.pbottom = function(){
		$info = $(this).find('.cp-slider-info');
		if($info.length > 0){
			$info.each(function(){
				infoLeft = ($(window).width()/2)-($(this).width()/2);
				if(infoLeft < 0)
					infoLeft = ($(this).width()/2)-($(window).width().width/2);
				$(this).css({
					'bottom'	: 0,
					'left'		: infoLeft
				});
			});
		}
	}

	$.getNextPos = function(p, direction){
		nextPos = p;
		if(direction == undefined){
			direction = 'next';
		}
		if(direction == 'next'){
			nextPos = (nextPos+1)%images.length;
		}

		return nextPos;
	};

	$.fn.getNextSlide = function(direction, side){
		pos = $.getNextPos(imgPos, direction);
		if(side == undefined || !side){
			imgPos = pos;
		}
		return $(this).getSlide(pos);
	};

	$.fn.getSlide = function(pos){
		if(pos == undefined){
			pos = imgPos;
		}
		if(imgPos == -1){
			pos = 0;
		}
		slides = $(this).children('.image');
		return $(slides[pos]).children('img');
	};

	$.fn.slide = function(direction){
		that = this;
		$(this).getNextSlide(direction).each(function(i, img){
			if($.isIPhone() || $.isAndroidMobile()){
				$(img).css({
					'transform'		: 'translate3d(-350px, 0px, 0px) scale(1)'
				});
			}else{
				$(img).css({
					'transform'		: 'translate3d(0px, 0px, 0px) scale(1)'
				});
			}
			$.imageShow(img, function(){$(img).tweet();});
		});

		return this;
	};

	$.fn.tweet = function(){
		that = this;
		el = $(this);
		toSlide = $(this).parent().parent();
		if(imgPos%2 == 0){
			translate = {x: +50, y: +20, z: 0, scale: 1.15};
		}else{
			translate = {x: 0, y: 0, z: 0, scale: 1.10};
		}


		el.translate3d(translate, translateTime, 'ease', function(){
			$.imageHide(that, function(){});
			toSlide.slide();
		});



		return this;
	};

    $.presetImage = function(obj){
    	obj.parent().css({'z-index': '2'});
    };

	$.imageShow = function(img, callback){
		$(img).animate({
			'opacity': 1
		}, showTime, "linear", function(){
			$(img).parent().css({'z-index': '1'});
			callback();
		});
	};

	$.imageHide = function(img, callback){
		$(img).parent().css({'z-index': '1'});
		$.presetImage($(img).parent().parent().getNextSlide('next', true));
		$(img).animate({
			'opacity': 0
		}, showTime, "linear", function(){
			$(img).parent().css({'z-index': '0'});
			callback();
		});
	};
}(jQuery));
