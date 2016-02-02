jQuery(document).ready(function(){

	var $ = jQuery;
	if($('#cp-press-attachment-inside').length > 0){
		var cpAttachment = new $.CpAttachment.View.Library({
    	el: $('#cp-press-attachment-inside'),
  	});
  	cpAttachment.load();
	}
});

(function($, _){
	$.CpAttachment = {
		View: {},
		Collection: {},
		Model: {}
	};
	
	$.CpAttachment.Model.File = Backbone.Model.extend({
		defaults:{
			name: null,
			size: null,
			link: null,
			mime: null,
			id: null,
			type: null,
			title: null,
			icon: null,
			featured: false
		},
		
		setData: function(attachment){
			this.set('name', attachment.filename);
			this.set('size', attachment.filesizeHumanReadable);
			this.set('link', attachment.link);
			this.set('mime', attachment.mime);
			this.set('id', attachment.id);
			this.set('type', attachment.subtype);
			this.set('title', attachment.title);
			this.set('icon', 'media-document');
		},
	});
	
	$.CpAttachment.Collection.Library = Backbone.Collection.extend({
		model: $.CpAttachment.Model.File,
	});
	
	$.CpAttachment.View.File = Backbone.View.extend({
		template: wp.template( "cppress-attachment-file" ),
		events: {
			'click .cp-widget-icon-delete': 'destroy',
			'click .cp-widget-icon-featured': 'setFeatured'
		},
		
		destroy: function(e){
			e.preventDefault();
			var _that = this;
			this.$el.fadeOut('normal', function(){
				_that.library.library.remove(_that.model);
				_that.library.store();
				_that.remove();
			});
		},
		
		setFeatured: function(e){
			e.preventDefault();
			var oldFeatured = this.library.library.where({'featured': true}).pop();
			if(typeof oldFeatured !== 'undefined'){
				oldFeatured.set('featured', false);
			}
			this.model.set('featured', true);
			$('ul.cp-press-attachment-files i.featured').removeClass('featured');
			this.$el.find('i.cp-widget-icon-featured').addClass('featured');
			this.library.store();
		},
		
		render: function(){
			var templateArgs = {
				icon: 'dashicons-' + this.model.get('icon'),
				info: this.model.get('title'),
			};
			this.setElement(this.template(templateArgs));
			if(this.model.get('featured')){
				this.$el.find('i.cp-widget-icon-featured').addClass('featured');
			}
			this.$el.data('view', this);
			return this;
		}
	});
	
	$.CpAttachment.View.Library = Backbone.View.extend({
		$input: null,
		files: null,
		library: null,
		validMime: null,
		
		events: {
			'click #cp-press-set-featured-attachment': 'attach'
		},
		
		initialize: function(){
			this.$input = this.$el.find('#cp-press-attachment-input');
			this.files = JSON.parse(this.$input.val());
			this.validMime = this.$el.data('valid');
			this.library = new $.CpAttachment.Collection.Library();
			this.listenTo(this.library, "add", this.onFileAdd);
		},
		
		load: function(){
			_.each(this.files, function(file, fkey){
				var fileObj = new $.CpAttachment.Model.File();
				for(var attr in file){
					if(file.hasOwnProperty(attr)){
						fileObj.set(attr, file[attr]);
					}
				}
				this.library.add(file);
			}, this);
		},
		
		attach: function(e){
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
        title: $$.data('choose'),
        button: {
          text: $$.data('update'),
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
		
			if(_.indexOf(this.validMime, attachment.mime) < 0){
				alert('Invalid file type');
				return;
			}else{
				var file = new $.CpAttachment.Model.File();
				file.setData(attachment);
				if(this.library.length < 1){
					file.set('featured', true);
				}
				this.library.add(file);
				this.wpMediaFrame.close();
				return;
			}
		},
		
		onFileAdd: function(file, library, options){
			var fileView = new $.CpAttachment.View.File({model: file});
			fileView.library = this;
			fileView.render();
			fileView.$el.appendTo(this.$el.find('.cp-press-attachment-files')).hide().fadeIn();
			this.store();
		},
		
		store: function(){
			var files = [];
			this.library.each(function(file, fkey){
				files.push({
					name: file.get('name'),
					size: file.get('size'),
					link: file.get('link'),
					mime: file.get('mime'),
					id: file.get('id'),
					type: file.get('type'),
					title: file.get('title'),
					icon: file.get('icon'),
					featured: file.get('featured')
				});
			});
			
			filesJson = JSON.stringify(files);
			
			if(this.$input.val() !== filesJson){
				this.files = filesJson;
				this.$input.val(filesJson);
				this.$input.trigger('change');
				this.trigger('attachments-files-stored');
			}
		}
		
	});
	
	
})(jQuery, _);