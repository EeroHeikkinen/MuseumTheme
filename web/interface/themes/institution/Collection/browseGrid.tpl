<!-- START of: Collection/browseGrid.tpl -->

{foreach from=$result item=item name=recordLoop}
  <div class="collectionBrowseEntry gridBrowse {if ($smarty.foreach.recordLoop.iteration % 2) == 0}alt {/if}">
    {if $item[1] > 0}
      <a href="{$path}/Collection/{$item[0]|urlencode}">
    {/if}
    <div class="collectionBrowseHeading">
      {$item[0]|truncate:103:"..."|escape:"html"}
    </div>
    <div class="collectionBrowseImg_Collection">
      <img src="{$path}/collectioncover.php?title={$item[0]|escape:url}&size=large" class="collcover" alt="{translate text='No Cover Image'}"/>
    </div>
    {if $item[1] > 0}
      </a>
      <div class="collectionBrowseCount">{$item[1]} {translate text="Items"}</div> 
    {/if}
    <div class="clearer"><!-- empty --></div>
  </div>
  {if $smarty.foreach.recordLoop.iteration is div by 4}
    <div class="clearer"></div>
  {/if}
{/foreach}

<!-- END of: Collection/browseGRID.tpl -->
