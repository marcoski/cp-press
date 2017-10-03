(function($, _){
	
	$.CpField = {
		View: {},
		fn: {}
	};
	
	$.CpField.fn.enable = function($el, options){
	 options || (options={});
	 options = _.extend({exclude: []}, options);
	 if($.inArray('colorpicker', options.exclude) < 0){
	   $.CpField.fn.enableColorPicker($el);
	 }
	 if($.inArray('toggler', options.exclude) < 0){
	   $.CpField.fn.enableToggler($el);
	 }
	 if($.inArray('icon', options.exclude) < 0){
    	$.CpField.fn.enableIcon($el);
    }
    if($.inArray('media', options.exclude) < 0){
      $.CpField.fn.enableMedia($el);
    }
    if($.inArray('linker', options.exclude) < 0){
      $.CpField.fn.enableLinker($el);
    }
    if($.inArray('repeater', options.exclude) < 0){
      $.CpField.fn.enableRepeater($el);
    }
    if($.inArray('accordion', options.exclude) < 0){
      $.CpField.fn.enableAccordion($el);
    }
    if($.inArray('clearall', options.exclude) < 0){
      $.CpField.fn.clearAllMultipleSelect($el);
    }
    $el.trigger('cpsetupform');
   };
   
  $.CpField.fn.clearAllMultipleSelect = function($el){
    $el.find('.cp-clear-all').click(function(){
      $select = $(this).siblings('select');
      $select.find('option:selected').prop('selected', false);
    });
  };
	
	$.CpField.fn.enableToggler = function($el){
	 $el.find('.cp-widget-type-section').each(function(){
     new $.CpField.View.Toggler({
      el: $(this)
     });
   });
	};
	
	$.CpField.fn.enableIcon = function($el){
	 $el.find('.cp-widget-type-icon-section').each(function(){
     new $.CpField.View.Icon({
      el: $(this) 
     });
   });
	};
	
	$.CpField.fn.enableMedia = function($el){
	 $el.find('.cp-widget-field-type-media').each(function(){
     new $.CpField.View.Media({
      el: $(this)
     });
   });
	};
	
	$.CpField.fn.enableLinker = function($el){
	 $el.find('.cp-widget-field-type-link').each(function(){
     new $.CpField.View.Linker({
      el: $(this)
     });
   });
	};
	
	$.CpField.fn.enableRepeater = function($el){
	 $el.find('.cp-widget-field-repeater').each(function(){
     new $.CpField.View.Repeater({
      el: $(this)
     });
   });
	};
	
	$.CpField.fn.enableColorPicker = function($el){
	 $el.find('.cp-widget-field-color').each(function(){
	   $(this).find('input').wpColorPicker();
	 });
	};
	
	$.CpField.fn.enableAccordion = function($el){
	 $el.find('.cp-widget-accordion').each(function(){
	   new $.CpField.View.Accordion({
	     el: $(this)
	   });
	 });
	};
	
	/**
	 * Toggler Handlers
	 */
	$.CpField.View.Toggler = Backbone.View.extend({
	   events: {
       'click label.section': 'toggle'
     },
     
     $section: null,
     
     initialize: function(){
        this.$section = this.$el.find('.cp-widget-section');
     },
     
     toggle: function(e){
      var $$ = $(e.target);
      $$.toggleClass('cp-widget-type-section-visible');
      this.$section.slideToggle(function(){
        $(window).resize();
      });
     }
     
	});
	
	/**
	 * Icon Field Handlers
	 */
	$.CpField.View.Icon = Backbone.View.extend({
	   iconsCache: {},
	   cpAjax: null,
	   events: {
	     'click .cp-widget-icon-selector-current': 'toggle',
	     'change .cp-widget-icon-family': 'changeSet'
	   }, 
	   $section: null,
	   $input: null,
	   $current: null,
	   $selector: null,
	   $color: null,
	  
	   initialize: function(){
	     this.cpAjax = $.fn.cpajax(this);
	     this.$input = this.$el.find('input.cp-widget-input-icon');
	     this.$current = this.$el.find('.cp-widget-icon-selector-current');
	     this.$selector = this.$el.find('.cp-widget-icon-selector');
	     this.changeSet();
	   },
	   
	   changeSet: function(){
	     var family = this.$selector.find('select.cp-widget-icon-family').val();
	     if(typeof family === 'undefined' || family === ''){
	       return;
	     }
	     if(typeof this.iconsCache[family] === 'undefined'){
	       var _that = this;
	       this.cpAjax.call('icon_family', function(data){
	         _that.iconsCache[family] = data;
	         _that.render();
	       }, {family: family});
	     }else{
	       this.render();
	     }
	   },
	   
	   render: function(){
	     var _that = this;
	     var family = this.$selector.find('select.cp-widget-icon-family').val();
	     var container = this.$selector.find('.cp-widget-icon-icons');
	     if(typeof this.iconsCache[family] === 'undefined'){
	       return this;
	     }
	     
	     container.empty();
	     
	     if($('#cp-widget-font-'+family).length == 0){
	       $("<link rel='stylesheet' type='text/css'>")
	        .attr('id', 'cp-widget-font-' + family)
          .attr('href', this.iconsCache[family].style)
          .appendTo('head');
	     }
	     for(var i in this.iconsCache[family].icons){
	       var icon = $('<div data-cp-icon="' + this.iconsCache[family].icons[i] +  '"/>')
	         .attr('data-value', family + '-' + i)
	         .addClass( 'cp-icon-' + family )
	         .addClass( 'cp-widget-icon-icons-icon' )
	         .click(function(){
	           if($(this).hasClass('cp-widget-active')){
	             $(this).removeClass('cp-widget-active');
	             _that.$input.val('');
	             _that.$current.find('span').hide();
	           }else{
	             container.find('.cp-widget-icon-icons-icon').removeClass('cp-widget-active');
	             $(this).addClass('cp-widget-active');
	             _that.$input.val($(this).data('value'));
	             _that.$current.find('span')
	               .show()
	               .attr('data-cp-icon', $(this).attr('data-cp-icon'))
                 .attr('class', '')
                 .addClass('cp-icon-' + family);
	           }
	           _that.$input.trigger('change');
	           _that.$selector.slideUp();
	         });
	       container.append(icon);
	       if(this.$input.val() === family + '-' +i){
	         if(!icon.hasClass('cp-widget-active')){
	           icon.click();
	         }
	         icon.addClass('cp-widget-active');
	       }
	     }
	   },
	   
	   toggle: function(){
	     this.$selector.slideToggle();
	   },
	   
	   
	});
	
	/**
	 * Media button handler
	 */
	$.CpField.View.Media = Backbone.View.extend({
	   events: {
       'click a.media-upload-button': 'add',
       'click a.media-remove-button': 'remove',
       'mouseeneter .current': 'showTooltip',
       'mouseleave .current': 'hideTooltip'
     },
     
     wpMediaFrame: null,
     $input: null,
     $removeButton: null,
     
     initialize: function(){
      this.$input = this.$el.find('.cp-widget-input-media');
      this.$removeButton = this.$el.find('.media-remove-button');
     },
     
     add: function(e){
      e.preventDefault();
      if(typeof wp.media === 'undefined'){
       return;
      }
      $$ = $(e.target);
      this.wpMediaFrame = $$.data('frame');
      if(this.wpMediaFrame){
        this.wpMediaFrame.open();
        return false;
      }
      
      this.wpMediaFrame = wp.media({
        // Set the title of the modal.
        title: $$.data('choose'),
  
        // Tell the modal to show only images.
        library: {
          type: $$.data('library').split(',').map(function(v){ return v.trim() })
        },
  
        // Customize the submit button.
        button: {
          // Set the text of the button.
          text: $$.data('update'),
          // Tell the button not to close the modal, since we're
          // going to refresh the page when the image is selected.
          close: false
        }
      });
      
      $$.data('frame', this.wpMediaFrame);
      this.wpMediaFrame.on('select', this.onSelectMedia, this);
      this.wpMediaFrame.open();
      return false;
      
     },
     
     onSelectMedia: function(){
      var attachment = this.wpMediaFrame.state().get('selection').first().attributes;
      this.$el.find('.current .title').html(attachment.title);
      this.$input.val(attachment.id);
      this.$input.trigger('change');
      if(typeof attachment.sizes !== 'undefined'){
        if(typeof attachment.sizes.thumbnail !== 'undefined'){
          this.$el.find('.current .thumbnail').attr('src', attachment.sizes.thumbnail.url).fadeIn();
        }else{
           this.$el.find('.current .thumbnail').attr('src', attachment.sizes.full.url).fadeIn();
        }
      }else{
         this.$el.find('.current .thumbnail').attr('src', attachment.icon).fadeIn();
      }
      
      this.$removeButton.removeClass('remove-hide');
      this.wpMediaFrame.close();
      
     },
     
     remove: function(e){
      e.preventDefault();
      this.$el.find('.current .title').html('');
      this.$input.val('');
      this.$el.find('.current .thumbnail').fadeOut('fast');
      this.$removeButton.addClass('remove-hide');
     },
     
     showTooltip: function(){
      var $t =this.$el.find('.current .title');
      if($t.html() !== ''){
        $t.fadeIn('fast');
      }
     },
     
     hideTooltip: function(){
      this.$el.find('.current .title').clearQueue().fadeOut('fast');
     }
     
	});
	
	/**
   * Linker button handler
   */
  $.CpField.View.Linker = Backbone.View.extend({
      events: {
        'click .select-content-button': 'toggle',
        'click .button-close': 'toggle',
        'click .posts li': 'select',
        'keyup .content-text-search': 'interval',
      },
      
      cpAjax: null,
      intervalTimeOut: null,
      args: null,
      action: null,
      $search: null,
      $input: null,
      $wtitle: null,
      $ul: null,
      $s: null,
      
      
      initialize: function(){
        this.cpAjax = $.fn.cpajax(this);
        if(this.$el.hasClass('cp-widget-field-type-taxonomy')){
            this.args = this.$el.data('excluded-taxonomies');
            this.action = 'widget_search_taxonomy'
        }else{
            this.args = this.$el.data('valid-types');
            this.action = 'widget_search_post';
        }
        this.$search = this.$el.find('.content-text-search');
        this.$input = this.$el.find('.url-input-wrapper input');
        this.$wtitle = $("[name*='[wtitle]']").not('.cp-widget-title-noedit');
        this.$ul = this.$el.find('ul.posts');
        this.$s = this.$el.find('.existing-content-selector');
      },
      
      toggle: function(e){
        e.preventDefault();
        var $$ = $(e.target);
        $$.blur();
        this.setPosition($$);
        this.$s.toggle();
        if(this.$s.is(':visible') && this.$ul.find('li').length === 0){
          this.refreshList();
        }
      },
      
      setPosition: function($el){
        var top = $el.position().top + $el.height() + 10;
        this.$s.css({top: top+"px"});
      },
      
      select: function(e){
        e.preventDefault();
        var $$ = $(e.target);
          if(this.$el.hasClass('cp-widget-field-type-taxonomy')){
              if(this.$wtitle.length > 0){
                  this.$wtitle.val($$.data('name'));
              }
              this.$input.val($$.data('taxonomy') + ': ' + $$.data('slug'));
          }else{
              if(this.$wtitle.length > 0){
                  this.$wtitle.val($$.data('post_title'));
              }
              this.$input.val($$.data('post_type') + ': ' + $$.data('ID'));
          }
        this.$input.trigger('change');
        this.$input.trigger('linker.change');
        this.$s.toggle();
      },
      
      interval: function(){
        var _that = this;
        if(this.intervalTimeOut !== null){
          clearTimeout(this.intervalTimeOut);
        }
        
        this.interval = setTimeout(function(){
          _that.refreshList();
        }, 500);
      },
      
      refreshList: function(){
        var _that = this;
        var query = this.$search.val();
        this.$ul.empty().addClass('loading');
        this.cpAjax.call(this.action, function(data){
        	if(data.length > 0){
	          for(var i=0; i<data.length; i++){
                  if(_that.$el.hasClass('cp-widget-field-type-taxonomy')){
                      _that.renderTaxonomy(data[i]);
                  }else{
                      _that.renderPost(data[i]);
                  }
	          } 
	         }else{
	         		_that.$ul.append(
		         		$('<li>')
	                .addClass('post')
	                .html('No element found')
	           	);
	         }
	         _that.$ul.removeClass('loading');
        }, {query: query, args: this.args});
      },

      renderPost: function(obj){
          if(obj.post_title === ''){
              obj.post_title = '&nbsp;';
          }

          var title;
          if(obj.post_title.length > 30){
              title = obj.post_title.substring(0, 30) + '...';
          }else{
              title = obj.post_title;
          }

          this.$ul.append(
              $('<li>')
                  .addClass('post')
                  .html(title + ' <span>('+obj.post_type+')</span>')
                  .data(obj)
          );
      },

      renderTaxonomy: function(obj){
          if(obj.name === ''){
              obj.name = '&nbsp;';
          }

          var title;
          if(obj.name.length > 30){
              title = obj.name.substring(0, 30) + '...';
          }else{
              title = obj.name;
          }

          this.$ul.append(
              $('<li>')
                  .addClass('post')
                  .html(title + ' <span>('+obj.taxonomy+')</span>')
                  .data(obj)
          );
      }
  
  });
	
	/**
   * Linker button handler
   */
  $.CpField.View.Repeater = Backbone.View.extend({
    template: wp.template("cppress-field-repeater"),
    events: {
      'click .cp-widget-field-repeater-add': 'onAddClick',
      'click .cp-widget-field-remove': 'remove',
      'click .cp-widget-field-expand': 'expand',
      'click .cp-widget-field-copy': 'copy',
      'change .cp-widget-field-repeater-item': 'change'
    },
    
    cpAjax: null,
    sortObj: null,
    data: null,
    countItem: 0,
    $counter: null,
    $items: null,
    
    initialize: function(){
      this.cpAjax = $.fn.cpajax(this);
      this.data = this.$el.data();
      this.$items = this.$el.find('.cp-widget-field-repeater-items');
      this.$counter = this.$el.find('.cp-widget-field-repeater-counter');
      this.initSortable();
      if(this.data.values !== null){
        var countItem = parseInt(this.data.values.countitem);
        for(var i=0; i<countItem; i++){
          var vals = {};
          _.each(_.keys(this.data.values), function(key){
            if(key !== 'countitem'){
              vals[key] = this.data.values[key][i];
            }
          }, this);
          this.add(vals);
        }
      }
    },
    
    initSortable: function(){
      this.sortObj = this.$items.cpsortable();
      this.sortObj.sort({
        handle : '.cp-widget-field-repeater-item-top',
        items : '> .cp-widget-field-repeater-item',
      });
    },
    
    onAddClick: function(){
      this.add();
      return this;
    },
    
    add: function(values){
      var _that = this;
      values || (values={});
      var $item = $(this.template({title: this.data.itemTitle}));
      this.countItem++;
      this.$counter.val(this.countItem);
      this.$items.append($item);
      $item.addClass('loading');
      var args = {id: this.data.itemId, name: this.data.elementName, values: values, count: this.countItem};
      $(document).trigger('repeater.preadd', [args]);
      this.cpAjax.call(this.data.action.add, function(html){
        	$item.find('.cp-widget-field-repeater-item-form').append(html.data);
        	$.CpField.fn.enable($item, {exclude: ['repeater']});
        	$item.removeClass('loading');
        	_that.sortObj.refresh();
        	$item.trigger('repeater.add');
      }, args);
    },
    
    change: function(e){
      var _that = this;
      var $$ = $(e.target);
      var $itemForm = $(e.currentTarget).find('.cp-widget-field-repeater-item-form');
      if(this.data.action.hasOwnProperty('change')
        && this.data.action.change !== ''){
        if($itemForm.find('.cp-widget-field-repeater-item-change').length === 0){
          $itemForm.append($('<div>').addClass('cp-widget-field-repeater-item-change cp-widget-field'));
        }
        $itemForm.addClass('loading');
        $itemChange = $itemForm.find('.cp-widget-field-repeater-item-change');
        $itemChange.empty();
        this.cpAjax.call(this.data.action.change, function(html){
          $itemChange.append(html.data);
          $itemChange.removeClass('loading');
        }, {id: $$.val()});
      }
    },
    
    remove: function(e){
      var _that = this;
      var $$ = $(e.target);
      var $item = $$.parents('.cp-widget-field-repeater-item');
      this.countItem--;
      this.$counter.val(this.countItem);
      $item.slideUp('fast', function(){
        $(this).remove();
        _that.sortObj.refresh();
        $(window).resize();
      });
    },
    
    expand: function(e){
      var $$ = $(e.target);
      var $item = $$.parent().siblings('.cp-widget-field-repeater-item-form');
      $item.slideToggle();
    },
    
    copy: function(e){
      var _that = this;
      var $$ = $(e.target);
      var $item = $$.parents('.cp-widget-field-repeater-item');
      var $copyItem = $item.clone();
      this.$items.append($copyItem);
      $.CpField.fn.enable($copyItem, {exclude: ['repeater']});
      this.sortObj.refresh();
      $copyItem.hide().slideDown('fast', function(){
        $(window).resize();
      });
    }
  });
  
  /**
   * Accordion handle
   */
  $.CpField.View.Accordion = Backbone.View.extend({
    events: {
      'click .cp-widget-accordion-section-top': 'toggle'
    },
    
    initialize: function(){
      this.$el.find('.cp-widget-accordion-section-top').each(function(){
        var elData = $(this).data();
        if(!elData.active){
        	$(this).next().find('[name]').prop('disabled', true);
        }
        
      });
    },
    
    toggle: function(e){
      var _that = this;
      var $$ = $(e.currentTarget);
      var data = $$.data();
      if(data.active){
        this.closeAll();
      }else{
        this.closeAll();
        $$.data('active', 1);
        $$.find('.cp-widget-accordion-select input').attr('checked', true);
        $$.next().find('[name]').prop('disabled', false);
        $$.next().slideDown('fast', function(){
          _that.trigger('accordion:activate');
        });
      }
    },
    
    closeAll: function(){
      this.$el.find('.cp-widget-accordion-section-top').each(function(){
        $(this).data('active', 0);
        $(this).find('.cp-widget-accordion-select input').attr('checked', false);
      });
      this.$el.find('.cp-widget-accordion-section [name]').prop('disabled', true);
      this.$el.find('.cp-widget-accordion-section').slideUp(300);
    },
  });
	
}(jQuery, _));