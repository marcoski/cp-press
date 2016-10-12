(function($, _){

    var Paginator = Backbone.View.extend({
        collection: null,
        events: {
            'click li': 'paginate'
        },
        query: null,
        itemsView: null,

        clicked: null,
        page: null,

        initialize: function(options){
            this.itemsView = options.itemsView;
            this.collection = options.collection;
            this.listenTo(this.collection, 'sync', this.render);
            this.query = this.$el.data('pagination-query');
        },

        paginate: function(event){
            event.preventDefault();
            var $$ = $(event.target).parents('li');
            this.clicked = $$;
            this.page = $$.data('pagination-page');
            $(window).trigger('paginator.onpaginate');
            this.collection.paginate(this.page, this.query);
        },

        render: function(){
            var _that = this;
            if(this.query.hasOwnProperty('offset')){
               this.query.offset = (this.page-1) * this.query.posts_per_page;
            }
            $.ajax({
                url: ajaxurl+'?action=cppress_paginate',
                data: {
                    query: this.query,
                    paged: this.page,
                    _front_ajax: 1
                },
                success: function(result){
                    _that.$el.html('');
                    _that.$el.append(result.html);
                    _that.$el.data('pagination-query', result.query);
                    _that.itemsView.render();
                    $(window).trigger('paginator.onsuccess');
                }
            });
            return this;
        }

    });

    Backbone.Paginator = Paginator;

})(jQuery, _);
