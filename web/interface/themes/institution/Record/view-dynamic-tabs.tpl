{literal}
<script type="text/javascript">
$('#dyntabnav').append('<span id="dyntabnav_spinner" class="ajax_availability"></span>');
var $tabs = $('#dyntabnav').tabs({
    spinner: "",
    select: function(event, ui) {
        $('#dyntabnav').append('<span id="dyntabnav_spinner" class="ajax_availability"></span>');
    },
    load: function(event, ui) {
        $('#dyntabnav_spinner').remove();
    }
});
</script>
{/literal}