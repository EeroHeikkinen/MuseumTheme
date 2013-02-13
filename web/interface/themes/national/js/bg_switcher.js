// Load random header background and related info text

function initBgSwitcher(source, target, lang) {
    bgChanged = true;
    var randomNumber, content;
    
    // If cookies not set
    if ($.cookie('bgNumber') === null || $.cookie('infoText') === null || $.cookie('userLang') === null || $.cookie('userLang') != lang) {
        $.get(path+source, function(data) {
            // Get related info text from source
            texts = $(data).find('#headerTexts > div');
            console.log('t: '+texts);
            if (texts.length > 0) {

                // Get random number from 0 to the number of elements found - 1
                randomNumber = Math.floor((Math.random()*texts.length)) + 1;
                content = texts.eq(randomNumber - 1).html();
                
                var expire = new Date();
                var minutes = 30;
                expire.setTime(expire.getTime() + (minutes * 60 * 1000));
                $.cookie('bgNumber', randomNumber, { expires: expire });
                $.cookie('infoText', content, { expires: expire });
                $.cookie('userLang', lang, { expires: expire });
                hide = true;
                fadeSpeed = 1000;

                performBgSwitch(target, randomNumber, content);
            }
        });
    }
    
    else {
        randomNumber = $.cookie('bgNumber');
        content = $.cookie('infoText');
        
        performBgSwitch(target, randomNumber, content);
    }


}

// Change background and info text
function performBgSwitch(target, randomNumber, content) {
    var fadeSpeed = 0;
    bgurl = "url('"+path+"/interface/themes/national/images/header_background_"
        + randomNumber+".jpg')";
    $(target).hide().css("background-image", bgurl).fadeIn(fadeSpeed);

    // Set infotext
    $('#header .infoBoxText').html(content); 
}