{if $openUrlAutoCheck}
{js filename="jquery.inview.min.js"}
{literal}
<script type="text/javascript">
$(document).ready(function() {
  $('a.openUrlEmbed').one('inview', function() { $(this).trigger('click'); });
});
{/literal}
</script>
{/if}
