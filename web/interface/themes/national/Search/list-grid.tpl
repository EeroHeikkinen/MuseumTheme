<!-- START of: Search/list-grid.tpl -->

{js filename="check_item_statuses.js"}
{js filename="check_save_statuses.js"}
{js filename="jquery.cookie.js"}
{js filename="openurl.js"}
{if $showPreviews}
{js filename="preview.js"}
{/if}
{if $metalibEnabled}
{js filename="metalib_links.js"}
{/if}

{include file="Search/rsi.tpl"}
{include file="Search/openurl_autocheck.tpl"}

<form method="post" name="addForm" action="{$url}/Cart/Home">
<table style="border-bottom:1px solid #eee;">
  <tr>
  {foreach from=$recordSet item=record name="recordLoop"}
   <td class="gridCell gridCellHover">
       <span class="recordNumber">{$recordStart+$smarty.foreach.recordLoop.iteration-1}</span>
       {* This is raw HTML -- do not escape it: *}{$record}
   </td>
   {if (($smarty.foreach.recordLoop.iteration % 4) == 0) && (!$smarty.foreach.recordLoop.last)}</tr><tr>{/if}
  {/foreach}
  </tr>
</table>
</form>

<!-- END of: Search/list-grid.tpl -->
