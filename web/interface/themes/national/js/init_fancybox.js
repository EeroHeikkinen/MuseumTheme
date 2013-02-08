$(document).ready(function() {
    $('.fancybox').fancybox({ nextEffect: 'fade', prevEffect: 'fade' });
});

function launchFancybox(el) {
    var hrefs = new Array();
    hrefs.push($(el).attr('href'));
    var group = $(el).attr('rel');
    $('a[rel="'+group+'"]').each(function(){
        if ($.inArray($(this).attr('href'), hrefs) === -1) {
            hrefs.push($(this).attr('href'));
        }
    });
    $.fancybox.open(hrefs, { type: 'image', nextEffect: 'fade', prevEffect: 'fade' } );
}