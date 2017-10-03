/**
 * Backbone Application File
 * @internal Obviously, I've dumped all the code into one file. This should probably be broken out into multiple
 * files and then concatenated and minified but as it's an example, it's all one lumpy file.
 * @package $.CpDialog.dialog
 */

/**
 * @type {Object} JavaScript namespace for our application.
 */

(function($){

	$.CpDialog = {
		dialog: {}
	};

	/**
	 * Primary Modal Application Class
	 */
	$.CpDialog.dialog.View = Backbone.View.extend({
		id: "cppress_dialog",
		events: {
			"click .cppress_dialog-close": "close",
			"click #btn-cancel": "close",
			"click #btn-ok": "save",
			"click .navigation-bar a": "doNothing"
		},

		/**
		 * Simple object to store any UI elements we need to use over the life of the application.
		 */
		ui: {
			nav: undefined,
			content: undefined
		},

		/**
		 * Container to store our compiled templates. Not strictly necessary in such a simple example
		 * but might be useful in a larger one.
		 */
		coreTemplates: {},
		content: null,
		button: null,

		sidebar: null,

		menu: false,
		title: 'CpPress dialog',

		/**
		 * Instantiates the Template object and triggers load.
		 */
		initialize: function () {
			"use strict";
			_.bindAll( this, 'render', 'close', 'save', 'doNothing' );
			this.initialize_templates();
		},


		/**
		 * Creates compiled implementations of the templates. These compiled versions are created using
		 * the wp.template class supplied by WordPress in 'wp-util'. Each template name maps to the ID of a
		 * script tag ( without the 'tmpl-' namespace ) created in template-data.php.
		 */
		initialize_templates: function () {
			this.coreTemplates.window = wp.template( "cppress-dialog-window" );
			this.coreTemplates.backdrop = wp.template( "cppress-dialog-backdrop" );
			this.coreTemplates.menuItem = wp.template( "cppress-dialog-menu-item" );
			this.coreTemplates.menuItemSeperator = wp.template( "cppress-dialog-menu-item-separator" );
		},

		/**
		 * Assembles the UI from loaded templates.
		 * @internal Obviously, if the templates fail to load, our modal never launches.
		 */
		render: function () {
			"use strict";

			// Build the base window and backdrop, attaching them to the $el.
			// Setting the tab index allows us to capture focus and redirect it in Application.preserveFocus
		},

		renderWindow: function(args){
			args || (args = {});
			// Build the base window and backdrop, attaching them to the $el.
			// Setting the tab index allows us to capture focus and redirect it in Application.preserveFocus
			this.$el.attr( 'tabindex', '0' );
			this.$el.append( this.coreTemplates.backdrop());
			this.$el.append( this.coreTemplates.window({
				title: this.title,
				button: this.button()
			})).hide().fadeIn();

			this.ui.content = this.$( '.cppress_dialog-main article div.cp-panel-dialog' )
				.append( this.content(args) );

			this.trigger('content-loaded');
		},

		getFormValues: function(formSelector){
			if(typeof formSelector === "undefined"){
				formSelector = '.cp-form';
			}

			var $form = this.$(formSelector);
			var data = {}, parts;
			$form.find('[name]').each(function(){
				var $$ = $(this);
				var name = /([A-Za-z0-9_]+)\[(.*)\]/.exec( $$.attr('name') );
					if(name === undefined){
        			return true;
        	}
	        if(name === null){
	          return true;
	        }
	        
	        if($$.is(':disabled')){
	        	return true;
	        }
	        
	        // Create an array with the parts of the name
	        if(typeof name[2] === 'undefined'){
	          parts = $$.attr('name');
	        }else{
	            parts = name[2].split('][');
	            parts.unshift( name[1] );
	        }
	
	        parts = parts.map(function(e){
		        if( !isNaN(parseFloat(e)) && isFinite(e) ) {
		          return parseInt(e);
		        }else{
		          return e;
		        }
	        });
	        var sub = data;
	        var fieldValue = null;
	
	        var fieldType = ( typeof $$.attr('type') === 'string' ? $$.attr('type').toLowerCase() : false );
	        // First we need to get the value from the field
	        if( fieldType === 'checkbox' ){
	          if ( $$.is(':checked') ) {
	            fieldValue = $$.val() !== '' ? $$.val() : true;
	          } else {
	            fieldValue = null;
	          }
	        } else if( fieldType === 'radio' ){
	          if ( $$.is(':checked') ) {
	            fieldValue = $$.val();
	          } else {
	            //skip over unchecked radios
	            return;
	          }
	        } else if( $$.prop('tagName') === 'TEXTAREA' && $$.hasClass('wp-editor-area') ){
	          // This is a TinyMCE editor, so we'll use the tinyMCE object to get the content
	          var editor = null;
	          if ( typeof tinyMCE !== 'undefined' ) {
	            editor = tinyMCE.get( $$.attr('id') );
	          }
	          if( editor !== null && typeof( editor.getContent ) === "function" && !editor.isHidden() ) {
	            fieldValue = editor.getContent();
	          } else {
	            fieldValue = $$.val();
	          }
	        } else if ( $$.prop('tagName') === 'SELECT' ) {
	          var selected = $$.find('option:selected');
						if( selected.length === 1 ) {
	            fieldValue = $$.find('option:selected').val();
	          }else if( selected.length > 1 ) {
	            // This is a mutli-select field
	            fieldValue = _.map( $$.find('option:selected'), function(n ,i){
	            	return $(n).val();
	          	});
	        	}
	        }else{
	            // This is a fallback that will work for most fields
	            fieldValue = $$.val();
	        }
	
					// Now, we need to filter this value if necessary
	        if( typeof $$.data('filter') !== 'undefined' ) {
	          switch( $$.data('filter') ) {
	            case 'json_parse':
	            // Attempt to parse the JSON value of this field
	            try {
	              fieldValue = JSON.parse( fieldValue );
	            }catch(err) {
	              fieldValue = '';
	            }
	            break;
	          }
	        }
	
	        // Now convert this into an array
	        if(fieldValue !== null) {
	        	for (var i = 0; i < parts.length; i++) {
	          	if (i === parts.length - 1) {
	              if( parts[i] === '' ) {
	                // This needs to be an array
	                sub.push(fieldValue);
	              }else {
	                sub[parts[i]] = fieldValue;
	              }
	            }else {
	              if (typeof sub[parts[i]] === 'undefined') {
	                if ( parts[i+1] === '' ) {
	                  sub[parts[i]] = [];
	                }else {
	                  sub[parts[i]] = {};
	                }
	            	}
	            	sub = sub[parts[i]];
	            }
	          }
	        }
	        
	        
				});

			return data;
		},

		/**
		 * Closes the modal and cleans up after the instance.
		 * @param e {object} A jQuery-normalized event object.
		 */
		close: function ( e ) {
			"use strict";

			e.preventDefault();
			this.undelegateEvents();
			$( document ).off( "focusin" );
			$( "body" ).css( {"overflow": "auto"} );
			this.remove();
			this.trigger('dialog-close');
		},

		/**
		 * Responds to the btn-ok.click event
		 * @param e {object} A jQuery-normalized event object.
		 * @todo You should make this your own.
		 */
		save: function ( e ) {
			"use strict";
			this.closeModal( e );
		},

		/**
		 * Ensures that events do nothing.
		 * @param e {object} A jQuery-normalized event object.
		 * @todo You should probably delete this and add your own handlers.
		 */
		doNothing: function ( e ) {
			"use strict";
			e.preventDefault();
		}

	} );
}(jQuery));
