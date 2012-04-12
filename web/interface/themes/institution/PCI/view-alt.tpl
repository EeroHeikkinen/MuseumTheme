<div class="record">
  <a href="{$url}/PCI/Record?id={$id|escape:"url"}" class="backtosearch">&laquo; {translate text="Back to Record"}</a>

  {if $pageTitle}<h1>{$pageTitle}</h1>{/if}
  {include file="PCI/$subTemplate"}
</div>
