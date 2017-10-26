jQuery(document).ready(function() {
    var $ = jQuery;

    $('.accordion-settings').parents('td').siblings('th').remove();
    $(".accordion-settings").accordion({ header: "h3" });


});