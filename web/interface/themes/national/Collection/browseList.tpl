<!-- START of: Collection/browseList.tpl -->

{foreach from=$result item=item name=recordLoop}
  <div class="collectionBrowseEntry listBrowse {if ($smarty.foreach.recordLoop.iteration % 2) == 0}alt {/if}">
    <div class="collectionBrowseHeading">
      {if $item[1] > 0}
        <a href="{$path}/Collection/{$item[0]|urlencode}">{$item[0]|escape:"html"}</a>
      {else}
        {$item[0]|escape:"html"}
      {/if}
    </div>
    {if $item[1] > 0}<div class="collectionBrowseCount">{$item[1]} {translate text="Items"}</div> {/if}
    <div class="clearer"><!-- empty --></div>
  </div>
{/foreach}

<!-- END of: Collection/browseList.tpl -->
