function checkRSI(source, peerReviewedOnly, texts) {
    var id = $.map($('.recordId'), function(i) {
        return $(i).attr('id').substr('record'.length);
    });
    if (id.length) {
    	// set the spinner going
        $('.rsi').addClass('ajax_fulltext_availability');

        url = path + '/AJAX/JSON_RSI?method=getRSIStatuses&source=' + source;
    	$.getJSON(url, {id:id}, function(response) {
	        $('.rsi').removeClass('ajax_fulltext_availability');
		    if (response.status != 'OK') {
		        $('.rsi').text("RSI status check failed.");
		        return;
		    }
            $.each(response.data, function(i, result) {
                var safeId = jqEscape(result.id);
                
                if (peerReviewedOnly) {
                	if (result.status == 'peerReviewedFullText') {
                        var span = $('<span/>');
                        span.addClass('rsi');
                        span.addClass('peerReviewed');
                        span.html(texts['peerReviewed']);
                        $('#record' + safeId).find('.rsi').append(span);
                	}
                	return;
                }
                
                if (result.status == 'fullText' || result.status == 'peerReviewedFullText') {
                    var a = $('<a/>');
                    a.attr('href', $('#record' + safeId).find('.openUrl').parent().attr('href') + '&__service_type=getFullTxt');
                    a.addClass('rsi');
                    if (result.status == 'peerReviewedFullText') {
                    	a.addClass('peerReviewed');
                    }
                    a.html(texts[result.status]);
                    $('#record' + safeId).find('.rsi').append(a);
                } else if (result.status == 'noInformation') {
                    var a = $('#record' + safeId).find('.openUrl').parent();
                    var children = a.children();
                    a.html(texts['maybeFullText']).append(children);
                } else {
                    var span = $('<span/>');
                    span.addClass('rsi');
                    span.html(texts[result.status]);
                    $('#record' + safeId).find('.rsi').append(span);
                }
                if (result.status != 'noInformation') {
                    var a = $('#record' + safeId).find('.openUrl').parent();
                    var children = a.children();
                    a.html(texts['moreInformation']).append(children);
                }
            });
        }).error(function() {
	        $('.rsi').removeClass('ajax_fulltext_availability');
	        $('.rsi').text("RSI status check failed.");
        });              
    }
}
