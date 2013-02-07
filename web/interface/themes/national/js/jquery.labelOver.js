/**
* @author Remy Sharp
* @url http://remysharp.com/2007/03/19/a-few-more-jquery-plugins-crop-labelover-and-pluck/#labelOver
* Modifications to show on focus and hide on keypress by NDL
*/

jQuery.fn.labelOver = function(overClass) {
    return this.each(function(){
        var label = jQuery(this);
        var f = label.attr('for');
        if (f) {
            var input = jQuery('#' + f);
            
            this.hide = function() {
              label.css({ textIndent: -10000 })
            }
            
            this.show = function() {
              label.css({ textIndent: -10000 }) // modified: keep label hidden
              if (input.val() == '') label.css({ textIndent: 0 })
            }

            // handlers
            input.focus(this.show);   // modified: added line
            input.keydown(this.hide); // modified: changed 'focus' to 'keydown'
            input.blur(this.show);
            label.addClass(overClass).click(function(){ input.focus() });
            
            if (input.val() != '') this.hide(); 
        }
    })
}
