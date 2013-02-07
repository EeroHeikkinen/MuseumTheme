<div class="record">
  <a href="{$url}/EBSCO/Record?id={$id|escape:"url"}" class="backtosearch">&laquo; {translate text="Back to Record"}</a>

  {if $pageTitle}<h1>{$pageTitle}</h1>{/if}
  {include file="EBSCO/$subTemplate"}
</div>
