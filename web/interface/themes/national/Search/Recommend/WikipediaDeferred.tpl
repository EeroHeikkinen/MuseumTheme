{if $deferredWikipediaLookfor}
<div id="WikipediaDeferred" class="recommendation">
  <div class="content">
    <p>{translate text="Loading"}... <img src="{$path}/images/loading.gif" /></p>
    <script>
    var url = path + "/AJAX/Recommend?mod=Wikipedia&lookfor=" +
        "{$deferredWikipediaLookfor|escape:"url"|escape:"javascript"}&type=" +
        "{$deferredWikipediaSearchType|escape:"url"|escape:"javascript"}" + "&params=";

    $('#WikipediaDeferred').load(url);
    </script>
  </div>
</div>
{/if}
