/* List of all the CSS3 elements that need PIE to show correcly in IE
   For troubleshooting see: http://css3pie.com/documentation/known-issues/
*/

/* TODO: Some selectors might be unnecessary, some others still missing.
         All queries should be combined to one multiple selector query.
*/

$(document).ready(function() {
    if (window.PIE) {
/*** border-radius ***/
        $('.openurlIframe').each(function() {
          PIE.attach(this);
        });
        $('.feedbackButton').each(function() {
          PIE.attach(this);
        });
/** The frame around searchbox from which it is quick to see if PIE works **/
        $('.searchFormWrapper').each(function() {
          PIE.attach(this);
        });
        $('.headerInfoBox').each(function() {
          PIE.attach(this);
        });
        $('.dropdown dd ul, ul.ui-menu.ui-widget').each(function() {
          PIE.attach(this);
        });
        $('.dropdown dd ul li:first-child a, ul.ui-menu.ui-widget li:first-child a').each(function() {
          PIE.attach(this);
        });
        $('.dropdown dd ul li:last-child a, ul.ui-menu.ui-widget li:last-child a').each(function() {
          PIE.attach(this);
        });
        $('div.ui-dialog').each(function() {
          PIE.attach(this);
        });
        $('div.ui-dialog .ui-widget-header .ui-dialog-titlebar-close').each(function() {
          PIE.attach(this);
        });
        $('.searchContextHelp').each(function() {
          PIE.attach(this);
        });
        $('.dropdown dt a').each(function() {
          PIE.attach(this);
        });
        $('.dateVisClear a').each(function() {
          PIE.attach(this);
        });
        $('.dateVisHandle .dateVisHelp .infoIndicator').each(function() {
          PIE.attach(this);
        });
        $('.paginationMove').each(function() {
          PIE.attach(this);
        });
        $('#dyntabnav .ui-tabs-nav').each(function() {
          PIE.attach(this);
        });
        $('.browseNav').each(function() {
          PIE.attach(this);
        });
        $('.sysInfo').each(function() {
          PIE.attach(this);
        });
        $('.button').each(function() {
          PIE.attach(this);
        });
        $('.roundButton').each(function() {
          PIE.attach(this);
        });
        $('.searchFormWrapper').each(function() {
          PIE.attach(this);
        });
/*** box-shadow ***/
        $('.dateVis').each(function() {
          PIE.attach(this);
        });
        $('.headerInfoBox.visible').each(function() {
          PIE.attach(this);
        });
/*** :before, :after ***/
        $('.searchContextHelp:before').each(function() {
          PIE.attach(this);
        });
        $('#moreInfoToggle:before, #moreInfoToggle:after').each(function() {
          PIE.attach(this);
        });
        $('.searchContextHelp:after').each(function() {
          PIE.attach(this);
        });
        $('#moreInfoToggle:after').each(function() {
          PIE.attach(this);
        });
        $('#moreInfoToggle.active:after').each(function() {
          PIE.attach(this);
        });
        $('.clearfix:after, .container:after').each(function() {
          PIE.attach(this);
        });
/*** -child ***/
        $('#headerMenu li a:hover span:first-child, #headerMenu li a span:first-child').each(function() {
          PIE.attach(this);
        });
        $('ul.subMenu li:first-child, .home-section.third li span:first-child').each(function() {
          PIE.attach(this);
        });
        $('#header .lang li:first-child').each(function() {
          PIE.attach(this);
        });
        $('.dropdown dd ul li:first-child a').each(function() {
          PIE.attach(this);
        });
        $('.finesList table tr td:first-child').each(function() {
          PIE.attach(this);
        });
        $('.module-Content #main .contentSection:first-child h2:first-child').each(function() {
          PIE.attach(this);
        });
        $('.module-Content #main .contentSection:first-child h2:first-child').each(function() {
          PIE.attach(this);
        });
        $('.searchtools ul li:first-child').each(function() {
          PIE.attach(this);
        });
        $('.groupSearchHolder .advRow:first-child').each(function() {
          PIE.attach(this);
        });
        $('.groupSearchHolder .advRow.last:first-child').each(function() {
          PIE.attach(this);
        });
        $('.browseNav li:first-child a').each(function() {
          PIE.attach(this);
        });
        $('tr:nth-child(even) td.gridCell').each(function() {
          PIE.attach(this);
        });
        $('tr:nth-child(even) td.gridMouseOver').each(function() {
          PIE.attach(this);
        });
/*** others ***/
        $('#header .content').each(function() {
          PIE.attach(this);
        });
    }
});
