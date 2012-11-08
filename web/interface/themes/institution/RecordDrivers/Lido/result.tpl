<!-- START of: RecordDrivers/Lido/result.tpl -->

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
        <div class="resultNoImage"><p>{translate text='No image'}</p></div>
    	{if $img_count > 0}
        {* large image link: <a id="thumbnail_link_{$summId|escape:"url"}" href="{$path}/thumbnail.php?id={$summId|escape:"url"}&size=large"> *}
        <div class="resultImage"><a href="{$url}/Record/{$summId|escape:"url"}">
          <img id="thumbnail_{$summId|escape:"url"}" src="{$path}/thumbnail.php?id={$summId|escape:"url"}&size=small" class="summcover" alt="{translate text='Cover Image'}">
        </a></div>
    	{else}
        <div class="resultImage"><a href="{$url}/Record/{$summId|escape:"url"}"><img src="{$path}/images/NoCover2.gif" /></a></div>
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
    
  {if is_array($summFormats)}
    {assign var=mainFormat value=$summFormats.0} 
    {assign var=displayFormat value=$summFormats|@end} 
  {else}
    {assign var=mainFormat value=$summFormats} 
    {assign var=displayFormat value=$summFormats} 
  {/if}
  <div class="resultItemFormat"><span class="iconlabel format{$mainFormat|lower|regex_replace:"/[^a-z0-9]/":""} format{$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=format_$displayFormat}</span></div>
</div>

 <div class="resultColumn2">
      <div class="resultItemLine1">
      	<a href="{$url}/Record/{$summId|escape:"url"}" class="title">{if !empty($summHighlightedTitle)}{$summHighlightedTitle|addEllipsis:$summTitle|highlight}{elseif !$summTitle}{translate text='Title not available'}{else}{$summTitle|truncate:180:"..."|escape}{/if} {$summSubtitle}</a>
      </div>

      <div class="resultItemLine2">
      	{if !empty($summAuthor)}
      	{translate text='by'}:
      	<a href="{$url}/Author/Home?author={$summAuthor|escape:"url"}">{if !empty($summHighlightedAuthor)}{$summHighlightedAuthor|highlight}{else}{$summAuthor|escape}{/if}</a>
      	{/if}

      	{if $summDate}<br>{translate text='Main Year'}: {$summDate.0|escape}{/if}
      	{if $summImageDate}<br>{translate text='Photo Taken:'} {$summImageDate|escape}{/if}
      	{if $summCreationDate}<br>{translate text='Created:'} {$summCreationDate|escape}{/if}
      	{if $summUseDate}<br>{translate text='Used:'} {$summUseDate|escape}{if $summUsePlace}, {$summUsePlace|escape}{/if}{/if}
      
      </div>

      <div class="resultItemLine3">
      {if !empty($summSnippetCaption)}<b>{translate text=$summSnippetCaption}:</b>{/if}
      {if !empty($summSnippet)}<span class="quotestart">&#8220;</span>...{$summSnippet|highlight}...<span class="quoteend">&#8221;</span><br>{/if}
      <div id="callnumAndLocation{$summId|escape}">
      {if $summAjaxStatus}
      <b>{translate text='Call Number'}:</b> <span id="callnumber{$summId|escape}">{translate text='Loading'}</span><br>
      <b>{translate text='Located'}:</b> <span id="location{$summId|escape}">{translate text='Loading'}</span>
      {elseif !empty($summCallNo)}
      <b>{translate text='Call Number'}:</b> {$summCallNo|escape}
      {/if}
      </div>
      </div>
      {if !empty($summHostRecordTitle)}
      <div class="resultItemLine4">
        <b>{translate text='component_part_is_part_of'}:</b> <a href="{$url}/Record/{$summHostRecordId|escape:"url"}">{$summHostRecordTitle|escape}</a>
      </div>
      {/if}
      <div class="resultItemLine4">
      {if $summOpenUrl || !empty($summURLs)}
        {if $summOpenUrl}
          <br>
          {include file="Search/openurl.tpl" openUrl=$summOpenUrl}
        {/if}
        {foreach from=$summURLs key=recordurl item=urldesc name="urlLoop"}
          {if $smarty.foreach.urlLoop.iteration <= 10}
          <br><a href="{if $proxy}{$proxy}/login?qurl={$recordurl|escape:"url"}{else}{$recordurl|escape}{/if}" class="fulltext" target="new">{if $recordurl == $urldesc}{translate text='Get full text'}{else}{$urldesc|escape}{/if}</a>
          {/if}
        {/foreach}
        {if count($summURLs) > 10}
          <br><a href="{$url}/Record/{$summId|escape:"url"}">{math equation="x - 10" x=$summURLs|@count} {translate text='more'} ...</a>
        {/if}
      {elseif $summAjaxStatus}
        <div class="status" id="status{$summId|escape}">
          <span class="unknown" style="font-size: 8pt;">{translate text='Loading'}...</span>
        </div>
      {/if}
        <div style="display: none;" id="locationDetails{$summId|escape}">&nbsp;</div>
      </div>

   <div class="savedLists info hide" id="savedLists{$summId|escape}">
      <ul id="lists{$summId|escape}"></ul>
      <script language="JavaScript" type="text/javascript">
        getSaveStatuses('{$summId|escape:"javascript"}');
      </script>
    </div>
    {if $showPreviews}
      {if (!empty($summLCCN) || !empty($summISBN) || !empty($summOCLC))}
        {if $showGBSPreviews}      
          <div class="previewDiv"> 
            <a class="{if $summISBN}gbsISBN{$summISBN}{/if}{if $summLCCN}{if $summISBN} {/if}gbsLCCN{$summLCCN}{/if}{if $summOCLC}{if $summISBN || $summLCCN} {/if}{foreach from=$summOCLC item=OCLC name=oclcLoop}gbsOCLC{$OCLC}{if !$smarty.foreach.oclcLoop.last} {/if}{/foreach}{/if}" style="display:none" target="_blank">
              <img src="https://www.google.com/intl/en/googlebooks/images/gbs_preview_button1.png" border="0" style="width: 70px; margin: 0; padding-bottom:5px;"/>
            </a>    
          </div>
        {/if}
        {if $showOLPreviews}
          <div class="previewDiv">
            <a class="{if $summISBN}olISBN{$summISBN}{/if}{if $summLCCN}{if $summISBN} {/if}olLCCN{$summLCCN}{/if}{if $summOCLC}{if $summISBN || $summLCCN} {/if}{foreach from=$summOCLC item=OCLC name=oclcLoop}olOCLC{$OCLC}{if !$smarty.foreach.oclcLoop.last} {/if}{/foreach}{/if}" style="display:none" target="_blank">
              <img src="{$path}/images/preview_ol.gif" border="0" style="width: 70px; margin: 0"/>
            </a>
          </div> 
        {/if}
        {if $showHTPreviews}
          <div class="previewDiv">
            <a id="HT{$summId|escape}" style="display:none"  target="_blank">
              <img src="{$path}/images/preview_ht.gif" border="0" style="width: 70px; margin: 0" title="{translate text='View online: Full view Book Preview from the Hathi Trust'}"/>
            </a>
          </div> 
        {/if}
      {/if}
     {/if}
  </div>
  <div class="last addToFavLink">
      <a href="{$url}/Record/{$summId|escape:"url"}/Save" onClick="getLightbox('Record', 'Save', '{$summId|escape}', '', '{translate text='Add to favorites'}', 'Record', 'Save', '{$summId|escape}'); return false;" class="fav tool"></a>
  </div>
  <div class="clear"></div>
 </div>

{if $summCOinS}<span class="Z3988" title="{$summCOinS|escape}"></span>{/if}

{if $summAjaxStatus}
<script type="text/javascript">
  getStatuses('{$summId|escape:"javascript"}');
</script>
{/if}
{if $showPreviews}
<script type="text/javascript">
  {if $summISBN}getExtIds('ISBN{$summISBN|escape:"javascript"}');{/if}
  {if $summLCCN}getExtIds('LCCN{$summLCCN|escape:"javascript"}');{/if}
  {if $summOCLC}{foreach from=$summOCLC item=OCLC}getExtIds('OCLC{$OCLC|escape:"javascript"}');{/foreach}{/if}
  {if (!empty($summLCCN) || !empty($summISBN) || !empty($summOCLC))}
    getHTIds('id:HT{$summId|escape:"javascript"};{if $summISBN}isbn:{$summISBN|escape:"javascript"}{/if}{if $summLCCN}{if $summISBN};{/if}lccn:{$summLCCN|escape:"javascript"}{/if}{if $summOCLC}{if $summISBN || $summLCCN};{/if}{foreach from=$summOCLC item=OCLC name=oclcLoop}oclc:{$OCLC|escape:"javascript"}{if !$smarty.foreach.oclcLoop.last};{/if}{/foreach}{/if}')
  {/if}
</script>
{/if}

<!-- END of: RecordDrivers/Lido/result.tpl -->
