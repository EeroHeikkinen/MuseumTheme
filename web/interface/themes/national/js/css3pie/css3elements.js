/* List of all the CSS3 elements that need PIE to show correcly in IE */

$(document).ready(function() {
    if (window.PIE) {
        $('.searchFormWrapper').each(function() {
          PIE.attach(this);
        });
    }
});
