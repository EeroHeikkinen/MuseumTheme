<!-- START of: Record/view-dynamic-tabs.tpl -->

{literal}
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    $('#dyntabnav').append('<span id="dyntabnav_spinner" class="ajax_availability"></span>');
    tabNo = $('#dyntabnav li.active').prevAll().size();
    if (window.location.hash) {
      var link = $(window.location.hash);
      if (link) {
        tabNo = link.parent().prevAll().size();
      }
    }
    var $tabs = $('#dyntabnav').tabs({
        spinner: "",
        selected: tabNo,
        select: function(event, ui) {
            $('#dyntabnav').append('<span id="dyntabnav_spinner" class="ajax_availability"></span>');
            window.location.hash = '#' + ui.tab.id;  
            $('a.prevRecord, a.nextRecord').each(function() {
              this.href = this.href.replace(/#.*/, "") + window.location.hash; 
            });
        },
        load: function(event, ui) {
            $('#dyntabnav_spinner').remove();
        }
    });
    $('a.prevRecord, a.nextRecord').each(function() {
      this.href = this.href.replace(/#.*/, "") + window.location.hash; 
    });
});
//]]>
</script>
{/literal}

<!-- END of: Record/view-dynamic-tabs.tpl -->
