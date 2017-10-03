jQuery(window).ready(function(){
    var $ = jQuery;
    $('.cp-portfolio').cpportfolio(cpPressPortfolioOptions);

});

;(function($){
    var CpPortfolio = function (element, settings){
      this.settings = settings;
  		this.$element = $(element);
  		if(browserMatch.mobile === false){
  			this.$items = $(element).find('.cp-portfolio-hover');
  		}else{
  			this.$items = $(element).find('.cp-portfolio-item');
  		}

    };

	CpPortfolio.prototype.init = function(){
		var that = this;
		var boxHeight;
		var boxSlideUp;
		if(this.settings !== null){
			var boxHeight = this.settings.boxheight === undefined ? 'auto' : parseInt(this.settings.boxheight);
			var boxSlideUp = this.settings.boxslide === undefined ? '155' : this.settings.boxslide;
		}else{
			var boxHeight = 'auto';
			var boxSlideUp = '155';
		}

		this.$items.each(function(){
			var $wrap = $(this).children('.cp-portfolio-item');
			if(typeof $wrap.data('hide') !== 'undefined'){
				$wrap.css({
					'cursor': 'pointer'
				});
				
			}
			if(typeof $wrap.data('link') !== 'undefined'){
				$wrap.click(function(){
					window.location.href = $(this).data('link');
				});
			}
			$wrap.css({
				'overflow'		: 'hidden',
				'height'		: boxHeight+'px'
			});
			that.imgLazyLoad($wrap);
			$wrap.hover(
				function(){that.inside($wrap, boxSlideUp);},
				function(){that.outside($wrap);}
			);
		});
	};

	CpPortfolio.prototype.initMobile = function(){
		var that = this;
		this.$items.each(function(){
			that.imgLazyLoad($(this));
		});
	};

	CpPortfolio.prototype.imgLazyLoad = function($el){
		$el.find('img.lazy').lazyload({
			effect : "fadeIn",
			threshold : 200,
			effect_speed: 500
		});
	};

	CpPortfolio.prototype.inside = function($el, boxSlideUp){
		var $transitionEl = $el.children('.details').children('.info-wrap');
		if(typeof $el.data('hide') !== 'undefined'){
			$transitionEl = $el.children('.details');
		}
		$transitionEl.transition(
			{'opacity': 1},
			10,
			function(){
				$el.children('.details').transition({
					'y'	: '-'+boxSlideUp+'px'
				});
			}
		);
    };

    CpPortfolio.prototype.outside = function($el){
		var $transitionEl = $el.children('.details');
		$transitionEl.transition({
			'y'	: '-20px'
		});
		$transitionEl.children('.info-wrap').transition({'opacity':0}, 100);
    };


    $.fn.cpportfolio = function(settings){
		return this.each(function ()
		{
			var $el = $(this);
			var portfolio = new CpPortfolio(this, settings);
			if(browserMatch.mobile === false){
				portfolio.init();
			}else{
				portfolio.initMobile();
			}
		});
	};

	$.fn.cpportfolio.Constructor = CpPortfolio;
}(jQuery));
