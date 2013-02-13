/**
 * Initialize National theme specific functions
 */

$(document).ready(function() {
    initHeaderMenu();
    initContentMenu();
    initCarousel();
    initSidebarFacets();
    initInfoBox();
    initDateVisHandle();
    initCustomEyeCandy();
});

// Header menu
function initHeaderMenu() {
    function headerOver() {
        var timeoutId = $('#headerTop').data('timeoutId');
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        var subMenu = $(this).children('ul');
        var subMenuHeight = subMenu.height();
        var menuHeight;
        if (subMenuHeight == 0 || typeof subMenuHeight === 'undefined') {
            menuHeight = 95;
        } else {
            menuHeight = subMenuHeight + 105;  // header + bottom padding
        }
        $('#headerTop').stop().animate({height: menuHeight}, 300);
        $('#headerMenu > li > ul').each(function(index, element) {
            if (!$(element).is(subMenu)) {
                $(element).stop(true, true).hide();
            }
        });
        subMenu.stop(true, true);
        if (!subMenu.is(':visible')) {
            subMenu.delay(50).fadeIn(50);
        }
    };
   
    function headerOut(e) {
        var subMenu = $('#headerMenu > li > ul');
        $('#headerTop').data('timeoutId', setTimeout(function() {
            $('#headerTop').stop().animate({height: 95}, 300);
            subMenu.stop(true, true).fadeOut(50);
        }, 300));
    };

    $('#headerMenu > li').mouseenter(headerOver);
    $('#headerMenu').mouseleave(headerOut);
    $('#headerMenu > li > a[href="#"]').click(function() {
        return false;
    });
    
}

// Front page content carousel
function initCarousel() {
    var ribbonH = $('#carousel h2.ribbon').height();
    $("#carousel").slides({
         play: 9000,
         pause: 9000,
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
};

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
               $('.dateVisHelp').fadeToggle(200);
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
        });
        menu += '</ul></div>';
        $('.module-Content .main .menu').append(menu);
        $('.module-Content .main .menu li').click(function(event){		
            $('html,body').animate({
                scrollTop:$('h2:contains('+$(this).text()+')').offset().top - 10
            }, 500);
        });
    }
}

// Sidebar facets visibility toggler
function initSidebarFacets() {
    $('.sidegroup dl').each(function() {
        
        // Determine facet use by finding the indicator image
        if ($(this).find('img[alt="Selected"]').length > 0) {
                $(this).addClass('active');
        }
        
        else if ($(this).find('dd').length == 0) $(this).addClass('collapsed open');
        
        // Mark dl collapsible
        $(this).addClass('collapsible');
    });
    
    // Toggle facet visibility by clicking its title
    $('.sidegroup dt').click(function() {
        
        // Get the facet container
        var parentDl = $(this).parent('dl');
        
        // Do nothing if facet selected
        if (parentDl.find('img[alt="Selected"]').length > 0) return false;
        
        // Make sure this facet has options
        if (parentDl.find('dd').length > 0) {
            
            // Slide them
            parentDl.children('dd').slideToggle(100);
         
            // If user has clicked "more", hide the additional facets too
            if (!parentDl.next('dl').hasClass('offscreen') && parentDl.hasClass('open')) {
                parentDl.next('dl[id*="Hidden"]').addClass('offscreen');
                parentDl.find('dd[id*="more"]').hide();
            }
                
            // Finally, mark this facet container opened
            parentDl.toggleClass('open collapsed');
        }
        
        // Extend to default facets (without dd children)
        
        else {
            parentDl.nextUntil('div').last().next().slideToggle(100);
            parentDl.toggleClass('open collapsed');
        }
        
    });
}

// Custom jQuery effects
function initCustomEyeCandy() {
    
    // Slide record page thumbnail enlarge button up/down
    $('#thumbnail_link').hover(function() {
        $(this).children('span').stop().delay(100).animate({top:0}, 150);
    }, function() {
        $(this).children('span').stop().delay(100).animate({top:-50}, 150);
    });
}