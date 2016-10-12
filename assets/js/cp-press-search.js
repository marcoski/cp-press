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

        paginate: function(page, query){
            if(query.hasOwnProperty('offset')){
                query.offset = (page-1) * query.posts_per_page;
            }
            this.fetch({data: {query: query}});
        }
    });

    var SearchItem = Backbone.View.extend({
        model: null,
        tagName: 'div',
        template: _.template($('#search-item-template').html()),
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
        },

        render: function(){
            this.$el.addClass(this.col);
            _.each(this.classes, function(c){
               this.$el.addClass(c);
            }, this);
            this.$el.append(this.template({data: this.model.attributes}));
            return this;
        }

    });

    var Search = Backbone.View.extend({

        collection: null,
        col: 'col-md-12',
        classes: [],

        initialize: function(options){
            this.collection = options.collection;
            if(options.hasOwnProperty('col')){
                this.col = options.col;
            }
            if(options.hasOwnProperty('classes')){
                this.classes = options.classes;
            }
        },

        render: function(){
            this.$el.html('');
            this.collection.each(function(item){
                this.addItem(item);
            }, this);

            return this;
        },

        addItem: function(item){
            var searchItem = new SearchItem({model: item, col: this.col, classes: this.classes});
            this.$el.insertAndFadeIn(searchItem.render().$el);
        }

    });

    Backbone.WPPost = WPPost;
    Backbone.SearchResults = SearchResults;
    Backbone.Search = Search;

})(jQuery, _);
