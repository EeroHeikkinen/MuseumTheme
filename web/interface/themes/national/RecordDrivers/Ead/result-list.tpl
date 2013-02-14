<!-- START of: RecordDrivers/Ead/result-list.tpl -->

<div class="result recordId" id="record{$summId|escape}">

<div class="resultColumn1">

<div class="resultCheckbox">
  {if $bookBag}
  <label for="checkbox_{$summId|regex_replace:'/[^a-z0-9]/':''|escape}" class="offscreen">{translate text="Select this record"}</label>
  <input id="checkbox_{$summId|regex_replace:'/[^a-z0-9]/':''|escape}" type="checkbox" name="ids[]" value="{$summId|escape}" class="checkbox_ui"/>
  <input type="hidden" name="idsAll[]" value="{$summId|escape}" />
  {/if}
</div>
  {if is_array($summFormats)}
    {assign var=mainFormat value=$summFormats.0} 
    {assign var=displayFormat value=$summFormats|@end} 
  {else}
    {assign var=mainFormat value=$summFormats} 
    {assign var=displayFormat value=$summFormats} 
  {/if}
{assign var=img_count value=$summImages|@count}
<div class="coverDiv">
      <div class="resultNoImage format{$mainFormat|lower|regex_replace:"/[^a-z0-9]/":""} format{$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}"></div>
    {if $img_count > 0}
        <div class="resultImage"><a href="{$url}/{if $summCollection}Collection{else}Record{/if}/{$summId|escape:"url"}"><img src="{$summThumb|escape}" class="summcover" alt="{translate text='Cover Image'}"/></a></div>
    {/if}

{* Multiple images *}
{if $img_count > 1}
  <div class="imagelinks">
{foreach from=$summImages item=desc name=imgLoop}
  <a href="{$path}/thumbnail.php?id={$summId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large" class="title" onmouseover="document.getElementById('thumbnail_{$summId|escape:"url"}').src='{$path}/thumbnail.php?id={$summId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=small'; document.getElementById('thumbnail_link_{$summId|escape:"url"}').href='{$path}/thumbnail.php?id={$summId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large'; return false;">
    {if $desc}{$desc|escape}{else}{$smarty.foreach.imgLoop.iteration + 1}{/if}
  </a>
{/foreach}
  </div>
{/if}
</div>
  
  <div class="resultItemFormat"><span class="iconlabel format{$mainFormat|lower|regex_replace:"/[^a-z0-9]/":""} format{$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$displayFormat prefix='format_'}</span></div>
</div>
    
  <div class="resultColumn2">

    <div class="resultItemLine1">
      <a href="{$url}/{if $summCollection}Collection{else}Record{/if}/{$summId|escape:"url"}" class="title">{$summSubtitle|escape} {if !empty($summHighlightedTitle)}{$summHighlightedTitle|addEllipsis:$summTitle|highlight}{elseif !$summTitle}{translate text='Title not available'}{else}{$summTitle|truncate:180:"..."|escape} {if $summYearRange}({$summYearRange|escape}){/if}{/if}</a>
    </div>
   
    <div class="resultHierarchyLinks">
        <span class="hierarchyDesc">{translate text='Archive Repository:'} </span>{foreach from=$summInstitutions name=loop item=institution}{translate text=$institution prefix='source_'}{if !$smarty.foreach.loop.last}, {/if}{/foreach}
        {if !empty($summOrigination)}
          <br/><span class="hierarchyDesc">{translate text='Archive Origination:'} </span><a href="{$url}/Search/Results?lookfor={$summOrigination|escape:"url"}&amp;type=Author">{$summOrigination|escape}</a>
        {/if}
        {if $displayFormat != 'Document/ArchiveFonds'} 
            <br/><span class="hierarchyDesc">{translate text='Archive:'} </span>{foreach from=$summHierarchyTopId name=loop key=topKey item=topId}<a href="{$url}/Collection/{$topId|escape:"url"}">{$summHierarchyTopTitle.$topKey|truncate:180:"..."|escape}</a>{if !$smarty.foreach.loop.last}, {/if}{/foreach}
        {/if}  
        {if $displayFormat != 'Document/ArchiveFonds' && $displayFormat != 'Document/ArchiveSeries'} 
            <br/><span class="hierarchyDesc">{translate text='Archive Series:'} </span>{foreach from=$summHierarchyParentId name=loop key=parentKey item=parentId}<a href="{$url}/Record/{$parentId|escape:"url"}">{$summHierarchyParentTitle.$parentKey|truncate:180:"..."|escape}</a>{if !$smarty.foreach.loop.last}, {/if}{/foreach}
        {/if}  
    </div>
   
    {if !empty($coreOtherLinks)}
        {assign var=prevOtherLinkHeading value=''}
        {foreach from=$coreOtherLinks item=coreOtherLink}
    <div class="resultOtherLinks">
        {if $prevOtherLinkHeading != $coreOtherLink.heading}{translate text=$coreOtherLink.heading prefix='link_'}:{else}&nbsp;{/if}
        {assign var=prevOtherLinkHeading value=$coreOtherLink.heading}
        {if $coreOtherLinks.isn}
        <a title="{$coreOtherLink.title|escape}" href="{$url}/Search/Results?lookfor={$coreOtherLink.isn|escape:"url"}&amp;type=ISN">
            {$coreOtherLink.title|escape}
        </a>
        {if $coreOtherLink.author}({$coreOtherLink.author|escape}){/if}
        {else}
        <a title="{$coreOtherLink.title|escape}" href="{$url}/Search/Results?lookfor=%22{$coreOtherLink.title|escape:"url"}%22&amp;type=Title">
            {$coreOtherLink.title|escape}
        </a>
        {if $coreOtherLink.author}({$coreOtherLink.author|escape}){/if}
        {/if}
    </div>    
        {/foreach}
    {/if}

    <div class="resultItemLine2">
      {if $summDate}{translate text='Published'}: {$summDate.0|escape}{/if}
      {if $summPublicationEndDate} - {if $summPublicationEndDate != 9999}{$summPublicationEndDate}{/if}{/if}
    </div>

    <div class="resultItemLine3">
      {if !empty($summSnippetCaption)}<strong>{translate text=$summSnippetCaption}:</strong>{/if}
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
        <strong>{translate text='Call Number'}:</strong> {$summCallNo|escape}
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
          <p class="resultContentToggle"><a href="#" class="toggleHeader">{translate text='Contents'}<img src="{$path}/interface/themes/institution/images/down.png" width="11" height="6" alt="" /></a></p>
          {else}
          <p class="resultContentToggle">{translate text='Contents'}<img src="{$path}/interface/themes/institution/images/down.png" width="11" height="6" alt="" /></p>
          {/if}
          <div class="resultContentList">
          {foreach from=$summURLs key=recordurl item=urldesc}
          <a href="{$recordurl|proxify|escape}" class="fulltext" target="_blank" title="{$recordurl|escape}">{if $recordurl == $urldesc}{$recordurl|truncate_url|escape}{else}{$urldesc|translate_prefix:'link_'|escape}{/if}</a>
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
        <a id="hierarchyTree{$summId|escape}" class="hierarchyTreeLinkText" href="{$url}/Record/{$summId|escape:"url"}/HierarchyTree?hierarchy={$hierarchyID}#tabnav" title="{if $coreShortTitle}{$coreShortTitle|truncate:150:"&nbsp;..."|urlencode}{else}{translate text="hierarchy_tree"}{/if}">
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

<!-- END of: RecordDrivers/Ead/result-list.tpl -->
