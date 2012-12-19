<!-- START of: Search/Recommend/TopFacets.tpl -->

{if $topFacetSet}
  {foreach from=$topFacetSet item=cluster key=title}
  <div {if $cluster.label == "Suggested Topics"}id="suggestedTopics" class="suggestedTopics span-3{if !$sidebarOnLeft} right last{/if}" {else}class="authorbox"{/if}>
    {* $cluster.label *}
    <strong>{translate text=$cluster.label}</strong><br />{* translate text="top_facet_suffix" *}
    {foreach from=$cluster.list item=thisFacet name="narrowLoop"}
      {if $smarty.foreach.narrowLoop.iteration == ($topFacetSettings.rows * $topFacetSettings.cols) + 1}
        <br class="clear"/>
        <a id="more{$title}" href="#" onclick="moreFacets('{$title}'); return false;">{translate text='more'}...</a>
        <div class="offscreen suggestedTopicsHidden" id="narrowGroupHidden_{$title}">
        
          {*
          <br/>
          <strong>{translate text="top_facet_additional_prefix"}{translate text=$cluster.label}</strong><br />{translate text="top_facet_suffix"}
          *}
          
      {/if}
      
      {*
      {if $smarty.foreach.narrowLoop.iteration % $topFacetSettings.cols == 1}
        <br/>
      {/if}
      *}
      
      {if $thisFacet.isApplied}
        {$thisFacet.value|escape} <img src="{$path}/images/silk/tick.png" alt="Selected"/>
      {else}
        <div class="facetWrapper"><a href="{$thisFacet.url|escape}">{$thisFacet.value|escape}</a> <span class="facetCount">({$thisFacet.count})</span></div>
      {/if}

      {if $smarty.foreach.narrowLoop.total > ($topFacetSettings.rows * $topFacetSettings.cols) && $smarty.foreach.narrowLoop.last}
          <br class="clear" />
          <a href="#" onclick="lessFacets('{$title}'); return false;">{translate text='less'}...</a>
        </div>
      {/if}
    {/foreach}
    <div class="clear"></div>
  </div>
  {/foreach}
{/if}

<!-- END of: Search/Recomment/TopFacets.tpl -->
