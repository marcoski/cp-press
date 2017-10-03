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

    $.inArrayComparer = function(array, comparer){
        for(var i=0; i < array.length; i++){
            if(comparer(array[i])){
                return true;
            }
        }

        return false;
    };

    $.pushIfNotExists = function(array, element, comparer){
        if(!$.inArrayComparer(array, comparer)){
            array.push(element);
        }
    }
})(jQuery);