<!-- START of: RecordDrivers/Index/listentry.tpl -->

<div class="listentry recordId" id="record{$listId|escape}">
  <div class="resultCheckbox">
    <label for="checkbox_{$listId|regex_replace:'/[^a-z0-9]/':''|escape}" class="offscreen">{translate text="Select"}: {$listTitle|escape}</label>
    <input id="checkbox_{$listId|regex_replace:'/[^a-z0-9]/':''|escape}" type="checkbox" name="ids[]" value="{$listId|escape}" class="checkbox_ui"/>
    <input type="hidden" name="idsAll[]" value="{$listId|escape}" />
  </div>
  
  {assign var=img_count value=$summImages|@count}
  <div class="coverDiv">
    <div class="resultNoImage"><p>{translate text='No image'}</p></div>
    {if $img_count > 0}
        <div class="resultImage"><a href="{$path}/thumbnail.php?id={$listId|escape:"url"}&index=0&size=large" rel="{$listId|escape:"url"}" onclick="launchFancybox(this); return false;"><img id="thumbnail_{$listId|escape:"url"}" src="{$listThumb|escape}" class="summcover" alt="{translate text='Cover Image'}"/></a></div>
    {else}
        <div class="resultImage"><img src="{$path}/images/NoCover2.gif" width="62" height="62" alt="{translate text='No Cover Image'}"/></div>
    {/if}

{* Multiple images *}
{if $img_count > 1}
    <div class="imagelinks">
{foreach from=$summImages item=desc name=imgLoop}
        <a href="{$path}/thumbnail.php?id={$listId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large" class="title fancybox fancybox.image" onmouseover="document.getElementById('thumbnail_{$listId|escape:"url"}').src='{$path}/thumbnail.php?id={$listId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=small'; document.getElementById('thumbnail_link_{$listId|escape:"url"}').href='{$path}/thumbnail.php?id={$listId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large'; return false;" rel="{$listId}">
      {if $desc}{$desc|escape}{else}{$smarty.foreach.imgLoop.iteration + 1}{/if}
        </a>
{/foreach}
    </div>
{/if}
  </div>
  {*
  <div class="coverDiv">
    {if $listThumb}
      <img src="{$listThumb|escape}" class="summcover" alt="{translate text='Cover Image'}"/>
    {else}
      <img src="{$path}/bookcover.php" class="summcover" alt="{translate text='No Cover Image'}"/>
    {/if}
  </div> *}
  <div class="resultColumn2">
      <a href="{$url}/Record/{$listId|escape:"url"}" class="title">{$listTitle|escape}</a><br/>
      {if $listAuthor}
        {translate text='by'}: <a href="{$url}/Search/Results?lookfor={$listAuthor|escape:"url"}&amp;type=Author">{$listAuthor|escape}</a><br/>
      {/if}
      {if $listTags}
        <strong>{translate text='Your Tags'}:</strong>
        {foreach from=$listTags item=tag name=tagLoop}
          <a href="{$url}/Search/Results?tag={$tag->tag|escape:"url"}">{$tag->tag|escape:"html"}</a>{if !$smarty.foreach.tagLoop.last},{/if}
        {/foreach}
        <br/>
      {/if}
      {if $listNotes}
        <strong>{translate text='Notes'}:</strong>
        {if count($listNotes) > 1}<br/>{/if}
        {foreach from=$listNotes item=note}
          {$note|escape:"html"}<br/>
        {/foreach}
      {/if}

      {assign var=mainFormat value=$listFormats.0} 
      {assign var=displayFormat value=$listFormats|@end} 
      <span class="iconlabel format{$mainFormat|lower|regex_replace:"/[^a-z0-9]/":""} format{$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$displayFormat prefix='format_'}</span>
  </div>

  {if $listEditAllowed}
  <div class="last floatright editItem">
      <a href="{$url}/MyResearch/Edit?id={$listId|escape:"url"}{if !is_null($listSelected)}&amp;list_id={$listSelected|escape:"url"}{/if}" class="icon edit tool"></a>
      {* Use a different delete URL if we're removing from a specific list or the overall favorites: *}
      <a
      {if is_null($listSelected)}
        href="{$url}/MyResearch/Favorites?delete={$listId|escape:"url"}"
      {else}
        href="{$url}/MyResearch/MyList/{$listSelected|escape:"url"}?delete={$listId|escape:"url"}"
      {/if}
      class="icon delete tool" onclick="return confirm('{translate text='confirm_delete'}');"></a>
  </div>
  {/if}

  <div class="clear"></div>
</div>

<!-- END of: RecordDrivers/Index/listentry.tpl -->
