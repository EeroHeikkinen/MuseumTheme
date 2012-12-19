<div class="result recordId" id="record{$summId|escape}">

<div class="resultColumn1">

<div class="resultCheckbox">
  {if $bookBag}
  <label for="checkbox_{$summId|regex_replace:'/[^a-z0-9]/':''|escape}" class="offscreen">{translate text="Select this record"}</label>
  <input id="checkbox_{$summId|regex_replace:'/[^a-z0-9]/':''|escape}" type="checkbox" name="ids[]" value="{$summId|escape}" class="checkbox_ui"/>
  <input type="hidden" name="idsAll[]" value="{$summId|escape}" />
  {/if}
</div>

  {assign var=img_count value=$summImages|@count}
  <div class="coverDiv">
  
  {* Multiple images *}
  {if $img_count > 1}
    <div class="imagelinks">
  {foreach from=$summImages item=desc name=imgLoop}
	  <a href="{$path}/thumbnail.php?id={$summId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large" class="title" onmouseover="document.getElementById('thumbnail_{$summId|escape:"url"}').src='{$path}/thumbnail.php?id={$summId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=small'; document.getElementById('thumbnail_link_{$summId|escape:"url"}').href='{$path}/thumbnail.php?id={$summId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large'; return false;" />
	  {if $desc}{$desc|escape}{else}{$smarty.foreach.imgLoop.iteration + 1}{/if}
	  </a>
  {/foreach}
    </div>
  {/if}
  
  {* Cover image *}
    <div class="resultNoImage"><p>{translate text='No image'}</p></div>
  {if $img_count > 0}
      <div class="resultImage"><a href="{$url}/{if $summCollection}Collection{else}Record{/if}/{$summId|escape:"url"}"><img src="{$summThumb|escape}" class="summcover" alt="{translate text='Cover Image'}" /></a></div>
  {else}
      <div class="resultImage"><a href="{$url}/{if $summCollection}Collection{else}Record{/if}/{$summId|escape:"url"}"><img src="{$path}/images/NoCover2.gif" alt="No image" /></a></div>
  {/if}

</div>
  
  {if is_array($summFormats)}
    {assign var=mainFormat value=$summFormats.0} 
    {assign var=displayFormat value=$summFormats|@end} 
  {else}
    {assign var=mainFormat value=$summFormats} 
    {assign var=displayFormat value=$summFormats} 
  {/if}
  <div class="resultItemFormat"><span class="iconlabel format{$mainFormat|lower|regex_replace:"/[^a-z0-9]/":""} format{$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$displayFormat prefix='format_'}</span></div>
</div>
    
  <div class="resultColumn2">

    <div class="resultItemLine1">
      <a href="{$url}/{if $summCollection}Collection{else}Record{/if}/{$summId|escape:"url"}" class="title">{if !empty($summHighlightedTitle)}{$summHighlightedTitle|addEllipsis:$summTitle|highlight}{elseif !$summTitle}{translate text='Title not available'}{else}{$summTitle|truncate:180:"..."|escape}{/if}</a>
    </div>
   
    {if !empty($coreOtherLinks)}
        {foreach from=$coreOtherLinks item=coreOtherLink}
    <div class="resultOtherLinks">
        {translate text=$coreOtherLink.heading prefix='link_'}: 
        {if $coreOtherLinks.isn}
        <a title="{$coreOtherLink.title|escape}" href="{$url}/Search/Results?lookfor={$coreOtherLink.isn|escape:"url"}&amp;type=ISN">
            {if $coreOtherLink.author != ''}{$coreOtherLink.author|escape}: {/if}{$coreOtherLink.title|escape}
        </a>
        {else}
        <a title="{$coreOtherLink.title|escape}" href="{$url}/Search/Results?lookfor=%22{$coreOtherLink.title|escape:"url"}%22&amp;type=Title">
            {if $coreOtherLink.author != ''}{$coreOtherLink.author|escape}: {/if}{$coreOtherLink.title|escape}
        </a>
        {/if}
    </div>    
        {/foreach}
    {/if}

    <div class="resultItemLine2">
      {if !empty($summAuthor)}
      {translate text='by'}:
      <a href="{$url}/Author/Home?author={$summAuthorForSearch|escape:"url"}">{if !empty($summHighlightedAuthor)}{$summHighlightedAuthor|highlight}{else}{$summAuthor|escape}{/if}</a>
      {/if}
      {if $summDate}{translate text='Published'}: {$summDate.0|escape}{/if}
      {if $summPublicationEndDate} - {if $summPublicationEndDate != 9999}{$summPublicationEndDate}{/if}{/if}
      {if !empty($summClassifications)}
        <div class="resultClassification">
            {* This is a single-line mess due to Smarty otherwise adding spaces *}
            {translate text='Classification'}:
            {foreach from=$summClassifications key=class item=field name=loop}{if !$smarty.foreach.loop.first}, {/if}{foreach from=$field item=subfield name=subloop}{if !$smarty.foreach.subloop.first}, {/if}{$class|escape} {$subfield|escape}{/foreach}{/foreach}
        </div>
      {/if}
      {if $summInCollection}
        {foreach from=$summInCollection item=InCollection key=cKey}
          <div>
            {translate text="in_collection_label"}
            <a class="collectionLinkText" href="{$path}/Collection/{$summInCollectionID[$cKey]|urlencode|escape:"url"}?recordID={$summId|urlencode|escape:"url"}">
               {$InCollection}
            </a>
          </div>
        {/foreach}
      {else}
          {if !empty($summContainerTitle)}
          <div>
            {translate text='component_part_is_part_of'}:
            {if $summHierarchyParentId}
              <a href="{$url}/Record/{$summHierarchyParentId.0|escape:"url"}">{$summContainerTitle|escape}</a>
            {else}
              {$summContainerTitle|escape}
            {/if}
            {if !empty($summContainerReference)}{$summContainerReference|escape}{/if}
          </div>
          {/if}
      {/if}
    </div>

    <div class="resultItemLine3">
      {if !empty($summSnippetCaption)}
        {translate text=$summSnippetCaption}: {/if}
      {if !empty($summSnippet)}<span class="quotestart">&#8220;</span>...{$summSnippet|highlight}...<span class="quoteend">&#8221;</span><br/>{/if}
      {if $summDedupData}
        <span class="tiny">
        {foreach from=$summDedupData key=source item=dedupData name=loop}{if $smarty.foreach.loop.index == 1} ({translate text="Other:"} {/if}{if $smarty.foreach.loop.index > 1}, {/if}<a href="{$url}/Record/{$dedupData.id|escape:"url"}" class="title">{translate text=$source prefix='source_'}</a>{if $smarty.foreach.loop.last and !$smarty.foreach.loop.first}){/if}{/foreach}
        <br/>
        </span>
      {/if}
      <div id="callnumAndLocation{$summId|escape}">
      {if $summAjaxStatus}
        {if !$summOpenUrl && empty($summURLs) && $summAjaxStatus}
        <div class="ajax_availability hide noLoad" id="status{$summId|escape}">&nbsp;</div>
        {/if}
        {* <strong class="hideIfDetailed{$summId|escape}">{translate text='Call Number'}:</strong> <span class="ajax_availability hide" id="callnumber{$summId|escape}"> </span><br class="hideIfDetailed{$summId|escape}"/> 
        <strong>{translate text='Located'}:</strong> *} <span class="ajax_availability hide" id="location{$summId|escape}"> </span>
        <div class="hide" id="locationDetails{$summId|escape}"></div>
      {elseif !empty($summCallNo)}
        {translate text='Call Number'}: {$summCallNo|escape}
      {/if}
      </div>

      {if $summOpenUrl || !empty($summURLs)}
        {if $summOpenUrl}
        <span class="openUrlSeparator"></span>
          {include file="Search/openurl.tpl" openUrl=$summOpenUrl}
        {/if}
        {if $summURLs}
        <div>
          {if $summURLs|@count > 2}
          <p class="resultContentToggle"><a href="#" class="toggleHeader">{translate text='Contents'}<img src="{path filename="images/down.png"}" width="11" height="6" /></a></p>
          {else}
          <p class="resultContentToggle">{translate text='Contents'}<img src="{path filename="images/down.png"}" width="11" height="6" /></p>
          {/if}
          <div class="resultContentList">
          {foreach from=$summURLs key=recordurl item=urldesc}
          <a href="{if $proxy}{$proxy}/login?qurl={$recordurl|escape:"url"}{else}{$recordurl|escape}{/if}" class="fulltext" target="_blank" title="{$recordurl|escape}">{if $recordurl == $urldesc}{$recordurl|truncate_url|escape}{else}{$urldesc|escape}{/if}</a>
          {/foreach}
          </div>
        </div>
        {/if}
      {/if}

      {if $summId|substr:0:8 == 'metalib_'}
        <br/>
        <span class="metalib_link">
          <span id="metalib_link_{$summId|escape}" class="hide"><a href="{$path}/MetaLib/Home?set=_ird%3A{$summId|regex_replace:'/^.*?\./':''|escape}">{translate text='Search in this database'}</a><br/></span>
          <span id="metalib_link_na_{$summId|escape}" class="hide">{translate text='metalib_not_authorized_single'}<br/></span>
        </span>
      {/if}

      {* <br class="hideIfDetailed{$summId|escape}"/> *}

    </div>

    {if $showPreviews}
      {if (!empty($summLCCN) || !empty($summISBN) || !empty($summOCLC))}
      <div>
        {if $googleOptions}
          <div class="googlePreviewDiv__{$googleOptions}">
            <a title="{translate text='Preview from'} Google Books" class="hide previewGBS{if $summISBN} ISBN{$summISBN}{/if}{if $summLCCN} LCCN{$summLCCN}{/if}{if $summOCLC} OCLC{$summOCLC|@implode:' OCLC'}{/if}" target="_blank">
              <img src="https://www.google.com/intl/en/googlebooks/images/gbs_preview_button1.png" alt="{translate text='Preview'}"/>
            </a>
          </div>
        {/if}
        {if $olOptions}
          <div class="olPreviewDiv__{$olOptions}">
            <a title="{translate text='Preview from'} Open Library" class="hide previewOL{if $summISBN} ISBN{$summISBN}{/if}{if $summLCCN} LCCN{$summLCCN}{/if}{if $summOCLC} OCLC{$summOCLC|@implode:' OCLC'}{/if}" target="_blank">
              <img src="{$path}/images/preview_ol.gif" alt="{translate text='Preview'}"/>
            </a>
          </div>
        {/if}
        {if $hathiOptions}
          <div class="hathiPreviewDiv__{$hathiOptions}">
            <a title="{translate text='Preview from'} HathiTrust" class="hide previewHT{if $summISBN} ISBN{$summISBN}{/if}{if $summLCCN} LCCN{$summLCCN}{/if}{if $summOCLC} OCLC{$summOCLC|@implode:' OCLC'}{/if}" target="_blank">
              <img src="{$path}/images/preview_ht.gif" alt="{translate text='Preview'}"/>
            </a>
          </div>
        {/if}
        <span class="previewBibkeys{if $summISBN} ISBN{$summISBN}{/if}{if $summLCCN} LCCN{$summLCCN}{/if}{if $summOCLC} OCLC{$summOCLC|@implode:' OCLC'}{/if}"></span>
      </div>
      {/if}
    {/if}

    {* Display the lists that this record is saved to *}
    <div class="savedLists info hide" id="savedLists{$summId|escape}">
      <strong>{translate text="Saved in"}:</strong>
    </div>
    {if $summHierarchy}
      {foreach from=$summHierarchy key=hierarchyID item=hierarchyTitle}
      <div class="hierarchyTreeLink">
        <input type="hidden" value="{$hierarchyID|escape}" class="hiddenHierarchyId" />
        <a id="hierarchyTree{$summId|escape}" class="hierarchyTreeLinkText" href="{$path}/Record/{$summId|escape:"url"}/HierarchyTree?hierarchy={$hierarchyID}#tabnav" title="{if $coreShortTitle}{$coreShortTitle|truncate:150:"&nbsp;..."|urlencode}{else}{translate text="hierarchy_tree"}{/if}">
          {if count($summHierarchy) == 1}
            {translate text="hierarchy_view_context"}
          {else}
            {translate text="hierarchy_view_context"}: {$hierarchyTitle}
          {/if}
        </a>
      </div>
      {/foreach}
    {/if}
</div>
  <div class="last addToFavLink">
    <a id="saveRecord{$summId|escape}" href="{$url}/Record/{$summId|escape:"url"}/Save" class="fav tool saveRecord" title="{translate text='Add to favorites'}"></a>
  </div>
  <div class="clear"></div>
</div>

{if $summCOinS}<span class="Z3988" title="{$summCOinS|escape}"></span>{/if}
