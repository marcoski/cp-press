(function($){
	$('.cppress_load_more a').on('click touchstart', function(e){
		e.preventDefault();
		var data = $(this).data();
		var $_that = $(this);
		data = $.extend({'action': 'cppress_loop_loadmore', '_cppress_front_ajax': 1}, data);
		data.options.offset = data.options.limit * (data.next-1);
		$(this).insertAndFadeIn('<i class="fa fa-refresh fa-spin fa-fw"></i>');
		$.post(data.url, data, function(response){
			$('#cppress_more_posts_news').insertAndFadeIn(response.data);
			$_that.data('next', data.next+1);
			$_that.find('.fa-refresh').fadeOutAndRemove();
		}, 'json');
	});
	
	$.fn.fadeOutAndRemove = function(speed){
    if(typeof speed === "undefined"){
      speed = "fast";
    }
    $(this).fadeOut(speed, function(){
      $(this).remove();
    });
  };
  
  $.fn.insertAndFadeIn = function(html, speed){
    if(typeof speed === "undefined"){
      speed = "fast";
    }
    $(html).hide().appendTo($(this)).fadeIn(speed);
  };
})(jQuery);