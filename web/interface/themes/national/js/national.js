/**
 * Initialize functions used by the National layout
 */
/**
* hoverIntent r6 // 2011.02.26 // jQuery 1.5.1+
* <http://cherne.net/brian/resources/jquery.hoverIntent.html>
* 
* @param  f  onMouseOver function || An object with configuration options
* @param  g  onMouseOut function  || Nothing (use configuration options object)
* @author    Brian Cherne brian(at)cherne(dot)net
*/
(function($){$.fn.hoverIntent=function(f,g){var cfg={sensitivity:7,interval:100,timeout:0};cfg=$.extend(cfg,g?{over:f,out:g}:f);var cX,cY,pX,pY;var track=function(ev){cX=ev.pageX;cY=ev.pageY};var compare=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);if((Math.abs(pX-cX)+Math.abs(pY-cY))<cfg.sensitivity){$(ob).unbind("mousemove",track);ob.hoverIntent_s=1;return cfg.over.apply(ob,[ev])}else{pX=cX;pY=cY;ob.hoverIntent_t=setTimeout(function(){compare(ev,ob)},cfg.interval)}};var delay=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);ob.hoverIntent_s=0;return cfg.out.apply(ob,[ev])};var handleHover=function(e){var ev=jQuery.extend({},e);var ob=this;if(ob.hoverIntent_t){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t)}if(e.type=="mouseenter"){pX=ev.pageX;pY=ev.pageY;$(ob).bind("mousemove",track);if(ob.hoverIntent_s!=1){ob.hoverIntent_t=setTimeout(function(){compare(ev,ob)},cfg.interval)}}else{$(ob).unbind("mousemove",track);if(ob.hoverIntent_s==1){ob.hoverIntent_t=setTimeout(function(){delay(ev,ob)},cfg.timeout)}}};return this.bind('mouseenter',handleHover).bind('mouseleave',handleHover)}})(jQuery);
$(document).ready(function() {
    initMenu();
});


// Header menu

function initMenu() {
/*    
    // Mouse enter
    $('#headerMenu li').hover(function() {
        var subMenu = $(this).children('ul');
        var subMenuHeight = subMenu.show().height();
        
        subMenu.hide().css('visibility', 'visible');
        
        $('#headerTop').stop().animate({
           height: 95 + subMenuHeight + 10 // header + bottom padding
        }, 300);
        
        subMenu.stop().delay(150).fadeIn(300);
    
    // Mouse leave
    }, function() {
         var subMenu = $(this).children('ul');
         $('#headerTop').height('95px');
         subMenu.stop().fadeOut(100);
         
    });
    */
   
   function headerOver() {
         var subMenu = $(this).children('ul');
        var subMenuHeight = subMenu.height();
        
        console.log(subMenuHeight);
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
