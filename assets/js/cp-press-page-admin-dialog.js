(function($, _){
	$.CpDialog.grid = {
		View: {},
		Sidebar: {},
		Widget: {}
	};

	$.CpDialog.addGrid = {
		View: {}
	};

	$.CpDialog.editGrid = {
		View: {}
	};

	$.CpDialog.editWidget = {
		View: {}
	};

	$.CpDialog.widgetHandler = {
		View: {}
	};

	$.CpDialog.grid.Widget = Backbone.View.extend({
		widgetLoaded: false,
		cpAjax: null,
		el: '#widget_form',

		initialize: function(){
			this.cpAjax = $.fn.cpajax(this);
		},

		render: function(args){
			args || (args={});
			args = _.extend({action: 'page_widget_form'}, args);
			$(document).trigger('widget.premodalopen', [args]);
			var _that = this;
			this.cpAjax.call(args.action, function(response){
				_that.$el.html(response.data);
				_that.widgetLoaded = true;
				_that.setup();
				_that.trigger('widget-loaded');
			}, {args: JSON.stringify(args)});
		},

		setup: function(){

		},

		attach: function(wrapper){
			wrapper.append(this.$el);
		},

		detach: function(){
			this.$el.detach();
		}
	});

	$.CpDialog.grid.Sidebar = Backbone.View.extend({

		sidebarLoaded: false,
		cpAjax: null,

		initialize: function(){
			this.cpAjax = $.fn.cpajax(this);
		},

		render: function(args){
			args || (args = {});
			args = _.extend({action: 'page_sidebar_grid'}, args);
			var _that = this;
			this.cpAjax.call(args.action, function(response){
				_that.$el.html(response.data);
				_that.sidebarLoaded = true;
				_that.setup();
				_that.trigger('sidebar-loaded');
			}, {args: JSON.stringify(args) });
		},

		setup: function(){
			$('.sidebar-section-wrapper').each(function(){
				var $$ = $(this);
				$$.find('.sidebar-section-head').click(function(e){
					e.preventDefault();
					$$.find('.sidebar-section-fields').slideToggle("fast");
				});
			});
		},

		attach: function(wrapper){
			wrapper.append(this.$el);
		},

		detach: function(){
			this.$el.detach();
		}

	});

	$.CpDialog.grid.WidgetsList = Backbone.View.extend({
        el: '<ul>',

        render: function(){
            this.$el.append(wp.template('cppress-dialog-widgets-list'));

            return this.$el;
        }
    });

	$.CpDialog.grid.View = $.CpDialog.dialog.View.extend({
		id: "cppress_page_dialog",

		/**
		 * Container to store our compiled templates. Not strictly necessary in such a simple example
		 * but might be useful in a larger one.
		 */

		menu: false,
		view: null,

		content: null,
		button: null,
		title: null,

		/**
		 * Instantiates the Template object and triggers load.
		 */
		initialize: function () {
			"use strict";
			_.bindAll( this, 'render', 'close', 'save', 'doNothing', 'changeRow' );
			this.content = wp.template(this.content);
			this.button = wp.template(this.button);
			this.initialize_templates();
			this.on('content-loaded', function(){
				// Handle any attempt to move focus out of the modal.
				this.$el.find('.cp-row-field').on('change', this.changeRow);
				this.$el.find('.cp-grid-select-field').on('change', this.changeRowSize);
				$( document ).on( "focusin", this.preserveFocus );
				// set overflow to "hidden" on the body so that it ignores any scroll events while the modal is active
				// and append the modal to the body.
				// TODO: this might better be represented as a class "modal-open" rather than a direct style declaration.
				$( "body" ).css( {"overflow": "hidden"} ).append( this.$el );
				// Set focus on the modal to prevent accidental actions in the underlying page
				// Not strictly necessary, but nice to do.
				this.$el.focus();
			});
		},

		changeRow: function(e){
			var sidebarFormData = this.getFormValues('.cppress_dialog-sidebar');
			var $$ = $(e.target);
			var cell = parseInt($$.val());
			var cellTmpl = wp.template('cppress-dialog-cell');
			var cellSidebar = wp.template('cppress-sidebar-cell');
			var weight = Math.floor(12/cell);
			var content='';
			var sidebar='';
			for(var i=0; i<cell; i++){
				var index = i+1;
				var cellIndex = "cell"+index;
				var cellData = {};
				if(sidebarFormData.hasOwnProperty(cellIndex)){
					cellData = sidebarFormData[cellIndex];
				}else{
					cellData.style = '';
					cellData.classes = '';
				}
				content += cellTmpl({weight: weight});
				sidebar += cellSidebar({
					cell: i+1,
					style: cellData.style,
					classes: cellData.classes
				});
			}
		
			this.$el.find('.cp-row-preview').html(content);
			this.$el.find('.sidebar-cell-container').html(sidebar);
			this.$el.find('.cp-grid-select-field').on('change', this.changeRowSize);
				this.$el.find('.cp-grid').each(function(i, el){
				$(el).data('weight', weight);
			});
		},
		
		changeRowSize: function(e){
			var $$ = $(e.target);
			var removed;
			$grid = $$.parents('.cp-grid');
			$grid.removeClass(function(index, css){
				removed = (css.match (/col-md-[0-9]*/g) || []).join(' ');
				return removed;
			});
			
			$grid.addClass(removed.replace(/(col-md-)[0-9]*/g, "$1"+$$.val()));
			$grid.data('weight', $$.val());
		},
		
		getWeight: function(){
			var w = [];
			this.$el.find('.cp-grid').each(function(i, el){
				w[i] = $(el).data('weight');
			});
			
			return w;
		},

		renderSidebar: function(args){
			this.sidebar = new $.CpDialog.grid.Sidebar();
			this.$el.find('div.navigation-bar').css({"display": "block"});
			this.$el.find('article').css({"right": 320});
			this.sidebar.render(args);
			this.sidebar.attach(this.$el.find('div.navigation-bar nav'));
			this.sidebar.on('sidebar-loaded', function(){
				this.$el.find('div.navigation-bar nav').removeClass("cp-loading cp-panel-loading");
			}, this);
			this.$el.find('div.navigation-bar nav').addClass("cp-loading cp-panel-loading");
			this.trigger('sidebar-loaded');
		},

		setView: function(view){
			this.view = view;
		},

		setModel: function(model){
			this.model = model;
		},

		formatStyle: function(styleString){
			var style = {};
			var regex = /([\w-]*)\s*:\s*([^;]*)/g;
			var match;
			while(match=regex.exec(styleString)){
				style[match[1]] = match[2];
			}

			return style;
		},

		formatClasses: function(classString){
			var classes = [];
			var a = classString.split(" ");
			for(var i=0; i<a.length; i++){
				if(a[i] !== ''){
					classes[i] = a[i];
				}
			}

			return classes;
		}

	} );

	$.CpDialog.addGrid.View = $.CpDialog.grid.View.extend({
		content: 'cppress-dialog-add-grid',
		button: 'cppress-dialog-add',
		title: 'Add row',
		render: function(){
			"use strict";
			this.renderWindow();
			//LOAD SIDEBAR
			this.renderSidebar({
				cell: 2,
				style: {},
				classes: [],
				cellInfo:{
					style: [{}, {}],
					classes: [[], []]
				}
			});
		},

		save: function(e){
			var sidebarFormData = this.getFormValues('.cppress_dialog-sidebar');
			var cell = this.$el.find('.cp-grid').length;
			var weight = this.getWeight();
			var map = [];
			map[0] = [];
			for(var i=0; i<cell; i++){
				map[0][i] = weight[i];
			}
			this.model.setGrids(1, map);
			var grid = this.model.grids.last();
			for(var j=0; j<cell; j++){
				var index = j+1;
				var cellIndex = "cell"+index;
				var cellData = sidebarFormData[cellIndex];
				var c = grid.cells.at(j);
				c.set('style', this.formatStyle(cellData.style));
				c.set('classes', this.formatClasses(cellData.classes));
			}
			grid.set('style', this.formatStyle(sidebarFormData.grid.style));
			grid.set('classes', this.formatClasses(sidebarFormData.grid.classes));
			this.trigger('grid:add');
			this.close(e);
		}

	});

	$.CpDialog.editGrid.View = $.CpDialog.grid.View.extend({
		content: 'cppress-dialog-edit-grid',
		button: 'cppress-dialog-save-cancel',
		title: 'Edit row',
		render: function(){
			var cellCount = this.model.cells.length;
			var cellWeight = [];
			this.model.cells.forEach(function(el, i){
				cellWeight[i] = el.get('weight');
			});
			this.renderWindow({count: cellCount, weight: cellWeight});
			this.renderSidebar({
				cell: cellCount,
				style: this.model.get("style"),
				classes: this.model.get("classes"),
				cellInfo:{
					style: this.model.cells.pluck("style"),
					classes: this.model.cells.pluck("classes")
				}
			});
		},

		save: function(e){
			var sidebarFormData = this.getFormValues('.cppress_dialog-sidebar');
			var cell = this.$el.find('.cp-grid').length;
			var weight = this.getWeight();
			var cells = [];
			for(var i=0; i<cell; i++){
				cells[i] = weight[i];
			}
			var widgets = [];
			this.model.cells.forEach(function(cell, i){
				widgets[i] = cell.widgets;
			});
			this.model.cells.reset();
			this.model.setCells(cells, this.model.section, {widgets: widgets});
			for(var j=0; j<cell; j++){
				var index = j+1;
				var cellIndex = "cell"+index;
				var cellData = sidebarFormData[cellIndex];
				var c = this.model.cells.at(j);
				c.set('style', this.formatStyle(cellData.style));
				c.set('classes', this.formatClasses(cellData.classes));
			}
			this.model.set('style', this.formatStyle(sidebarFormData.grid.style));
			this.model.set('classes', this.formatClasses(sidebarFormData.grid.classes));
			this.trigger('grid:edit');
			this.close(e);
		}
	});

	$.CpDialog.editWidget.View = $.CpDialog.grid.View.extend({
		content: 'cppress-dialog-edit-widget',
		button: 'cppress-dialog-save-cancel',
		title: 'Edit Widget',
		icons: null,
		render: function(){
		  var _that = this;
			this.renderWindow();
			this.$el.find('div.navigation-bar').remove();
			this.$el.find('article').css({"right": 30});
			this.widget = new $.CpDialog.grid.Widget();
			this.widget.render({
				widget: this.model.get('data'),
				widget_class: this.model.get('class'),
				widget_id: this.model.get('id_base'),
			});
			this.widget.attach(this.$el.find('#widget_form'));
			this.widget.on('widget-loaded', function(){
				this.$el.find('article').removeClass("cp-loading cp-panel-loading");
				$.CpField.fn.enable(this.$el.find('#widget_form'));
				this.$el.find('#widget_form').trigger('widget.loaded');
			}, this);
			this.$el.find('article').addClass("cp-loading cp-panel-loading");
			this.trigger('widget-loaded');
		},

		save: function(e){
	      var widgetFormData = this.getFormValues('#widget_form');
	      widgetFormData = widgetFormData[Object.keys(widgetFormData)[0]];
	      if(typeof widgetFormData === 'undefined'){
	        widgetFormData = {};
	      }else{
	        widgetFormData = widgetFormData[Object.keys(widgetFormData)[0]];
	      }
	      this.model.setData(widgetFormData);
	      this.model.set('raw', true);
	      this.trigger('widget:edit');
	      
	      this.close(e);
		}

	});

	$.CpDialog.widgetHandler.View = $.CpDialog.grid.View.extend({
        content: 'cppress-dialog-widget-handler',
        button: 'cppress-dialog-save-cancel',
        title: 'Widgets Handler',
        icons: null,
        widgetsList: new $.CpDialog.grid.WidgetsList(),

        render: function(){
            var widgetTemplate = wp.template("cppress-page-widget");
            var _that = this;
            var cellCount = this.model.cells.length;
            var cellWeight = [];
            var widgetsHandler = [];
            var cellWidgetsBoxes = [];
            this.model.cells.forEach(function(el, i){
                cellWeight[i] = el.get('weight');
                widgetsHandler[i] = [];
                cellWidgetsBoxes[i] = '';
                el.widgets.forEach(function(widget, j){
                    cellWidgetsBoxes[i] += widgetTemplate({
                        title: widget.get('title'),
                        description: widget.get('description')
                    });
                    widgetsHandler[i][j] = {
                        widgetTitle: widget.get('title'),
                        widgetDescription: widget.get('description'),
                        widgetIcon: widget.get('icon'),
                        widgetClassname: widget.get('class'),
                        id: widget.get('id_base')
                    }
                });
            });
            this.renderWindow({count: cellCount, weight: cellWeight});
            this.renderWidgets(cellWidgetsBoxes);
            this.$el.find('div.navigation-bar').remove();
            this.$el.find('article').css({"right": 30});
            this.$el.find('.cp-widgets-handler-lists').append(this.widgetsList.render());
            this.$el.find('.cp-widgets-list-expand').click(function(){
                var id = $(this).attr('id');
                _that.$el.find('#cp-widgets-'+id).toggle();
            });
            this.$el.find('.cp-widgets-handler').data('widgets', widgetsHandler);
            this.$el.find('.cp-widgets-list-button').click(function(e){
               var widgetData = $(this).data();
               widgetData.id = $(this).attr('id');
               var cell = $(this).parents('div.cp-widgets-handler-lists').data('cell');
               var widgetsHandler = _that.$el.find('.cp-widgets-handler').data('widgets');
               if(typeof widgetsHandler[cell] !== 'undefined'){
                   widgetsHandler[cell].push(widgetData);
               }else{
                   throw new Error('No cell found in widgetsHandler');
               }
               $(this).parents('div.cp-widgets-handler-lists').toggle();
               _that.$el.find('.cp-row-list[data-cell="'+cell+'"] .cp-row-droppable').append(widgetTemplate({
                   title: widgetData.widgetTitle,
                   description: widgetData.widgetDescription,
                   showWidgetOp: false
               }));

                _that.$el.find('.cp-widgets-handler').data('widgets', widgetsHandler);
            });
        },

        renderWidgets: function(widgets){
            var _that = this;
            this.model.cells.forEach(function(cell, i){
                _that.$el.find('.cp-row-list[data-cell="'+i+'"] .cp-row-droppable').append(widgets[i]);
            })
        },

        save: function(e){
            var widgetsHandler = this.$el.find('.cp-widgets-handler').data('widgets');
            for(var i=0; i<widgetsHandler.length; i++){
                var Cell = this.model.cells.at(i);
                _.each(Cell.widgets.toArray(), function(widget, index){
                	widgetsHandler[i][index].widgetData = widget.get('data');
                	widget.destroy();
                });

                for(var j=0; j<widgetsHandler[i].length; j++){
                    var widget = new $.CpPage.Model.Widget();
                    widget.cell = Cell;
                    var values = {};
                    if(widgetsHandler[i][j].hasOwnProperty('widgetData')){
                    	values = widgetsHandler[i][j].widgetData;
					}
                    widget.setData(values);
                    widget.set('title', widgetsHandler[i][j].widgetTitle);
                    widget.set('icon', widgetsHandler[i][j].widgetIcon);
                    widget.set('description', widgetsHandler[i][j].widgetDescription);
                    widget.set('class', widgetsHandler[i][j].widgetClassname);
                    widget.set('raw', false);
                    widget.set('id_base', widgetsHandler[i][j].id);
                    Cell.widgets.add(widget);
                }
            }

            this.close(e);
        }
	});

}(jQuery, _));