(function($){
  $('.cppress_load_more a').click(function(e){
    e.preventDefault();
    var _that = $(this);
    var data = { action: 'cppress_loop_loadmore', '_cppress_front_ajax': 1 };
    var elData = $(this).data();
    console.log(elData);
    data = $.extend({}, data, $(this).data());
    $.post(data.url, data, function(response){
      if(response.extra.hasmore){
        var $appendContainer = _that.parents()
        .filter('.cppress_load_more')
        .siblings('[id^="cppress_more_posts_"]');
        $appendContainer.append(response.data);
        _that.attr('data-options', JSON.stringify(response.extra.data));
        _that.data(response.extra.data);
      }
    });
  });
})(jQuery);
