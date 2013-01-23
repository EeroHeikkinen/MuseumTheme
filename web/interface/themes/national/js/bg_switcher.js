// Load random header background and related info text
initBgSwitcher('/Content/headertexts','.backgroundContainer');

function initBgSwitcher(source, target) {
    
    var randomNumber, content;
    var fadeSpeed = 0;
    
    // If cookies not set
    if ($.cookie('bgNumber') === null || $.cookie('infoText') === null) {

        $.get(path+source, function(data) {

            // Get related info text from source
            texts = $(data).find('#headerTexts > div');
            if (texts.length > 0) {

                // Get random number from 0 to the number of elements found - 1
                randomNumber = Math.floor((Math.random()*texts.length)) + 1;
                content = texts.eq(randomNumber - 1).html();

                $.cookie('bgNumber', randomNumber);
                $.cookie('infoText', content);
                hide = true;
                fadeSpeed = 1000;

            }
        });
    }
    
    else {
        randomNumber = $.cookie('bgNumber');
        content = $.cookie('infoText');
    }
    
    // Add background and related content
    $('#header').ready(function() {
        // Set target background image
        bgurl = "url('"+path+"/interface/themes/national/images/header_background_"
            + randomNumber+".jpg')";
        $(target).hide().css("background-image", bgurl).fadeIn(fadeSpeed);

        // Set infotext
        $('#header .infoBoxText').html(content); 
    });

}