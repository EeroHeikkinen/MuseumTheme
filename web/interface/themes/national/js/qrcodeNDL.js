var URL = location.href;

$('#qrcode').qrcode({
    render  : "div",
    width   : 120,
    height  : 120,
    text    : URL
});

/* Show the URL of the QRCode.
 * Later on, this should be changed to something more descriptive with proper translations.
 */
$('#qrcode').append('<br/><span class="small">' + URL + '</span>');

// Below is the code for icon overlay on the QRCode. Not needed now, and the overlay.php needs work to strip the Google API stuff off and a proper location

/*!
 * jQuery uQR library
 * http://www.userdot.net/#!/jquery
 *
 * Copyright 2011, UserDot www.userdot.net
 * Licensed under the GPL Version 3 license.
 * Version 1.0.0
 *
 */
/*
(function ($) {
    $.fn.extend({
        uQr: function (x) {
            var c = {
                baseUrl: 'http://tester21-kktest.lib.helsinki.fi/php/overlay.php?overlay=qr_overlay.png',
                size: 200,
                create: false,
                number: null,
                email: null,
                subject: null,
                latitude: null,
                longitude: null,
                address: null,
                name: null,
                url: null,
                alt: 'QR code',
                note: null,
                encoding: 'UTF-8',
                type: 'text',
                text: 'Welcome to UserDot'
            };
            var b = $.extend(c, x);
            return this.each(function () {
                var d = $(this);
                var url = b.baseUrl + '&text=' + b.text + '&url=' + b.url;
                if (b.create) {

                    d.append('<img src="' + url + '" alt="' + b.alt + '" />');
                }
                else {
                    d.attr('src', url);
                }
            });
        }
    })
})(jQuery)


$(document).ready(function(){
   $('#qrcode').uQr({
      create : true,
      type : 'text',
      text : encodeURIComponent(location.href)
   });


});
*/
