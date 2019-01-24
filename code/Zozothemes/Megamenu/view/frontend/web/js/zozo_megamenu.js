define([
    'jquery'
], function () {;(function($, window, document, undefined) {
    $.fn.zozoMegamenu = function() {
        $(".navigation.zozo-megamenu li.classic .submenu, .navigation.zozo-megamenu li.staticwidth .submenu, .navigation.zozo-megamenu li.classic .subchildmenu .subchildmenu").each(function(){
            $(this).css("left","-9999px");
            $(this).css("right","auto");
        });
        $(this).find("li.classic .subchildmenu > li.parent").mouseover(function(){
            var popup = $(this).children("ul.subchildmenu");
            var w_width = $(window).innerWidth();
            
            if(popup) {
                var pos = $(this).offset();
                var c_width = $(popup).outerWidth();
                if(w_width <= pos.left + $(this).outerWidth() + c_width) {
                    $(popup).css("left","auto");
                    $(popup).css("right","100%");
                    $(popup).css("border-radius","0");
                } else {
                    $(popup).css("left","100%");
                    $(popup).css("right","auto");
                    $(popup).css("border-radius","0");
                }
            }
        });
        $(this).find("li.staticwidth.parent,li.classic.parent").mouseover(function(){
            var popup = $(this).children(".submenu");
            var w_width = $(window).innerWidth();
            
            if(popup) {
                var pos = $(this).offset();
                var c_width = $(popup).outerWidth();
                if(w_width <= pos.left + $(this).outerWidth() + c_width) {
                    $(popup).css("left","auto");
                    $(popup).css("right","0");
                    $(popup).css("border-radius","0");
                } else {
                    $(popup).css("left","0");
                    $(popup).css("right","auto");
                    $(popup).css("border-radius","0");
                }
            }
        });
        $(window).resize(function(){
            $(".navigation.zozo-megamenu li.classic .submenu, .navigation.zozo-megamenu li.staticwidth .submenu, .navigation.zozo-megamenu li.classic .subchildmenu .subchildmenu").each(function(){
                $(this).css("left","-9999px");
                $(this).css("right","auto");
            });
        });
        $(".nav-toggle").click(function(e){
            if(!$("html").hasClass("nav-open")) {
                $("html").addClass("nav-before-open");
                setTimeout(function(){
                    $("html").addClass("nav-open");
                }, 300);
            }
            else {
                $("html").removeClass("nav-open");
                setTimeout(function(){
                    $("html").removeClass("nav-before-open");
                }, 300);
            }
        });
        $("li.ui-menu-item > .open-children-toggle").click(function(){
            if(!$(this).parent().children(".submenu").hasClass("opened")) {
                $(this).parent().children(".submenu").addClass("opened");
                $(this).parent().children("a").addClass("ui-state-active");
            }
            else {
                $(this).parent().children(".submenu").removeClass("opened");
                $(this).parent().children("a").removeClass("ui-state-active");
            }
        });
    };
})(window.Zepto || window.jQuery, window, document);});