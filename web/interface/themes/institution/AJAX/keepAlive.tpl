{literal}
<script type="text/javascript">
$(document).ready(function() {
    // poll every 60 seconds
    var refreshTime = 60000;
    window.setInterval(function() {
        $.getJSON("{/literal}{$url}{literal}/AJAX/JSON_KeepAlive",
               {method: 'keepAlive'});
    }, refreshTime);
});
</script>
{/literal}