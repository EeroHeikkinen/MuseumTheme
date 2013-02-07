<!-- START of: MyResearch/view-alt.tpl -->

<div class="record">
  {if !empty($recordId)}
    <a href="{$url}/Record/{$recordId|escape:"url"}/Home" class="backtosearch">&laquo; {translate text="Back to Record"}</a>
  {/if}

  {if $pageTitle}<h1><span class="content">{$pageTitle}</span></h1>{/if}
  <div class="content">
  {include file="MyResearch/$subTemplate"}
  </div>
</div>

<!-- END of: MyResearch/view-alt.tpl -->
