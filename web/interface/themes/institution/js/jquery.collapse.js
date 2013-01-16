// Field collapsing plugin for jQuery
// by Ere Maijala, The National Library of Finland, 2012
// Free to modify and redistribute with credit.
// Modified at The National Library of Finland

(function($) {

  var trailing_whitespace = true;

  $.fn.collapse = function(options) {

    var opts = $.extend({}, $.fn.collapse.defaults, options);

    $(this).each(function() {

      var lineHeight = getStyle(this, "line-height");
      if (!lineHeight)
        return;
      lineHeight = lineHeight.replace('px', '');
      lineHeight = lineHeight.replace('pt', '');
      
      var node = $(this);

      // We actually let three rows pass, as our "more" link would take one anyway...
      // Then we allow for padding of almost a full row
      if (node.height() / lineHeight <= opts.maxRows + 1.7)
        return;
      
      node.css('height', (opts.maxRows + 1) * lineHeight + 'px').css('overflow', 'hidden').css('position', 'relative');

      $('<div class="moreLink"><a href="">' + opts.more + '</a></div>').click(function() {
        node.css('height', '');
        node.find('.moreLink').hide();
        node.find('.lessLink').show();
        return false;
      }).appendTo(node);
      if (opts.less) {
        $('<div class="lessLink" style="display: none"><a href="">' + opts.less + '</a></div>').click(function() {
          node.css('height', (opts.maxRows + 1) * lineHeight + 'px');
          node.find('.lessLink').hide();
          node.find('.moreLink').show();
          return false;
        }).appendTo(node);
      }
    });
  };

  $.fn.collapse.defaults = {
    maxRows: 3,
    more: 'more',
    less: 'less', // Use null or false to omit
  };

  function getStyle(el, cssprop) {
    if (el.currentStyle)
      return el.currentStyle[cssprop]
    else if (document.defaultView && document.defaultView.getComputedStyle)
      return document.defaultView.getComputedStyle(el).getPropertyValue(cssprop);
    else
      return el.style[cssprop]
  }  

})(jQuery);
