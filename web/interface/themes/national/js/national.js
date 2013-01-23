/**
 * Initialize National theme specific functions
 */

$(document).ready(function() {
    initHeaderMenu();
    initContentMenu();
    initCarousel();
    initInfoBox();
    initDateVisHandle();
});

// Header menu
function initHeaderMenu() {
   
    function headerOver() {
        var subMenu = $(this).children('ul');
        var subMenuHeight = subMenu.height();
        $('#headerTop').stop().animate({height: subMenuHeight + 105 // header + bottom padding
        }, 300);
        
        subMenu.stop(true,true).delay(50).fadeIn(50);
    };
   
    function headerOut() {
       var subMenu = $(this).children('ul');
         $('#headerTop').stop().animate({height: 95}, 300);
         subMenu.stop(true,true).fadeOut(50);
    };

    $('#headerMenu > li').hover(headerOver, headerOut);
    $('#headerMenu > li > a[href="#"]').click(function() {
        return false;
    })
}

// Front page content carousel
function initCarousel() {
    var ribbonH = $('#carousel h2.ribbon').height();
    $("#carousel").slides({
         play: 5000,
         pause: 2500,
         hoverPause: true
    });
    if ($("#carousel .slide").length > 1)
    $("#carousel a.prev, #carousel a.next").removeClass('disabled');
    
   
    function slideOver() {
        pickupHeight = $(this).children('.pickup-content').height();
        $(this).children('.pickup-content').stop().animate({top:314-pickupHeight}, 300);
        if ($(this).index() == 0) $('#carousel h2.ribbon').stop()
            .animate({height:0,padding:'0 7px',opacity:0}, 400);
    }

    function slideOut() {
        $(this).children('.pickup-content').stop().delay(100).animate({top:253}, 300);
        if ($(this).index() == 0) $('#carousel h2.ribbon').stop().delay(100)
            .animate({height:ribbonH,padding:'5px 7px',opacity:1}, 100);
    }

    $('#carousel li').hover(slideOver, slideOut);
}

// Home page header info box
function initInfoBox() {
    
    $('.toggleBox').click(function() {
        toggleInfoBox();
    });
}

function toggleInfoBox() {
    var box = $('.headerInfoBox');
    box.toggleClass('visible');
    var boxWidth = box.hasClass('visible') ? 395 : 25;
    var boxHeight = box.hasClass('visible') ? $('.infoBoxText').height() +30 : 25;
    $('.openInfoBox, .closeInfoBox, .infoBoxText').stop(true, true).toggle();
    box.stop(true, true).animate({ width: boxWidth, height: boxHeight}, 200);
    $('.infoBoxText').stop(true, true).vToggle().fadeToggle(300);
};

// Helper function: visibility toggler
jQuery.fn.vToggle = function() {
    return this.css('visibility', function(i, visibility) {
        return (visibility == 'visible') ? 'hidden' : 'visible';
    });
}

// Date range selector 
function initDateVisHandle() {
    $('.dateVisHandle').click(function() {
        showDateVis();
    });
    
    function showDateVis() {
        var dateVis = $('.resultDates');
        
        var dateVisHeight = !dateVis.hasClass('expanded') ? 110 : 0;
       dateVis.stop(true, true).animate({ height: dateVisHeight}, 200, function() {
           dateVis.toggleClass('expanded');
           
       });
       $('.resultDatesHeader').toggleClass('expanded');
           $('div.dateVisHandle').not('.visible').fadeIn(300, function() {
               $('div.dateVisHandle.visible').fadeOut(300);
               $('div.dateVisHandle').toggleClass('visible');
           });
    }
}

// Content pages menu 
function initContentMenu() {
    if ( $(".module-Content .main .menu").length > 0 ) {
        var menu = '<div class="content"><ul>';
        $('.module-Content .main h2').each(function() {
            var text = $(this).text(); 
            menu += '<li>'+text+'</li>';
        })
        menu += '</ul></div>';
        $('.module-Content .main .menu').append(menu);
        $('.module-Content .main .menu li').click(function(event){		
            $('html,body').animate({
                scrollTop:$('h2:contains('+$(this).text()+')').offset().top - 10
            }, 500);
        });
    }
}

