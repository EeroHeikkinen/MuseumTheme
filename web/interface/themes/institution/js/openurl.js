$(document).ready(function() {
    // assign action to the openUrlWindow link class
    $('a.openUrlWindow').click(function(){
        var params = extractParams($(this).attr('class'));
        var settings = params.window_settings;
        window.open($(this).attr('href'), 'openurl', settings);
        return false;
    });

    // assign action to the openUrlEmbed link class
    $('a.openUrlEmbed').click(function(){
        var params = extractParams($(this).attr('class'));
        var openUrl = $(this).children('span.openUrl:first').attr('title');
        $(this).hide();
        loadResolverLinks($('#openUrlEmbed'+params.openurl_id).show(), openUrl);
        return false;
    });
});

function loadResolverLinks($target, openUrl) {
    $target.addClass('ajax_availability');
    var url = path + '/AJAX/JSON?' + $.param({method:'getResolverLinks',openurl:openUrl});
    $.ajax({
        dataType: 'json',
        url: url,
        success: function(response) {
            if (response.status == 'OK') {
                $target.removeClass('ajax_availability')
                    .empty().append(response.data);
                link = $target.find('.openurl_more');
                link.one('click', function() {
                	$(this).addClass('iframe_loading');
                	div = $('<div/>');
                	iframe = $('<iframe/>');
                	iframe.attr('class', 'openurlIframe');
                	iframe.load(function() { $('.iframe_loading').removeClass('iframe_loading');});
                	iframe.attr('src', $(this).attr('href'));
                	div.append(iframe);
                	div.insertAfter($(this));
                	return false;
                });
            } else {
                $target.removeClass('ajax_availability').addClass('error')
                    .empty().append(response.data);
            }
        }
    });
}
