{literal}
<script type="text/javascript" id="test">
$(document).ready(function() {
    // poll every 60 seconds
    var refreshTime = 60000;
    window.setInterval(function() {
        $.get("{/literal}{$url}{literal}/AJAX/JSON_KeepAlive",
               {method: 'keepAlive'});
    }, refreshTime);
});
</script>
{/literal}