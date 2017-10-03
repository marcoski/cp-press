jQuery(document).ready(function(){
	var $ = jQuery;
	var $portfolioTable = $('table.cp-portfolio');
	if($portfolioTable.length){
		var portfolio = $portfolioTable.cpportfolioitem();
		portfolio.super.$element.on('click.delete', '.cp-row-delete', function(){
			portfolio.super.delete($(this));
		});
		
		portfolio.super.$element.find('.add-item').on('click.addItem', function(event){
			event.preventDefault();
			if(!$(this).hasClass('disabled')){
				portfolio.addItem($(this));
			}
		});
	}
	
});

(function($){
	
	var CpPortfolioItem = function(element){
		this.super = new $.fn.cpitem(element);
		CpPortfolioItem.prototype.constructor = CpPortfolioItem;
		
		this.init();
	};
	
	CpPortfolioItem.prototype.init = function(){
		this.$items = this.super.$element.find('.cp-item');
		this.super.$deleteDialogContent = $("<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>These item will be deleted from this portfolio. Are you sure?</p>");
		this.super.deleteInfo = {
			title		: 'Delete Item',
			action		: 'delete_item',
			selector	: 'tr.cp-item[data-item=%s]'
		};
		this.super.cpAjax = $.fn.cpajax(this);
		this.accordionIcons = {
			header: "ui-icon-circle-arrow-e",
			activeHeader: "ui-icon-circle-arrow-s"
		};
	};
	
	CpPortfolioItem.prototype.addItem = function($element){
		var that = this;
		var portfolio = this.super.$element.attr('id').split('_')[1];
		this.super.cpAjax.call('add_item_modal', function(response){
			that.super.$dialog.html(response.data);
			that.super.dialog('Add Item', {
				height: $(window).width()/3,
				buttons: {
					Add: function(){ that.actionAddItem(that, $(this)); }
				}
			});
			$("#cp-accordion").accordion({
				icons: that.accordionIcons,
				active: false,
				heightStyle: "fill"
			});
			$( ".cp-selectable" ).selectable();
		}, {portfolio_id: portfolio});
	};
	
	CpPortfolioItem.prototype.actionAddItem = function(that, $dialog){
		var $selectedItems = $('.cp-selectable > li.ui-selected');
		$selectedItems.each(function(){
			item_id = $(this).data('item').split('-')[1];
			type = $(this).data('item').split('-')[0];
			that.super.cpAjax.call('add_item', function(response){
				that.super.$element.append(response.data);
			}, {item_id: item_id, post_type: type});
		});
		$dialog.dialog('close');
	};
	
	$.fn.cpportfolioitem = function(){
		return new CpPortfolioItem(this);
	};

	$.fn.cpportfolioitem.Constructor = CpPortfolioItem;
	
}(jQuery));