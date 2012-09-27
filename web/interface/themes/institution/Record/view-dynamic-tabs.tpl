<!-- START of: Record/view-dynamic-tabs.tpl -->

{literal}
<script type="text/javascript">
$(document).ready(function() {
    $('#dyntabnav').append('<span id="dyntabnav_spinner" class="ajax_availability"></span>');
    tabNo = $('#dyntabnav li.active').prevAll().size();
    var $tabs = $('#dyntabnav').tabs({
        spinner: "",
        selected: tabNo,
        select: function(event, ui) {
            $('#dyntabnav').append('<span id="dyntabnav_spinner" class="ajax_availability"></span>');
        },
        load: function(event, ui) {
            $('#dyntabnav_spinner').remove();
        }
    });
});
</script>
{/literal}

<!-- END of: Record/view-dynamic-tabs.tpl -->
