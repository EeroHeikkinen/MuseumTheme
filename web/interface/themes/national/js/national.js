/**
 * Initialize National theme specific functions
 */

$(document).ready(function() {
    initMenu();
});


// Header menu
function initMenu() {
   
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
