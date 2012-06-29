<div class="result recordId" id="record{$summId|escape}">

<div class="resultColumn1">

<div class="resultCheckbox">
  {if $bookBag}
  <label for="checkbox_{$summId|regex_replace:'/[^a-z0-9]/':''|escape}" class="offscreen">{translate text="Select this record"}</label>
  <input id="checkbox_{$summId|regex_replace:'/[^a-z0-9]/':''|escape}" type="checkbox" name="ids[]" value="{$summId|escape}" class="checkbox_ui"/>
  <input type="hidden" name="idsAll[]" value="{$summId|escape}" />
  {/if}
</div>

<div class="coverDiv">
  {assign var=img_count value=$summImages|@count}
  {if $img_count >= 1}
      <div id="imagelinks">
        <a id="thumbnail_link_{$summId|escape:"url"}" href="{$path}/thumbnail.php?id={$summId|escape:"url"}&size=large">
          <img id="thumbnail_img_{$summId|escape:"url"}" src="{$path}/thumbnail.php?id={$summId|escape:"url"}&size=small" class="summcover" alt="{translate text='Cover Image'}">
        </a>
      </div>
    {else}
      {if $summThumb}
        <img src="{$summThumb|escape}" class="summcover" alt="{translate text='Cover Image'}"/>
        {else}
        <img src="{$path}/bookcover.php" class="summcover" alt="{translate text='No Cover Image'}" title="{translate text='No Cover Image'}"/>
      {/if}
  {/if}
  </div>

  {foreach from=$summFormats item=format}
    <div class="resultItemFormat"><span class="iconlabel format{$format|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$format}</span></div>
  {/foreach}
</div>
    
  <div class="resultColumn2">
  
    <div class="resultItemLine1">
      <a href="{$url}/{if $summCollection}Collection{else}Record{/if}/{$summId|escape:"url"}" class="title">{if !empty($summHighlightedTitle)}{$summHighlightedTitle|addEllipsis:$summTitle|highlight}{elseif !$summTitle}{translate text='Title not available'}{else}{$summTitle|truncate:180:"..."|escape}{/if}</a>
    </div>

    <div class="resultItemLine2">
      {if !empty($summAuthor)}
      {translate text='by'}:
      <a href="{$url}/Author/Home?author={$summAuthor|escape:"url"}">{if !empty($summHighlightedAuthor)}{$summHighlightedAuthor|highlight}{else}{$summAuthor|escape}{/if}</a>
      {/if}
      <br/>
      {if $summDate}{translate text='Published'}: {$summDate.0|escape}{/if}
      {if $summInCollection}
        {foreach from=$summInCollection item=InCollection key=cKey}
          <div>
            <b>{translate text="in_collection_label"}</b>
            <a class="collectionLinkText" href="{$path}/Collection/{$summInCollectionID[$cKey]|urlencode|escape:"url"}?recordID={$summId|urlencode|escape:"url"}">
               {$InCollection}
            </a>
          </div>
        {/foreach}
      {else}
          {if !empty($summContainerTitle)}
          <div>
            <b>{translate text='component_part_is_part_of'}:</b>
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

    <div class="resultItemLine3 last">
      {if !empty($summSnippetCaption)}<strong>{translate text=$summSnippetCaption}:</strong>{/if}
      {if !empty($summSnippet)}<span class="quotestart">&#8220;</span>...{$summSnippet|highlight}...<span class="quoteend">&#8221;</span><br/>{/if}
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
        <span class="openUrlSeparator"><br/></span>
          {include file="Search/openurl.tpl" openUrl=$summOpenUrl}
        {/if}
        {foreach from=$summURLs key=recordurl item=urldesc}
          <br/><a href="{if $proxy}{$proxy}/login?qurl={$recordurl|escape:"url"}{else}{$recordurl|escape}{/if}" class="fulltext" target="new">{if $recordurl == $urldesc}{translate text='Get full text'}{else}{$urldesc|escape}{/if}</a>
        {/foreach}
      {/if}

      {if $summId|substr:0:8 == 'metalib_'}
        <br/><a href="{$path}/MetaLib/Home?set=_ird%3A{$summId|regex_replace:'/^.*?\./':''|escape}">{translate text='Search in this database'}</a>
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
  <div class="clear"></div>

  <div class="span-3 last addToFavLink">
    <a id="saveRecord{$summId|escape}" href="{$url}/Record/{$summId|escape:"url"}/Save" class="fav tool saveRecord" title="{translate text='Add to favorites'}">{translate text='Add to favorites'}</a>

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
</div>
  <div class="clear"></div>
</div>

{if $summCOinS}<span class="Z3988" title="{$summCOinS|escape}"></span>{/if}
