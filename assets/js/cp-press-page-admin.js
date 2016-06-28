jQuery(document).ready(function(){

	var $ = jQuery;
	if($('#cp_press_select_content_type').length > 0){
		var cpPageModel = new $.CpPage.Model.Page();
		var cpPageBuilder = new $.CpPage.View.PageBuilder({
    	el: $('#cp_press_select_content_type'),
    	model: cpPageModel
  	});
  	cpPageBuilder.render().load();
	}
});

(function($, _){

	$.CpPage = {
		Model: {},
		Collection: {},
		View: {},
		fn: {}
	};

  $.CpPage.Model.Page = Backbone.Model.extend({
    sections: {},

		defaults: {
			data: {
				widgets: [],
				sections: [],
				grids: [],
				cells: []
			}
		},

    initialize: function(){
      this.sections = new $.CpPage.Collection.Sections();
    },

	addSection: function(gridCount, weights, options){
		  options = _.extend({
		    title: null,
		    slug: null
		  }, options);
		  
		var section = new $.CpPage.Model.Section({
			collection: this.sections
		});

      if(options.title !== null){
        section.set('title', options.title);
      }
      
      if(options.slug !== null){
        section.set('slug', options.slug);
      }

		section.setGrids(gridCount, weights);
		this.sections.add(section);

		return section;
	},

	loadLayoutData: function(data){
		this.emptySections();
		this.set("data", data, {silent: true});
		if(typeof data.sections == "undefined"){
			this.trigger('layout-loaded');
			return;
		}
		var sections = [];
		for(var skey=0; skey<data.sections.length; skey++){
			sections[skey] = {
				cells: []
			};
			sections[skey].gridCount = data.sections[skey].grids;
			for(var gkey=0; gkey<data.grids.length; gkey++){
				for(var ckey=0; ckey<data.cells.length; ckey++){
					if(data.cells[ckey].section == skey &&
						 	data.cells[ckey].grid == gkey){
						if(typeof sections[skey].cells[gkey] === "undefined"){
							sections[skey].cells[gkey] = [];
						}
						sections[skey].cells[gkey].push(data.cells[ckey].weight);
					}
				}
			}
		}
		var _that = this;
		_.each(sections, function(section, skey){
			var ns = _that.addSection(
				section.gridCount,
				section.cells,
				{
					title: data.sections[skey].title,
					slug: data.sections[skey].slug
				}
			);
			var sg = _.where(data.grids, {section: skey});
			_.each(sg, function(g, gkey){
				var grid = ns.grids.at(g.gkey);
				grid.set('style', g.style);
				grid.set('classes', g.classes);
				var gc = _.where(data.cells, {section: skey, grid: gkey});
				_.each(gc, function(c, ckey){
					var cell = grid.cells.at(c.ckey);
					cell.set('style', c.style);
					cell.set('classes', c.classes);
				});
			});
		});

		if(typeof data.widgets === "undefined") { return; }

		_.each(data.widgets, function(widgetData){
			try{
				var widget_info = widgetData.widget_info;
				delete widgetData.widget_info;
				var section = _that.sections.at(parseInt(widget_info.section));
				var grid = section.grids.at(parseInt(widget_info.grid));
				var cell = grid.cells.at(parseInt(widget_info.cell));
				var widget = new $.CpPage.Model.Widget({
					class: widget_info.class,
					id_base: widget_info.id_base,
					data: widgetData,
					title: widget_info.title,
					description: widget_info.description,
					icon: widget_info.icon
				});
				if(typeof widget_info.style !== "undefined"){
					widget.set('style', widget_info.style);
				}
				widget.cell = cell;
				cell.widgets.add(widget);
			}catch(err){
			}
		});

	},

	getLayoutData: function(){
		var layout = {
			widgets: [],
			sections: [],
			grids: [],
			cells: []
		};

		var widgetId = 0;

		this.sections.each(function(section, skey){
			section.grids.each(function(grid, gkey){
					grid.cells.each(function(cell, ckey){
						cell.widgets.each(function(widget, wkey){
							var values = _.extend(_.clone(widget.get('data')),{
								widget_info: {
									class: widget.get('class'),
									id_base: widget.get('id_base'),
									title: widget.get('title'),
									description: widget.get('description'),
									icon: widget.get('icon'),
									section: skey,
									grid: gkey,
									cell: ckey,
									id: widgetId++,
									style: widget.get('style'),
									raw: widget.get('raw')
								}
							});
							layout.widgets.push(values);
						});

						layout.cells.push({
							grid: gkey,
							ckey: ckey,
							section: skey,
							weight: cell.get('weight'),
							style: cell.get('style'),
							classes: cell.get('classes')
						});

					});

					layout.grids.push({
						section: skey,
						gkey: gkey,
						cells: grid.cells.length,
						style: grid.get('style'),
						classes: grid.get('classes')
					});

			});

			layout.sections.push({
				grids: section.grids.length,
				title: section.get('title'),
				slug: section.get('slug'),
			});
		});
		return layout;

	},

	refreshLayoutData: function(){
		var oldLayout = JSON.stringify(this.get('data'));
		var newLayout = this.getLayoutData();
		this.set('data', newLayout, {silent: true});
		if(JSON.stringify(newLayout) !== oldLayout){
			this.trigger('change');
			this.trigger('change:data');
		}
	},

	emptySections: function(){
		_.invoke(this.sections.toArray(), "destroy");
		this.sections.reset();

		return this;
	}
  });

  $.CpPage.Model.Section = Backbone.Model.extend({
    grids: {},

    defaults: {
      title: '',
      slug: ''
    },

    initialize: function(){
      this.grids = new $.CpPage.Collection.Grids();
			this.on('destroy', this.onDestroy, this);
    },

		onDestroy: function(){
			_.invoke(this.grids.toArray(), "destroy");
			this.grids.reset();
		},

    setGrids: function(countGrids, cells){
      for(var i=0; i<countGrids; i++){
        var grid = new $.CpPage.Model.Grid();
        grid.setCells(cells[i], this);
        grid.section = this;
        this.grids.add(grid);
      }
    },

	clone: function(cloneOptions){
			cloneOptions = _.extend({cloneGrids: true}, cloneOptions);
			var clone = new this.constructor(this.attributes);
			if(cloneOptions.cloneGrids){
				this.grids.each(function(grid){
					clone.grids.add(grid.clone(clone, cloneOptions), {silent: true});
				});
			}

			return clone;
	}
  });

  $.CpPage.Collection.Sections = Backbone.Collection.extend({
    model: $.CpPage.Model.Section
  });

  $.CpPage.Model.Grid = Backbone.Model.extend({
    cells: {},

    section: null,

    defaults: {
      style: {},
      classes: []
    },

    initialize: function(){
      this.cells = new $.CpPage.Collection.Cells();
			this.on('destroy', this.onDestroy, this);
    },

		onDestroy: function(){
			_.invoke(this.cells.toArray(), "destroy");
			this.cells.reset();
		},

    setCells: function(cells, section, options){
    	options || (options={});
			options = _.extend({widgets: []}, options);
      var _that = this;
      _.each(cells, function(cellWeight, i){
        var cell = new $.CpPage.Model.Cell({weight: cellWeight});
				cell.section = section;
        cell.grid = _that;
        
        if(options.widgets.length > 0){
        	if(typeof options.widgets[i] !== "undefined"){
	        	_.each(options.widgets[i].models, function(widget, k){
	        		cell.widgets.add(widget);
	        	});
	        }
        }
        _that.cells.add(cell);
      });
    },

		clone: function(section, cloneOptions){
			if(typeof section === "undefined"){
				section = this.section;
			}
			cloneOptions = _.extend({cloneCells: true}, cloneOptions);
			var clone = new this.constructor(this.attributes);
			clone.set('collection', section.grids, {silent: true});
			clone.section = section;
			if(cloneOptions.cloneCells){
				this.cells.each(function(cell){
					clone.cells.add(cell.clone(clone, cloneOptions), {silent: true});
				});
			}

			return clone;
		}
  });

  $.CpPage.Collection.Grids = Backbone.Collection.extend({
    model: $.CpPage.Model.Grid
  });

  $.CpPage.Model.Cell = Backbone.Model.extend({
    defaults: {
      weight: 12,
      style: {},
      classes: [],
    },
		widgets: {},
    grid: null,

		initialize: function(){
			this.widgets = new $.CpPage.Collection.Widgets();
			this.on("destroy", this.onDestory, this);
		},

		onDestroy: function(){
			_.invoke(this.widgets.toArray(), "destroy");
			this.widgets.reset();
		},

		clone: function(grid, cloneOptions){
			if(typeof grid === "undefined"){
				grid = this.grid;
			}
			cloneOptions = _.extend({cloneWidgets: true}, cloneOptions);
			var clone = new this.constructor(this.attributes);
			clone.set('collection', grid.cells, {silent: true});
			clone.grid = grid;
			if(cloneOptions.cloneWidgets){
				this.widgets.each(function(widget){
					clone.widgets.add(widget.clone(clone), {silent: true});
				});
			}

			return clone;
		}
  });

  $.CpPage.Collection.Cells = Backbone.Collection.extend({
    model: $.CpPage.Model.Cell
  });

	$.CpPage.Model.Widget = Backbone.Model.extend({
		default: {
			class: null,
			title: null,
			description: null,
			icon: null,
			style: {},
			missing: true,
			data: {},
			raw: false
		},
		cell: null,

		initialize: function(){

		},

		setData: function(data){
				var hasChanged = false;
				if(JSON.stringify(data) !== JSON.stringify(this.get('data'))){
					hasChanged = true;
				}

				this.set('data', data, {silent: true});
				if(hasChanged){
					this.trigger('change');
					this.trigger('change:data');
				}
		},

		getData: function(value){
			var data = this.get('data');
			if(data.hasOwnProperty(value)){
					return data[value];
			}else{
				return null;
			}
		},

		moveToCell: function(newCell, options){
			options = _.extend({
				silent: true
			}, options);

			if(this.cell.cid == newCell.cid){
				return false;
			}

			this.cell = newCell;
			this.collection.remove(this, options);
			newCell.widgets.add(this, options);

			return true;
		},

		clone: function(cell){
			if(typeof cell === "undefined"){
				cell = this.cell;
			}

			var clone = new this.constructor(this.attributes);
			var cloneData = JSON.parse(JSON.stringify(this.get('data')));
			var cleanClone = function(vals){
				_.each(vals, function(el, i){
					if(typeof i === "string" && i[0] === '_'){
						delete vals[i];
					}else if(_.isObject(vals[i])){
						cleanClone(vals[i]);
					}
				});
				return vals;
			};

			cloneData = cleanClone(cloneData);
			clone.set('data', cloneData, {silent: true});
			clone.set('collection', cell.widgets, {silent: true});
			clone.cell = cell;
			clone.isDuplicate = true;
			return clone;
		}
	});

	$.CpPage.Collection.Widgets = Backbone.Collection.extend({
		model: $.CpPage.Model.Widget
	});


  $.CpPage.View.Section = Backbone.View.extend({
    template: wp.template( "cppress-page-section" ),
    pageBuilder: null,
		sortObj: null,
    events: {
			"click .cp-section-dropdown[data-action=add]" : "addGrid",
			"click .cp-section-dropdown[data-action=delete]" : "deleteConfirm",
			"click .cp-section-dropdown[data-action=duplicate]": "duplicate",
			"click .cp-section-dropdown[data-action=edit]": "edit",
			"blur .cp-press-section-titleslug": "changeTitleSlug"
		},

    initialize: function(){
			this.model.on('change', function(){
				this.pageBuilder.model.refreshLayoutData();
			}, this);
			this.model.on('destroy', this.onModelDestroy, this);
			this.model.grids.on('add', this.onAddGrid, this);
    },

    render: function(){
      this.setElement(this.template());
      this.$el.data('view', this);
      var _that = this;
      this.model.grids.each(function(grid){
        var gridView = new $.CpPage.View.Grid({model: grid});
        gridView.section = _that;
        gridView.render();
        gridView.$el.appendTo(_that.$('.cp-grids-container'));
      });
      if(this.model.get('title') !== ''){
        this.$el.find('.cp-press-section-titleslug[data-model=title]').val(
          this.model.get('title')
        );
      }
      if(this.model.get('slug') !== ''){
        this.$el.find('.cp-press-section-titleslug[data-model=slug]').val(
          this.model.get('slug')
        );
      }
     
      return this;
    },

		duplicate: function(e){
			e.preventDefault();
			var duplicateSection = this.model.clone();
			this.pageBuilder.model.sections.add(duplicateSection, {
				at: this.pageBuilder.model.sections.indexOf(this.model)+1
			});
		},

		edit: function(e){
			e.preventDefault();
		},

		changeTitleSlug: function(e){
			var $$ = $(e.target);
			var section = $$.attr('data-section');
			var toSet = {};
			toSet[$$.attr('data-model')] = $$.val();
			this.model.set(toSet);
		},

		deleteConfirm: function(e){
			$.CpPage.fn.deleteConfirm(e, this);
		},

		destroy: function(){
			var _that = this;
			this.$el.fadeOut('normal', function(){
				_that.model.destroy();
				_that.pageBuilder.model.refreshLayoutData();
			});
		},

		onModelDestroy: function(){
			this.remove();
		},

		addGrid: function(e){
			e.preventDefault();
			this.dialog = new $.CpDialog.addGrid.View();
			this.dialog.setView(this);
			this.dialog.setModel(this.model);
			this.dialog.render();
			this.dialog.on('grid:add', function(){
				this.pageBuilder.model.refreshLayoutData();
			}, this);
		},

		onAddGrid: function(grid){
			var gridView = new $.CpPage.View.Grid({model: grid});
			gridView.section = this;
			gridView.render();
			gridView.$el.appendTo(this.$('.cp-grids-container')).hide().fadeIn();
			this.refreshSortable();
			this.pageBuilder.initDropable();
		},

		initSortable: function(){
			this.sortObj = this.$el.find(".cp-grids-container").cpsortable();
			this.sortObj.setPlaceHolder("cp-row-portlet-placeholder");
			this.sortObj.sort({
				axis		: "y",
				handle	: ".cp-row-move",
				scroll	: false,
				stop	: this.pageBuilder.sort,
			});
		},

		refreshSortable: function(){
			this.sortObj.refresh();
		}

  });

  $.CpPage.View.Grid = Backbone.View.extend({
    template: wp.template( "cppress-page-grid" ),
		dialog: null,
    events: {
			"click .cp-row-dropdown[data-action=edit]" : "edit",
			"click .cp-row-dropdown[data-action=delete]" : "deleteConfirm",
			"click .cp-row-dropdown[data-action=duplicate]": "duplicate"
		},

    initialize: function(){
			this.model.on('destroy', this.onModelDestroy, this);
			this.model.cells.on('add', this.onAddCell, this);
			this.model.cells.on('remove', this.onRemoveCell, this);
			this.model.cells.on('reset', this.onResetCells, this);
    },

		deleteConfirm: function(e){
			$.CpPage.fn.deleteConfirm(e, this);
		},

		duplicate: function(e){
			e.preventDefault();
			var duplicateGrid = this.model.clone(this.section.model);
			this.section.model.grids.add(duplicateGrid, {
				at: this.section.model.grids.indexOf(this.model)+1
			});
		},

		edit: function(e){
			this.dialog = new $.CpDialog.editGrid.View();
			this.dialog.setView(this);
			this.dialog.setModel(this.model);
			this.dialog.render();
			this.dialog.on('grid:edit', function(){
				this.section.pageBuilder.model.refreshLayoutData();
				this.section.pageBuilder.initDropable();
			}, this);
			return false;
		},

		destroy: function(){
			var _that = this;
			this.$el.fadeOut('normal', function(){
				_that.model.destroy();
				_that.section.pageBuilder.model.refreshLayoutData();
			});
		},

		onModelDestroy: function(){
			this.remove();
		},

    render: function(){
      this.setElement(this.template());
      this.$el.data('view', this);
      var _that = this;
      this.model.cells.each(function(cell){
        var cellView = new $.CpPage.View.Cell({model: cell});
        cellView.grid = _that;
				cellView.pageBuilder = _that.section.pageBuilder;
        cellView.render();
        cellView.$el.appendTo(_that.$('.cp-rows'));
      });

      return this;
    },

		onAddCell: function(cell){
			var cellView = new $.CpPage.View.Cell({model: cell});
			cellView.grid = this;
			cellView.pageBuilder = this.section.pageBuilder;
			cellView.render();
			cellView.$el.appendTo(this.$('.cp-rows'));
		},

		onRemoveCell: function(cell){
			this.$el.find('.cp-grid').each(function(){
				var view = $(this).data('view');
				if(typeof view === "undefined"){
					return false;
				}
				if(view.model.cid == cell.cid){
					view.remove();
				}
			});
		},

		onResetCells: function(cells, options){
			_.each(options.previousModels, function(cell){
				this.onRemoveCell(cell);
			}, this);
		}
  });

  $.CpPage.View.Cell = Backbone.View.extend({
    template: wp.template( "cppress-page-cell" ),
		sortObj: null,

    initialize: function(){
			/*this.model.on('change', function(){
				this.pageBuilder.model.refreshLayoutData();
			}, this);*/
			this.model.on('destroy', this.onModelDestroy, this);
			this.model.widgets.on('add', this.onAddWidget, this);
    },

    render: function(){
      var tArgs = {
        weight: this.model.get('weight')
      };
      this.setElement(this.template(tArgs));
      this.$el.data('view', this);
			this.model.widgets.each(function(widget){
				var widgetView = new $.CpPage.View.Widget({model: widget});
				widgetView.cell = this;
				widgetView.render();
				widgetView.$el.appendTo(this.$el.find('.cp-row-droppable')).hide().fadeIn();
			}, this);
			this.initSortable();
    },

		initSortable: function(){
			var _that = this;
			var builderId = this.pageBuilder.$el.attr('id');
			this.sortObj = this.$el.find('.cp-widgets-container').cpsortable();
			this.sortObj.setPlaceHolder("cp-widget-portlet-placeholder");
			this.sortObj.options.connectWith = '#'+ builderId +' .cp-rows .cp-grid .cp-widgets-container';
			this.sortObj.sort({
				tolerance: 'pointer',
				srcoll: false,
				stop: function(e, ui){
					var widget = $(ui.item).data('view');
					var targetCell = $(ui.item).closest('.cp-grid').data('view');

					widget.model.moveToCell(targetCell.model);
					widget.cell = targetCell;
					_that.pageBuilder.sort();
				},
				helper: function(e, el){
					var helper = el.clone()
						.css({
							'width': el.outerWidth(),
							'z-index': 10000,
							'position': 'absolute'
						});
					if(el.outerWidth() > 720){
						helper.animate({
							'margin-left': e.pageX - el.offset().left - (480/2),
							'width': 480
						}, 'fast');
					}
					return helper;
				}
			});
		},

		refreshSortable: function(){
			this.sortObj.refresh();
		},

		onModelDestroy: function(){
			this.remove();
		},

		loadWidget: function($widget){
			var widget = new $.CpPage.Model.Widget();
			widget.cell = this.model;
			var values = {};
			widget.setData(values);
			widget.set('title', $widget.data().widgetTitle);
			widget.set('icon', $widget.data().widgetIcon);
			widget.set('description', $widget.data().widgetDescription);
			widget.set('class', $widget.data().widgetClassname);
			widget.set('raw', false);
			widget.set('id_base', $widget.attr('id'));
			this.model.widgets.add(widget);
		},

		onAddWidget: function(widget){
			var widgetView = new $.CpPage.View.Widget({model: widget});
			widgetView.cell = this;
			widgetView.pageBuilder = this.pageBuilder;
			widgetView.render();
			widgetView.$el.appendTo(this.$el.find('.cp-row-droppable')).hide().fadeIn();
			this.refreshSortable();
      this.pageBuilder.model.refreshLayoutData();
		}
  });

	$.CpPage.View.Widget = Backbone.View.extend({
		template: wp.template( "cppress-page-widget" ),
		cell: null,
		events: {
				'click .title h4': 'edit',
				'click .actions .widget-edit': 'edit',
				'click .actions .widget-delete': 'delete',
				'click .actions .widget-duplicate': 'duplicate'
		},

		initialize: function(){
			this.model.on('destroy', this.onDestroy, this);
			this.model.on('visual_destroy', this.destroy, this);
		},

		delete: function(){
			this.model.trigger('visual_destroy');
			return false;
		},

		destroy: function(){
			var _that = this;
			this.$el.fadeOut('fast', function(){
				_that.model.destroy();
				_that.pageBuilder.model.refreshLayoutData();
			});
		},

		duplicate: function(){
			var newWidget = this.model.clone(this.model.cell);
			this.cell.model.widgets.add(newWidget, {
				at: this.model.collection.indexOf(this.model)+1
			});
			//this.pageBuilder.model.refreshLayoutData();
			return false;
		},

		edit: function(e){
			e.preventDefault();
			this.dialog = new $.CpDialog.editWidget.View();
			this.dialog.setView(this);
			this.dialog.setModel(this.model);
			this.dialog.render();
      this.dialog.on('widget:edit', function(){
        var widgetTitle = this.model.get('title');
        if(this.model.getData('wtitle') !== null){
          widgetTitle += " - - " + this.model.getData('wtitle');
        }
        this.$el.find('.title h4').html(widgetTitle);
        this.pageBuilder.model.refreshLayoutData();
      }, this);
			return false;
		},

		onDestroy: function(){
			this.remove();
		},

		render: function(){
		  var widgetTitle = this.model.get('title');
		  if(this.model.getData('wtitle') !== null){
		    widgetTitle += " - - " + this.model.getData('wtitle');
		  }
			this.setElement(this.template({
				title: widgetTitle,
				description: this.model.get('description')
			}));
			this.$el.data('view', this);
			return this;
		}
	});

  $.CpPage.View.PageBuilder = Backbone.View.extend({
    template: wp.template('cppress-page'),
    layout: null,
    elData: null,

    $input: null,
    $rowHead: null,

		sortObj: null,

    sections: {},

		$widgetsList: null,

		dragOptions: {
			snap: '.cp-row-droppable',
			scrollSpeed: 13,
			scrollSensitivity: 100
		},

		dropOptions: {},

    events: {
      "click #cp_add_section" : "addSection",
      "click #cp_export_content": "export",
      "click #cp_import_content": "import",
      "change #cp_import_content .cp-importer": "importJsonFile"
    },

    initialize: function(){
			_.bindAll(this, 'sort');
      this.$input = $('#cp-press-layout-input');
      this.$rowHead = $('#cp_press_rows_head');
      this.elData = this.$el.data();
      this.layout = JSON.parse(this.$input.val());
      this.listenTo(this.model.sections, "add", this.onSectionAdd);
			this.listenTo(this.model, "change:data", this.storeLayout);
			this.initDragable();
    },
    
    render: function(){
      $p = $(this.template());
      $p.insertAfter(this.$rowHead);
      this.initSortable();
      this.initDropable();
      return this;
    },

    load: function(){
      this.model.loadLayoutData(this.layout);
    },

		dropWidget: function(ev, ui){
			var cellView = $(ev.target).parent().data('view');
			cellView.loadWidget(ui.draggable);
		},

    addSection: function(){
        section = new $.CpPage.Model.Section();
        section.setGrids(1, [[12]]);
        this.model.sections.add(section);
    },
    
    initDragable: function(){
    	this.$widgetsList = $('#cp-press-page-widgets').find('li.cp-draggable')
													.cpdragdrop($('div.cp-row-droppable'));
			_.each(this.$widgetsList, function(el){
				el.drag(this.dragOptions);
			}, this);
    },

		initDropable: function(){
			this.dropOptions.drop = this.dropWidget;
			this.$widgetsList = $('#cp-press-page-widgets').find('li.cp-draggable')
													.cpdragdrop($('div.cp-row-droppable'));
			_.each(this.$widgetsList, function(el){
				el.setDropable($('div.cp-row-droppable'));
				el.drop(this.dropOptions);
			}, this);
		},

		initSortable: function(){
			this.sortObj = this.$el.find('#cp_press_rows_container').cpsortable();
			this.sortObj.setPlaceHolder("cp-row-portlet-placeholder");
			this.sortObj.sort({
				axis		: "y",
				handle	: ".cp-section-move",
				scroll	: false,
				stop	: this.sort
			});
		},

		refreshSortable: function(){
			this.sortObj.refresh();
		},

		sort: function(){
			var indexes = {};
			var _that = this;
			this.$el.find('section').each(function(skey){
				indexes[$(this).data('view').model.cid] = skey;
				$(this).find('div.cp-grids').each(function(gkey){
					indexes[$(this).data('view').model.cid] = gkey;
					$(this).find('div.cp-grid').each(function(ckey){
						$(this).find('div.cp-widget').each(function(wkey){
							indexes[$(this).data('view').model.cid] = wkey;
						});
						indexes[$(this).data('view').model.cid] = ckey;
					});
				});
			});
			this.model.sections.models = this.model.sections.sortBy(function(model){
				return indexes[model.cid];
			});

			this.model.sections.each(function(section){
				section.grids.models = section.grids.sortBy(function(model){
					return indexes[model.cid];
				});
				section.grids.each(function(grid){
					grid.cells.models = grid.cells.sortBy(function(model){
						return indexes[model.cid];
					});
					grid.cells.each(function(cell){
						cell.widgets.models = cell.widgets.sortBy(function(model){
							return indexes[model.cid];
						});
					});
				});
			});

			this.model.refreshLayoutData();
		},

    onSectionAdd: function(section, collection, options){
      var sectionView = new $.CpPage.View.Section({model: section});
      sectionView.pageBuilder = this;
      sectionView.render();
      sectionView.$el.appendTo(this.$('#cp_press_rows_container')).hide().fadeIn();
			sectionView.initSortable();
			this.refreshSortable();
			this.initDropable();
      this.model.refreshLayoutData();
    },

		storeLayout: function(){
			var data = JSON.stringify(this.model.get('data'));
			if(this.$input.val() !== data){
				this.$input.val(data);
				this.$input.trigger("change");
				this.trigger("layout-changed");
			}
		},
		
		export: function(e){
		  var data = this.$input.val();
		  var file = new Blob([data], {type: 'application/json'});
		  var fileName = 'export-' + this.elData.postname + '-' + this.elData.post + '.json';
		  var a = document.createElement('a');
		  a.href = URL.createObjectURL(file);
		  a.download = fileName;
		  a.click();
		},
		
		import: function(e){
		  var $$ = $(e.target);
		  $$.find('#cp-importer').click();
		},
		
		importJsonFile: function(e){
		  var file = e.target.files[0];
		  if(!file){
		    return;
		  }
		  var reader = new FileReader();
		  var _that = this;
		  reader.onload = function(e){
		    var contents = e.target.result;
		    try{
		      _that.layout = JSON.parse(contents);
		      _that.$input.val(contents);
		      _that.render().load();
		    }catch(e){
		      console.log(e);
		      alert('File must be JSON encoded: not a JSON encoded file');
		    }
		  };
		  reader.readAsText(file);
		}

  });

	$.CpPage.fn.deleteConfirm = function(e, view){
		e.preventDefault();
		var $$ = $(e.target);
		if($$.hasClass('dashicons')){
			$$ = $$.parent();
		}
	
		if($$.hasClass('cp-delete-confirmed')){
			view.destroy();
		}else{
			var oText = $$.html();
			$$.addClass('cp-delete-confirmed').html(
				'<span class="dashicons dashicons-yes"></span> Are you sure?'
			);
			setTimeout(function(){
				$$.removeClass('cp-delete-confirmed').html(oText);
			}, 2500);
		}
	
	};

}(jQuery, _));
