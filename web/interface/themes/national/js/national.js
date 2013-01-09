/**
 * Initialize National theme specific functions
 */

$(document).ready(function() {
    initMenu();
    initCarousel();
});

// Header menu
function initMenu() {
   
    function headerOver() {
        var subMenu = $(this).children('ul');
        var subMenuHeight = subMenu.height();
        $('#headerTop').stop().animate({height: subMenuHeight + 105 // header + bottom padding
        }, 300);
        
        subMenu.delay(150).stop().fadeIn();
    };
   
    function headerOut() {
       var subMenu = $(this).children('ul');
         $('#headerTop').stop().animate({height: 95}, 300);
         subMenu.stop().fadeOut(50);
    };

    $("#headerMenu > li").hover(headerOver, headerOut);
}

// Front page content carousel
function initCarousel() {
    var ribbonH = $('#carousel h2.ribbon').height();
    $("#carousel").slides({
         play: 5000,
         pause: 2500,
         hoverPause: true
    });
   
    function slideOver() {
        pickupHeight = $(this).children('.pickup-content').height();
        $(this).children('.pickup-content').stop().animate({top:313-pickupHeight}, 300);
        if ($(this).index() == 0) $('#carousel h2.ribbon').stop()
            .animate({height:0,padding:'0 9px',opacity:0}, 400);
    }

    function slideOut() {
        $(this).children('.pickup-content').stop().animate({top:253}, 300);
        if ($(this).index() == 0) $('#carousel h2.ribbon').stop()
            .animate({height:ribbonH,padding:'6px 9px',opacity:1}, 100);
    }

    $('#carousel li').hover(slideOver, slideOut);

}