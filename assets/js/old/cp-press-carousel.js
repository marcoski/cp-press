(function($){
	
	var CpCarousel = function (element){
		var that = this;
		this.$element  = $(element);
		this.elementData = this.$element.data();
		this.$caoursel = this.$element.find('.cp-carousel');
		this.moveX = this.$element.find('.cp-carousel-box').width();
		this.moveY = this.$element.find('.cp-carousel-box').find('.cp-carousel-mask').height();
		
		this.$element.find('.row').each(function(key, value){
			if(key > 0){
				$(this).css({
					 transitionDuration: '0ms',
					 transform: 'translateX(' + that.moveX + 'px)'
				});
			}
		});
		CpCarousel.prototype.constructor = CpCarousel;
	}
	
	CpCarousel.prototype.getMaxHeight = function(){
		return Math.max.apply(null, this.$element.find('.row').map(function(){
			return parseInt($(this).css('height'));
		}));
	}
	
	CpCarousel.prototype.setViewPort = function(offset){
		var that = this;
		if(typeof(offset) === 'undefined')
			offset = 0;
		this.$element.find('.cp-carousel-mask').each(function(){
			padTop    = parseInt( that.$element.find('.row').css('padding-top')    , 10);
			padBottom = parseInt( that.$element.find('.row').css('padding-bottom') , 10);
			elHeight = parseInt(that.getMaxHeight(), 10);
				
			$(this).css({
				height: (elHeight - (padTop + padBottom))+offset
			});
		});
	}
	
	CpCarousel.prototype.show = function($target, column){
		var that = this;
		$toShow = this.$element.find('.row[id="row-'+column+'"]');
		$toUnCurrent = null;
		this.$element.find('span').each(function(){
			if($(this).hasClass('current-slide')){
				$toUnCurrent = $(this);
				return;
			}
		});
		$toHide = null;
		this.$element.find('.row').each(function(){
			unCurAData = $toUnCurrent.parent().data();
			col = $(this).attr('id').split('-')[1];
			if(col == unCurAData.column){
				$toHide = $(this);
				return;
			}
		});
		$target.find('span').addClass('current-slide');
		$toUnCurrent.removeClass('current-slide');
		$toHide.translate(
			{x: -that.moveX, y: 0, z: 0, scale: 1},
			1000,
			'ease',
			function(){
				$toHide.translate(
					{x: -that.moveX, y: that.moveY, z: 0, scale: 1},
					0,
					'ease',
					function(){
						$toHide.translate(
							{x: that.moveX, y: that.moveY, z: 0, scale: 1},
							0,
							'ease',
							function(){
								$toHide.translate(
									{x: that.moveX, y: 0, z: 0, scale: 1},
									0
								);
							}
						);
					}
				);
			}
		);
		$toShow.translate(
			{x: 0, y: 0, z: 0, scale: 1},
			1000,
			'ease',
			function(){}
		);
			
	};
	
	CpCarousel.prototype.next = function($target){
		aData = this.$caoursel.data();
		col = (aData.column+1) % aData.count;
		if(col.toString().length > 1)
			mycol = parseInt(col.toString().charAt(1));
		else
			mycol = col
		this.$caoursel.data('column', col);
		this.$caoursel.attr('data-column', col);
		
		this.show(this._findDot(mycol), mycol);
	}
	
	CpCarousel.prototype.prev = function($target){
		aData = this.$caoursel.data();
		col = (aData.column - 1) % aData.count;
		if(col < 0){
			col = aData.count - 1;
		}
		if(col.toString().length > 1)
			mycol = parseInt(col.toString().charAt(1));
		else
			mycol = col
		this.$caoursel.data('column', col);
		this.$caoursel.attr('data-column', col);
		this.show(this._findDot(mycol), mycol);
	}
	
	CpCarousel.prototype._findDot = function(col){
		return this.$element.find('a.dot[data-column="'+col+'"]');
	}
	
	$.fn.cpcarousel = function(element){
		return new CpCarousel(element);
	};

	$.fn.cpcarousel.Constructor = CpCarousel;
}(jQuery));