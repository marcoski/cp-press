jQuery(document).ready(function(){
	var $ = jQuery;
	
	var $linkTable = $('table.cp-link');
	if($linkTable.length){
		var linker = $linkTable.cplinkitem();
		linker.super.$element.on('click.delete', '.cp-row-delete', function(){
			linker.super.delete($(this));
		});
		linker.super.$element.on('click.addimage', '.cp-row-image', function(){
			linker.addMedia($(this));
		});
		linker.super.$element.find('.add-link').on('click.addLink', function(event){
			event.preventDefault();
			if(!$(this).hasClass('disabled')){
				linker.addLink($(this));
			}
		});
	}
});
(function($){
	
	var CpLinkItem = function(element){
		this.super = new $.fn.cpitem(element);
		CpLinkItem.prototype.constructor = CpLinkItem;
		
		this.init();
	};
	
	CpLinkItem.prototype.init = function(){
		this.$links = this.super.$element.find('.cp-link');
		this.super.$deleteDialogContent = $("<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>These link will be deleted from this content. Are you sure?</p>");
		this.super.deleteInfo = {
			title		: 'Delete Link',
			action		: 'delete_link',
			selector	: 'tr.cp-link[data-item=\'%s\']'
		};
		this.super.cpAjax = $.fn.cpajax(this);
		this.accordionIcons = {
			header: "ui-icon-circle-arrow-e",
			activeHeader: "ui-icon-circle-arrow-s"
		};
	};
	
	CpLinkItem.prototype.addLink = function($element){
		var that = this;
		var content = this.super.$element.attr('id').split('_')[1];
		
		this.super.cpAjax.call('add_link_modal', function(response){
			that.super.$dialog.html(response.data);
			that.super.dialog('Add Link', {
				height: 200,
				buttons: {
					Add: function(){ that.actionAddLink(that, $(this)); }
				}
			});
			$('.cp-link-url').on('keypress', 'input.cp-filter', function(event){
				if(event.keyCode == 13){
					that.linkInfo($(this));
				}
			});
			$('.cp-link-url').find('input.cp-filter').blur(function(event){
				that.linkInfo($(this));
			});
		}, {content_id: content});
	};
	
	CpLinkItem.prototype.linkInfo = function($element){
		var $status = $('.cp-link-status');
		if(this.isUrl($element.val())){
			$status.find('.error').remove();
			$status.html('<div style="width:99%; padding: 5px;" class="updated below-h2">Url is valid!!!</div>');
		}else{
			$status.find('.updated').remove();
			$status.html('<div style="width:99%; padding: 5px;" class="error form-invalid below-h2">Invalid url</div>');
		}
	};
	
	CpLinkItem.prototype.actionAddLink = function(that, $dialog){
		var that = this;
		uri = $dialog.find('input.cp-filter').val();
		this.super.cpAjax.call('process_link', function(response){
			that.super.$element.append(response.data);
			$dialog.dialog('close');
		}, {uri: uri});
	};
	
	CpLinkItem.prototype.addMedia = function($element){
		var that = this;
		var cpmedia = $.fn.cpmedia('Add new media');
		var id = $element.attr('id').split('-')[3];
		cpmedia.open();
		cpmedia.mediaFrame.on('select', function(){
			var imgUri = cpmedia.selectedObj.sizes.thumbnail.url;
			$img = that.super.$element.find('#cp_link_'+id).find('.cp-link-image');
			$imgInput = that.super.$element.find('#cp_link_'+id).find('input[name="cp-press-link['+id+'][image]"]');
			$img.attr('src', imgUri);
			$imgInput.val(imgUri);
		});
	};
	
	CpLinkItem.prototype.isUrl = function(s) {
		var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
		return regexp.test(s);
	}
	
	$.fn.cplinkitem = function(){
		return new CpLinkItem(this);
	};

	$.fn.cplinkitem.Constructor = CpLinkItem;
	
}(jQuery));