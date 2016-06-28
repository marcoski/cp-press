(function($){
  $(document).on('widget.loaded', function(){
    var divs = ['.cp-widget-gmaps-custom', '.cp-widget-gmaps-rawjson'];
    var cls = '.cp-widget-gmaps-' + $('.cp-widget-gmap-mapstyles select').find(':selected').val();
    $.each(divs, function(k, v){
      $('.cp-widget-gmaps-styles').find(v).addClass('cp-widget-section-hide');
    });
    if(cls != ".cp-widget-gmap-normal"){
      $('.cp-widget-gmaps-styles').find(cls).removeClass('cp-widget-section-hide');
    }
    $('.cp-widget-gmap-mapstyles').on('change', function(event){
      var cls = '.cp-widget-gmaps-' + $(this).find('select').find(':selected').val();
      $.each(divs, function(k, v){
        $('.cp-widget-gmaps-styles').find(v).addClass('cp-widget-section-hide');
      });
      if(cls != ".cp-widget-gmap-normal"){
        $('.cp-widget-gmaps-styles').find(cls).removeClass('cp-widget-section-hide');
      }
    });
  });
  $(document).on('repeater.add', function(e){
    var $$ = $(e.target);
    $title = $$.find("[name*='[customstyles][mapfeature]']");
    if($title.length > 0){
      var label = $title.find(':selected').text(); 
      $$.find('.cp-widget-field-repeater-item-top h4').text(label);
    }
    $title.on('change', function(e){
      $$.find('.cp-widget-field-repeater-item-top h4').text($(e.target).find(':selected').text());
    });
  });
})(jQuery);
