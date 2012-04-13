<!-- START of: Record/view.tpl -->

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
{assign var=bookBagItems value=$bookBag->getItems()}
{/if}
{if isset($syndetics_plus_js)}
<script src="{$syndetics_plus_js}" type="text/javascript"></script>
{/if}
{if !empty($addThis)}
<!-- <script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js?pub={$addThis|escape:"url"}"></script> -->
{/if}

{js filename="record.js"}
{js filename="check_save_statuses.js"}
{if $showPreviews}
  {js filename="preview.js"}
{/if}
{if $coreOpenURL || $holdingsOpenURL}
  {js filename="openurl.js"}
{/if}


{* <div class="span-10{if $sidebarOnLeft} push-5 last{/if}"> *}

<div class="record recordId" id="record{$id|escape}">
	{if $errorMsg || $infoMsg}
		<div class="messages">
		{if $errorMsg}<div class="error">{$errorMsg|translate}</div>{/if}
		{if $infoMsg}<div class="info">{$infoMsg|translate}</div>{/if}
		</div>
	{/if}
  {if $lastsearch}
	<div class="backToResults">
		<a href="{$lastsearch|escape}#record{$id|escape:"url"}">&laquo; {translate text="Back to Search Results"}</a>
	</div>
  {/if}
  {if $previousRecord || $nextRecord}
	<div class="resultscroller">
		{if $previousRecord}<a href="{$url}/Record/{$previousRecord}" class="prevRecord">&laquo; {translate text="Prev"}</a>
		{else}<span class="prevRecord inactive">&laquo; {translate text="Prev"}</span>{/if}
		{$currentRecordPosition} / {$resultTotal}
		{* #{$currentRecordPosition} {translate text='of'} {$resultTotal} *}
		{if $nextRecord}<a href="{$url}/Record/{$nextRecord}" class="nextRecord">{translate text="Next"} &raquo;</a>
		{else}<span class="nextRecord inactive">{translate text="Next"} &raquo;</span>{/if}
	</div>
	{/if}
<div class="clear"></div>
</div>

<div class="span-3">

  {* Display Cover Image *}
  <div class="coverImages">
  {if $coreThumbMedium}
      {if $coreThumbLarge}<a id="thumbnail_link" href="{$coreThumbLarge|escape}" class="span-3">{/if}
        <img id="thumbnail" alt="{translate text='Cover Image'}" class="recordcover" src="{$coreThumbMedium|escape}">
      {if $coreThumbLarge}</a>{/if}
      <div class="clear"></div>
      {assign var=img_count value=$coreImages|@count}
      {if $img_count > 1}
        <div class="coverImageLinks">
      {foreach from=$coreImages item=desc name=imgLoop}
          <a href="{$path}/thumbnail.php?id={$id|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large" class="title" onclick="document.getElementById('thumbnail').src='{$path}/thumbnail.php?id={$id|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=medium'; document.getElementById('thumbnail_link').href='{$path}/thumbnail.php?id={$id|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large'; return false;">
            {if $desc}{$desc|escape}{else}{$smarty.foreach.imgLoop.iteration + 1}{/if}
          </a>
        {/foreach}
        </div>
      {/if}
  {else}
<img src="{$path}/bookcover.php" alt="{translate text='No Cover Image'}">
  {/if}
</div>
  {* End Cover Image *}

  <div class="toolbar span-3">
    <ul>
      <li id="saveLink"><a href="{$url}/Record/{$id|escape:"url"}/Save" class="saveRecord fav" id="saveRecord{$id|escape}" title="{translate text="Add to favorites"}">{translate text="Add to favorites"}</a></li>
      
      {* SMS commented out for now
      <li><a href="{$url}/Record/{$id|escape:"url"}/SMS" class="smsRecord sms" id="smsRecord{$id|escape}" title="{translate text="Text this"}">{translate text="Text this"}</a></li>
      *}
      
      <li><a href="{$url}/Record/{$id|escape:"url"}/Email" class="mailRecord mail" id="mailRecord{$id|escape}" title="{translate text="Email this"}">{translate text="Email this"}</a></li>
      {if is_array($exportFormats) && count($exportFormats) > 0}
      <li>
        <a href="{$url}/Record/{$id|escape:"url"}/Export?style={$exportFormats.0|escape:"url"}" class="export exportMenu">{translate text="Export Record"}</a>
        <ul class="menu offscreen" id="exportMenu">
        {foreach from=$exportFormats item=exportFormat}
          <li><a {if $exportFormat=="RefWorks"}target="{$exportFormat}Main" {/if}href="{$url}/Record/{$id|escape:"url"}/Export?style={$exportFormat|escape:"url"}">{translate text="Export to"} {$exportFormat|escape}</a></li>
        {/foreach}
        </ul>
      </li>
      {/if}
      <li><a href="{$url}/Record/{$id|escape:"url"}/Cite" class="citeRecord cite" id="citeRecord{$id|escape}" title="{translate text="Cite this"}">{translate text="Cite this"}</a></li>
      {if !empty($addThis)}
      <li id="addThis"><a class="addThis addthis_button"" href="https://www.addthis.com/bookmark.php?v=250&amp;pub={$addThis|escape:"url"}">{translate text='Bookmark'}</a></li>
      {/if}
      {if $bookBag}
      <li><a id="recordCart" class="{if in_array($id|escape, $bookBagItems)}bookbagDelete{else}bookbagAdd{/if} offscreen" href="">{translate text='Add to Book Bag'}</a></li>
      {/if}
    </ul>
    {if $bookBag}
    <div class="cartSummary">
    <form method="post" name="addForm" action="{$url}/Cart/Home">
      <input id="cartId" type="hidden" name="ids[]" value="{$id|escape}" />
      <noscript>
        {if in_array($id|escape, $bookBagItems)}
        <input id="cartId" type="hidden" name="ids[]" value="{$id|escape}" />
        <input type="submit" class="button cart bookbagDelete" name="delete" value="{translate text='Remove from Book Bag'}"/>
        {else}
        <input type="submit" class="button bookbagAdd" name="add" value="{translate text='Add to Book Bag'}"/>
        {/if}
      </noscript>
    </form>
    </div>
    {/if}
    <div class="clear"></div>
  </div>

    <div id="qrcode"></div>
    {js filename="qrcodeNDL.js"}
</div>

<div class="span-7">

 {include file=$coreMetadata}
  
  <div id="{if $dynamicTabs}dyn{/if}tabnav">
    <ul>
      {if $hasHoldings}
      <li{if $tab == 'Holdings' || $tab == 'Hold'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/Holdings{if $dynamicTabs}?subPage=1{/if}#tabnav">{translate text='Holdings'}</a>
      </li>
      {/if}
      <li{if $tab == 'Description'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/Description{if $dynamicTabs}?subPage=1{/if}#tabnav">{translate text='Description'}</a>
      </li>
      {if $hasTOC}
      <li{if $tab == 'TOC'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/TOC{if $dynamicTabs}?subPage=1{/if}#tabnav">{translate text='Table of Contents'}</a>
      </li>
      {/if}
      {if $hasContainedComponentParts}
      <li{if $tab == 'ComponentParts'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/ComponentParts{if $dynamicTabs}?subPage=1{/if}#tabnav" class="first"><span></span>{translate text='Contents/Parts'}</a>
      </li>
      {/if}
      <li{if $tab == 'UserComments'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/UserComments{if $dynamicTabs}?subPage=1{/if}#tabnav">{translate text='Comments'}</a>
      </li>
      {if $hasReviews}
      <li{if $tab == 'Reviews'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/Reviews{if $dynamicTabs}?subPage=1{/if}#tabnav">{translate text='Reviews'}</a>
      </li>
      {/if}
      {if $hasExcerpt}
      <li{if $tab == 'Excerpt'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/Excerpt{if $dynamicTabs}?subPage=1{/if}#tabnav">{translate text='Excerpt'}</a>
      </li>
      {/if}
      {if $hasHierarchyTree}
        <li{if $tab == 'Hierarchytree'} class="active"{/if}>
          <a href="{$url}/Record/{$id|escape:"url"}/HierarchyTree{if $dynamicTabs}?subPage=1{/if}#tabnav" class="first"><span></span>{translate text='hierarchy_tree'}</a>
        </li>
      {/if}
      {if $hasMap}
        <li{if $tab == 'Map'} class="active"{/if}>
          <a href="{$url}/Record/{$id|escape:"url"}/Map{if $dynamicTabs}?subPage=1{/if}#tabnav" class="first"><span></span>{translate text='Map View'}</a>
        </li>
      {/if}
      <li{if $tab == 'Details'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/Details{if $dynamicTabs}?subPage=1{/if}#tabnav">{translate text='Staff View'}</a>
      </li>
    </ul>
    <div class="clear"></div>
  </div>

  {if $dynamicTabs}
  <div class="recordsubcontent">
        {include file="Record/view-dynamic-tabs.tpl"}
  </div>
  {else}
  <div class="recordsubcontent">
        {include file="Record/$subTemplate"}
  </div>
  {/if}

  {* Add COINS *}
  <span class="Z3988" title="{$openURL|escape}"></span>
</div>

<div class="span-3 {if $sidebarOnLeft}pull-18 sidebarOnLeft{else}last{/if}">
  <div class="sidegroup">
    <h4>{translate text="Similar Items"}</h4>
    {if is_array($similarRecords)}
    <ul class="similar">
      {foreach from=$similarRecords item=similar}
      <li>
        {if is_array($similar.format)}
        <span class="{$similar.format[0]|lower|regex_replace:"/[^a-z0-9]/":""}">
        {else}
        <span class="{$similar.format|lower|regex_replace:"/[^a-z0-9]/":""}">
        {/if}
          <a href="{$url}/Record/{$similar.id|escape:"url"}">{$similar.title|escape}</a>
        </span>
        {if $similar.author}<br/>{translate text='By'}: {$similar.author|escape}{/if}
        {if $similar.publishDate} {translate text='Published'}: ({$similar.publishDate.0|escape}){/if}
      </li>
      {/foreach}
    </ul>
    {else}
      <p>{translate text='Cannot find similar records'}</p>
    {/if}
  </div>

  {if is_array($editions)}
  <div class="sidegroup">
    <h4>{translate text="Other Editions"}</h4>
    <ul class="similar">
      {foreach from=$editions item=edition}
      <li>
        {if is_array($edition.format)}
          <span class="{$edition.format[0]|lower|regex_replace:"/[^a-z0-9]/":""}">
        {else}
          <span class="{$edition.format|lower|regex_replace:"/[^a-z0-9]/":""}">
        {/if}
        <a href="{$url}/Record/{$edition.id|escape:"url"}">{$edition.title|escape}</a>
        </span>
        {$edition.edition|escape}
        {if $edition.publishDate}({$edition.publishDate.0|escape}){/if}
      </li>
      {/foreach}
    </ul>
  </div>
  {/if}
</div>

<div class="clear"></div>

<!-- END of: Record/view.tpl -->
