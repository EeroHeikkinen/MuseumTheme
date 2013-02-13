<!-- START of: MyResearch/list.tpl -->

{js filename="bulk_actions.js"}
{js filename="init_fancybox.js"}
{if $bookBag}
<script type="text/javascript">
vufindString.bulk_noitems_advice = "{translate text="bulk_noitems_advice"}";
vufindString.confirmEmpty = "{translate text="bookbag_confirm_empty"}";
vufindString.viewBookBag = "{translate text="View Book Bag"}";
vufindString.addBookBag = "{translate text="Add to Book Bag"}";
vufindString.removeBookBag = "{translate text="Remove from Book Bag"}";
vufindString.itemsAddBag = "{translate text="items_added_to_bookbag"}";
vufindString.itemsInBag = "{translate text="items_already_in_bookbag"}";
vufindString.bookbagMax = "{$bookBag->getMaxSize()}";
vufindString.bookbagFull = "{translate text="bookbag_full_msg"}";
vufindString.bookbagStatusFull = "{translate text="bookbag_full"}";
</script>
{/if}

{include file="MyResearch/menu.tpl"}

<div class="myResearch">
  <div class="content">
  <div class="resultHead">
  {if $errorMsg || $infoMsg}
    <div class="messages">
    {if $errorMsg}<p class="error">{$errorMsg|translate}</p>{/if}
    {if $infoMsg}<p class="info">{$infoMsg|translate}{if $showExport} <a class="save" target="_new" href="{$showExport|escape}">{translate text="export_save"}</a>{/if}</p>{/if}
    </div>
  {/if}
  </div>
  <div id="sidebarFavoritesLists">
  {if $listList}
    <span class="hefty">{translate text='Your Lists'}</span>
    
    <ul>
      {foreach from=$listList item=listItem}
      <li>
        {if $list && $listItem->id == $list->id}
        <div class="selected">{$listItem->title|escape:"html"}&nbsp;<span class="favoritesCount">({$listItem->cnt})</span></div>
        {if $tagList}
            <div class="tags">
              <div>{if $list}{translate text='Tags'}{else}{translate text='Your Tags'}{/if}</div>
              {if $tags}
              <ul>
                {foreach from=$tags item=tag}
                <li>{translate text='Tag'}: {$tag|escape:"html"}
                  <a href="{$url}/MyResearch/{if $list}MyList/{$list->id}{else}Favorites{/if}?{foreach from=$tags item=mytag}{if $tag != $mytag}tag[]={$mytag|escape:"url"}&amp;{/if}{/foreach}">X</a>
                </li>
                {/foreach}
              </ul>
              {/if}

              <ul>
              {foreach from=$tagList item=tag}
                <li class="button buttonTurquoise"><a href="{$url}/MyResearch/{if $list}MyList/{$list->id}{else}Favorites{/if}?tag[]={$tag->tag|escape:"url"}{foreach from=$tags item=mytag}&amp;tag[]={$mytag|escape:"url"}{/foreach}">{$tag->tag|escape:"html"}</a> ({$tag->cnt})</li>
                {/foreach}
              </ul>
            </div>
        {/if}

          {if $listEditAllowed}
        
        <div class="editList">
          <a class="icon edit" href="{$url}/MyResearch/EditList/{$list->id|escape:"url"}">{translate text="edit_list"}</a>
          <a class="icon delete" href="{$url}/Cart/Home?listID={$list->id|escape}&amp;listName={$list->title|escape}&amp;origin=Favorites&amp;listFunc=editList&amp;deleteList=true">{translate text="delete_list"}</a>
        </div>
          {/if}
        {else}
        <div class="listItem"><a href="{$url}/MyResearch/MyList/{$listItem->id|escape:"url"}">{$listItem->title|escape:"html"}</a>&nbsp;<span class="favoritesCount">({$listItem->cnt})</span></div>
        {/if}
      </li>
     {/foreach}
    </ul>
    <a href="{$url}/MyResearch/ListEdit" class="listAdd add" id="listAdd" title="{translate text='Create a List'}">{translate text='Create a List'}</a>
  {/if}
  </div>

  <div class="favoritesList last">
  {if $list && $list->id}
    <span class="hefty myResearchTitle">{$list->title|escape:"html"}</span><br/>
    {if $list->description}<p class="favoritesDescription">{$list->description|escape}</p>{/if}
  {else}
    <span class="hefty">{translate text='Your Favorites'}</span><br/>
  {/if}
  {if $resourceList}
    {include file="Search/paging.tpl" position="Top"}
    
    <div class="floatright small resultOptions">
      <form action="{$path}/Search/SortResults" method="post">
        <label for="sort_options_1">{translate text='Sort'}</label>
        <select id="sort_options_1" name="sort" class="jumpMenu">
        {foreach from=$sortList item=sortData key=sortLabel}
          <option value="{$sortData.sortUrl|escape}"{if $sortData.selected} selected="selected"{/if}>{translate text=$sortData.desc}</option>
        {/foreach}
        </select>
        <noscript><input type="submit" value="{translate text="Set"}" /></noscript>
      </form>
    </div>
    
    <div class="clear"></div>

  <form method="post" name="bulkActionForm" action="{$url}/Cart/Home">
    <input type="hidden" name="origin" value="Favorites" />
    <input type="hidden" name="followup" value="true" />
    <input type="hidden" name="followupModule" value="MyResearch" />
    <input type="hidden" name="followupAction" value="{if $list}MyList/{$list->id}{else}Favorites{/if}" />
    {if $list && $list->id}
    <input type="hidden" name="listID" value="{$list->id|escape}" />
    <input type="hidden" name="listName" value="{$list->title|escape}" />
    {/if}

    <div class="bulkActionButtons">
      <div class="allCheckboxBackground"><input type="checkbox" class="selectAllCheckboxes" name="selectAll" id="addFormCheckboxSelectAll" /></div>
      <div class="floatright"><strong>{translate text="with_selected"}: </strong>
      {if $bookBag}
        <a id="updateCart" class="bookbagAdd offscreen" href="">{translate text='Add to Book Bag'}</a>
        <noscript>
          <input type="submit"  class="button bookbagAdd" name="add" value="{translate text='Add to Book Bag'}"/>
        </noscript>
      {/if}
      {if $listList}
        <select name="move">
          <option value="">{translate text="move_to_list"}</option>
          {foreach from=$listList item=listItem}
            {if !$list || $listItem->id != $list->id}
          <option value="{$listItem->id|escape}">{$listItem->title|escape:"html"}</option>
            {/if}
          {/foreach}
        </select>
        <select name="copy">
          <option value="">{translate text="copy_to_list"}</option>
          {foreach from=$listList item=listItem}
            {if !$list || $listItem->id != $list->id}
          <option value="{$listItem->id|escape}">{$listItem->title|escape:"html"}</option>
            {/if}
          {/foreach}
        </select>
      {/if}  
        <input type="submit" class="button" name="email" value="{translate text='Email this'}" title="{translate text='Email this'}"/>
        {if is_array($exportOptions) && count($exportOptions) > 0}
        <input type="submit" class="button" name="export" value="{translate text='export_expanding'}" title="{translate text='export_expanding'}"/>
        {/if}
        {if $listEditAllowed}<input id="delete_list_items_{if $list}{$list->id|escape}{/if}" type="submit" class="button" name="delete" value="{translate text='Delete'}" title="{translate text='delete_selected'}"/>{/if}
      </div>
      <div class="clear"></div>
    </div> 

    <ul class="recordSet">
    {foreach from=$resourceList item=resource name="recordLoop"}
      <li class="result{if ($smarty.foreach.recordLoop.iteration % 2) == 0} alt{/if}">
        <span class="recordNumber">{$recordStart+$smarty.foreach.recordLoop.iteration-1}</span>
        {* This is raw HTML -- do not escape it: *}
        {$resource}
      </li>
    {/foreach}
    </ul>
    
    <div class="clear"></div>
  </form>
  
    {include file="Search/paging.tpl"}
  {else}
    <div class="noContentMessage">{translate text='You do not have any saved resources'}</div>
  </div>
  {/if}
  </div>
</div>

<div class="clear"></div>


<!-- END of: MyResearch/list.tpl -->
