(function($, _){
    Backbone.ajax = function(){
        var args = Array.prototype.slice.call(arguments, 0);

        args[0]['data']['_front_ajax'] = 1;
        return Backbone.$.ajax.apply(Backbone.$, args);
    };

    var WPPost = Backbone.Model.extend({
        url: ajaxurl+'?action=cppress_search'
    });

    var SearchResults = Backbone.Collection.extend({
        model: WPPost,
        url: ajaxurl+'?action=cppress_search',

        foundPosts: 0,

        paginate: function(page, query){
            var _that = this;
            if(query.hasOwnProperty('offset')){
                query.offset = (page-1) * query.posts_per_page;
            }
            this.fetch({
                data: {query: query},
                success: function(collection, response, options){
                    _that.set(response.posts);
                    _that.foundPosts = response.total;
                    _that.trigger('sync:paginate');
                },
                error: function(collection, response, options){
                    _that.trigger('sync:paginate:error');
                }
            });
            this.trigger('sync:paginate');
        },

        search: function(query){
            var _that = this;
            this.fetch({
                data: {query: query},
                reset: true,
                success: function(collection, response, options){
                    _that.set(response.posts);
                    _that.foundPosts = response.total;
                    _that.trigger('sync:search')
                },
                error: function(collection, response, options){
                    _that.trigger('sync:search:error')
                }
            });
        }
    });

    var SearchItem = Backbone.View.extend({
        model: null,
        tagName: 'div',
        template: null,
        col: 'col-md-12',
        classes: [],

        initialize: function(options){
            this.model = options.model;
            if(options.hasOwnProperty('col')){
                this.col = options.col;
            }
            if(options.hasOwnProperty('classes')){
                this.classes = options.classes;
            }

            if(_.isUndefined(options.template)){
                if($('#search-item-template').length > 0){
                    this.template = _.template($('#search-item-template').html());
                }
            }else{
                if($(options.template).length > 0) {
                    this.template = _.template($(options.template).html());
                }
            }

            if(options.hasOwnProperty('tagName')){
                this.tagName = options.tagName;
            }
        },

        render: function(){
            this.$el.addClass(this.col);
            _.each(this.classes, function(c){
               this.$el.addClass(c);
            }, this);
            try {
                this.$el.append(this.template({data: this.model.attributes}));
            }catch(err){
                console.error('You must specify a template for search item with id #search-item-template');
            }
            return this;
        }

    });

    var Search = Backbone.View.extend({

        collection: null,
        col: 'col-md-12',
        classes: {},
        template: null,
        searchItemTag: 'div',

        initialize: function(options){
            this.collection = options.collection;
            if(options.hasOwnProperty('col')){
                this.col = options.col;
            }
            if(options.hasOwnProperty('classes')){
                this.classes = options.classes;
            }

            if(!_.isUndefined(options.template)){
                this.template = options.template;
            }

            if(options.hasOwnProperty('searchItemTag')){
                this.searchItemTag = options.searchItemTag;
            }
        },

        render: function(){
            this.$el.html('');
            this.collection.each(function(item, key){
                item.set('count', key);
                this.addItem(item);
            }, this);

            return this;
        },

        addItem: function(item){
            var searchItem = new SearchItem({
                model: item,
                col: this.col,
                classes: this.classes,
                template: this.template,
                tagName: this.searchItemTag,
            });
            this.$el.insertAndFadeIn(searchItem.render().$el);
        }

    });

    var Filters = Backbone.View.extend({
        events: {
            'click .dropdown-title': 'toggle',
            'change .dropdown-input': 'dropdown',
            'keyup .simple-input': 'simple',
            'keydown .simple-input': 'prevent',
            'click .tags-item a': 'removeFilter'
        },

        checked: [],
        checkedLabel: [],

        collection: null,

        query: null,
        originalQuery: null,

        itemsView: null,

        paginator: null,

        toggled: null,

        $infoBox: null,

        filterActiveTemplate: _.template('<li class="tags-item"> <a href="#" data-filter="<%= filter_active %>"><%= tag %></a> </li>'),
        filterActive: null,

        timeoutId: null,

        initialize: function(options){
            this.itemsView = options.itemsView;
            this.query = this.$el.data('query');
            this.originalQuery = _.clone(this.query);
            this.collection = options.collection;
            if(!_.isUndefined(options.paginator)) {
                this.paginator = options.paginator;
            }
            this.$infoBox = this.$el.find('.filters-info-box');
            this.listenTo(this.collection, 'sync:search', this.render);
        },

        render: function(){
            this.$el.data('query', this.query);
            if(null !== this.toggled){
                this.toggle(this.toggled);
            }
            if(null === this.paginator){
                this.itemsView.render();
                this.refreshInfoBox();
                $(window).trigger('filter.onsuccess');
            }else{
                this.paginator.refresh(this.query);
                this.refreshInfoBox();
            }
        },

        refreshInfoBox: function(){
            var $tagsList = this.$infoBox.find('.filters-active ul.tags');
            this.$infoBox.find('.filters-result-count span.filters-result-count-number').html(this.collection.foundPosts);
            if(this.$infoBox.find('.filters-active').hasClass('is-empty')){
                this.$infoBox.find('.filters-active').removeClass('is-empty');
            }
            if(this.checkedLabel !== null){
                $tagsList.prepend(this.filterActiveTemplate({
                    tag: this.checkedLabel,
                    filter_active: _.escape(JSON.stringify(this.filterActive))
                }));
            }

        },

        removeFilter: function(e){
            e.preventDefault();
            $(window).trigger('filter.onsearch');
            var $$ = $(e.target);
            var data = ($$.data());
            var $checked = $('.dropdown-input:checked');
            if(data.hasOwnProperty('filter')){
                var filter = data.filter;
                var _that = this;
                _.each(filter, function(f, k){
                    if(this.query.hasOwnProperty(k)){
                        delete this.query[k];
                        $checked.each(function(){
                           _.each(f, function(v){
                               if(v === $(this).val()){
                                   $(this).prop('checked', false);
                                   $(this).removeAttr('checked');
                               }
                           }, this);
                        });
                    }
                }, this);
                $$.parent().fadeOutAndRemove();
            }else if(data.hasOwnProperty('filterRemoveAll')){
                this.query = _.clone(this.originalQuery);
                $checked.each(function(){
                    $(this).prop('checked', false);
                });
                $('.tags-item:not(.tags-item-clear-all)').remove();
            }
            this.checkedLabel = null;
            this.filterActive = null;
            this.collection.search(this.query);
        },

        toggle: function(e){
            var $$ = $(e.target);
            this.toggled = e;
            $$.siblings('ul.dropdown-list').toggle();
        },

        prevent: function(e){
            var code = (e.keyCode ? e.keyCode : e.which);
            if(code === 13){
                e.preventDefault();
            }
        },

        simple: function(e){
            var $$ = $(e.target);
            var code = (e.keyCode ? e.keyCode : e.which);
            if(code === 13){
                e.preventDefault();
                this.simpleSearch($$, e.target.value);
                return;
            }
            clearTimeout(this.timeoutId);
            this.timeoutId = setTimeout(_.bind(this.simpleSearch, this, $$, e.target.value), 500)
        },

        simpleSearch: function($input, searchStr){
            _.extend(this.query, {'s': searchStr});
            $(window).trigger('filter.onsearch');
            this.collection.search(this.query);
        },

        dropdown: function(e){
            var $$ = $(e.target);
            $(window).trigger('filter.onsearch');
            var inputQuery = $$.data('input-query');
            var type = $$.data('type');
            this.checkedLabel = $$.parents('label').find('span').text();
            var queryObj = {
                value: $$.val()
            };
            if(!this.checked.hasOwnProperty(type)){
                this.checked[type] = [];
            }
            $.pushIfNotExists(this.checked[type], queryObj, function(e){
                return e.value === queryObj.value;
            });

            if(!_.isUndefined(this[type])){
                this.filterActive = this[type](queryObj, inputQuery);
                _.extend(this.query, this.filterActive);
            }else{
                this.filterActive = this.custom(queryObj, inputQuery, type);
                _.extend(this.query, this.filterActive);
            }

            this.collection.search(this.query);
        },

        category: function(queryObj, inputQuery){
            _.each(this.checked['category'], function(obj, key){
                if(inputQuery.hasOwnProperty('category__in')){
                    inputQuery['category__in'].push(obj.value);
                }
            }, this);
            return inputQuery
        },

        post_tag: function(queryObj, inputQuery){
            _.each(this.checked['post_tag'], function(obj, key){
                if(inputQuery.hasOwnProperty('tag__in')){
                    inputQuery['tag__in'].push(obj.value);
                }
            }, this);
            return inputQuery
        },

        custom: function(queryObj, inputQuery, type){
            _.each(this.checked[type], function(obj, key){
                if(inputQuery.hasOwnProperty('tax_query')){
                    inputQuery['tax_query'][0]['terms'].push(obj.value);
                }
            }, this);
            return inputQuery;
        }
    });

    Backbone.WPPost = WPPost;
    Backbone.SearchResults = SearchResults;
    Backbone.Search = Search;
    Backbone.Filters = Filters;

})(jQuery, _);
