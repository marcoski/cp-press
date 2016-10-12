(function($){
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

    $.fn.prependAndFadeIn = function(html, speed){
        if(typeof speed === "undefined"){
            speed = "fast";
        }
        $(html).hide().prependTo($(this)).fadeIn(speed);
    };
})(jQuery);