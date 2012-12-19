{if $facetCloudSet}
  <div class="sidegroup">
    <h4>{translate text="In this collection"}</h4>
      {foreach from=$facetCloudSet item=facets}
        {assign var='itemCount' value=0}
        <dl class="narrowList navmenu">
          <dt>{translate text=$facets.label}</dt>
          {foreach from=$facets.list item=facetItem name="itemsLoop"}{assign var='itemCount' value=$itemCount+1}{if $itemCount < $cloudLimit}{if $itemCount != 1}, {/if}<a href="{$facetItem.url}">{$facetItem.value}</a> {$facetItem.count}{else}{translate text=", and more..."}{/if}{/foreach}
        </dl><br/>
      {/foreach}
  </div>
{/if}
