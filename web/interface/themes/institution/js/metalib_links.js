$(document).ready(function() {
    checkMetaLibLinks();
});

function checkMetaLibLinks() {
    var id = $.map($('.recordId'), function(i) {
        var id = $(i).attr('id').substr('record'.length);
        if (id.substr(0, 8) == 'metalib_') {
            return id;
        }
        return null;
    });
    if (id.length) {
    	// set the spinner going
        $('.metalib_link').addClass('ajax_fulltext_availability');

        url = path + '/AJAX/JSON_MetaLib?method=getSearchLinkStatuses';
    	$.getJSON(url, {id:id}, function(response) {
	        $('.metalib_link').removeClass('ajax_fulltext_availability');
		    if (response.status != 'OK') {
		        $('.metalib_link').text("MetaLib link check failed.");
		        return;
		    }
            $.each(response.data, function(i, result) {
                var safeId = jqEscape(result.id);
                
                if (result.status == 'allowed') {
                	$('#metalib_link_' + safeId).show();
                } else {
                	$('#metalib_link_na_' + safeId).show();
                }
            });
        }).error(function() {
	        $('.metalib_link').removeClass('ajax_fulltext_availability');
	        $('.metalib_link').text("MetaLib link check failed.");
        });              
    }
}
