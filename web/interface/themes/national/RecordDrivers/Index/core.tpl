<!-- START of: RecordDrivers/Index/core.tpl -->

<div id="recordMetadata">
    
  {* Display Title *}
  <h1 class="recordTitle">{$coreShortTitle|escape}{if $coreSubtitle}&nbsp;: {$coreSubtitle|escape}{/if}
  {* {if $coreTitleSection} / {$coreTitleSection|escape}{/if}
  {if $coreTitleStatement}{$coreTitleStatement|escape}{/if} *}
  </h1>
  {* End Title *}

  {if !empty($coreRecordLinks)}
  <div class="recordLinks">
    {foreach from=$coreRecordLinks item=coreRecordLink}
      {translate text=$coreRecordLink.title}:
      <a href="{$coreRecordLink.link|escape}">{$coreRecordLink.value|escape}</a>
    {/foreach}
  </div>
  {/if}

  {* Display Cover Image, commented out since already in view.tpl
  {if $coreThumbMedium}
    {if $coreThumbLarge}<a href="{$coreThumbLarge|escape}">{/if}
      <img alt="{translate text='Cover Image'}" class="recordcover" src="{$coreThumbMedium|escape}"/>
    {if $coreThumbLarge}</a>{/if}
  {else}
    <img src="{$path}/bookcover.php" class="recordcover" alt="{translate text='No Cover Image'}"/>
  {/if}
  End Cover Image *}

  {* Summary, commented out since it exists in extended.tpl 
  {if $coreSummary}<p>{$coreSummary|truncate:300:"..."|escape}</p>{/if}
  *}

  {* Display Main Details *}
  <table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Bibliographic Details'}">
    {if !empty($coreContainerTitle)}
    <tr valign="top" class="recordContainerReference">
      <th>{translate text='component_part_is_part_of'}:</th>
      <td>
      {if $coreHierarchyParentId}
        <a href="{$url}/Record/{$coreHierarchyParentId[0]|escape:"url"}/ComponentParts">{$coreContainerTitle|escape}</a>
      {else}
        {$coreContainerTitle|escape}
      {/if}
      {if !empty($coreContainerReference)}{$coreContainerReference|escape}{/if}
      </td>
    </tr>
    {/if}

    {if !empty($coreNextTitles)}
    <tr valign="top" class="recordNextTitles">
      <th>{translate text='New Title'}: </th>
      <td>
        {foreach from=$coreNextTitles item=field name=loop}
          <a href="{$url}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Title">{$field|escape}</a><br/>
        {/foreach}
      </td>
    </tr>
    {/if}

    {if !empty($corePrevTitles)}
    <tr valign="top" class="recordPrevTitles">
      <th>{translate text='Previous Title'}: </th>
      <td>
        {foreach from=$corePrevTitles item=field name=loop}
          <a href="{$url}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Title">{$field|escape}</a><br/>
        {/foreach}
      </td>
    </tr>
    {/if}

    {if !empty($coreOtherLinks)}
    {assign var=prevOtherLinkHeading value=''}
    {foreach from=$coreOtherLinks item=coreOtherLink}
    <tr valign="top" class="recordOtherLink">
      <th>{if $prevOtherLinkHeading != $coreOtherLink.heading}{translate text=$coreOtherLink.heading prefix='link_'}:{else}&nbsp;{/if}</th>
      {assign var=prevOtherLinkHeading value=$coreOtherLink.heading}
      <td>
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
      </td>
    </tr>
    {/foreach}    
    {/if}

    {if $coreNonPresenterAuthors}
    <tr valign="top" class="recordAuthors">
      <th>{translate text='Authors'}: </th>
      <td>
        <div class="truncateField">
      {foreach from=$coreNonPresenterAuthors item=field name=loop}
          <a href="{$url}/Search/Results?lookfor={$field.name|escape:"url"}&amp;type=Author">{$field.name|escape}{if $field.role}, {$field.role|escape}{/if}</a>{if !$smarty.foreach.loop.last} ; {/if}
      {/foreach}
        </div>
      </td>
    </tr>
    {/if}

    {if $corePresenters.presenters or $corePresenters.details}
    <tr valign="top" class="recordPresenters">
      <th>{translate text='Presenters'}: </th>
      <td>
        <div class="truncateField">
      {foreach from=$corePresenters.presenters item=field name=loop}
          <a href="{$url}/Search/Results?lookfor={$field.name|escape:"url"}&amp;type=Author">{$field.name|escape}{if $field.role}, {$field.role|escape}{/if}</a>{if !$smarty.foreach.loop.last} ; {/if}
      {/foreach}
      {foreach from=$corePresenters.details item=detail name=loop}
          <br />
          {$detail|escape}
      {/foreach}        
        </div>
      </td>
    </tr>
    {/if}

    {if !empty($coreAlternativeTitles)}
    <tr valign="top" class="recordAltTitles">
      <th>{translate text='Other Titles'}: </th>
      <td>
        {foreach from=$coreAlternativeTitles item=field name=loop}
          {$field|escape}{if !$smarty.foreach.loop.last}; {/if}
        {/foreach}
      </td>
    </tr>
    {/if}

    <tr valign="top" class="recordFormat">
      <th>{translate text='Format'}: </th>
      <td>
        {if is_array($recordFormat)}
          {assign var=mainFormat value=$recordFormat.0} 
          {assign var=displayFormat value=$recordFormat|@end} 
        {else}
          {assign var=mainFormat value=$recordFormat} 
          {assign var=displayFormat value=$recordFormat} 
        {/if}
        <span class="iconlabel format{$mainFormat|lower|regex_replace:"/[^a-z0-9]/":""} format{$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$displayFormat prefix='format_'}</span>
        {if !empty($extendedPhysical)}
          {assign var=extendedContentDisplayed value=1}
          {foreach from=$extendedPhysical item=field name=loop}
            <br/>{$field|escape}
          {/foreach}
        {/if}
      </td>
    </tr>

    {if $recordLanguage}
    <tr valign="top" class="recordLanguage">
      <th>{translate text='Language'}: </th>
      <td>{foreach from=$recordLanguage item=lang}{translate text=$lang prefix='facet_'}<br/>{/foreach}</td>
    </tr>
    {/if}

    {if !empty($corePublications)}
    <tr valign="top" class="recordPublications">
      <th>{translate text='Published'}: </th>
      <td>
        {foreach from=$corePublications item=field name=loop}
          {$field|escape}<br/>
        {/foreach}
      </td>
    </tr>
    {/if}
    
    {if !empty($coreProjectedPublicationDate)}
    <tr valign="top" class="coreProjectedPublicationDate">
      <th>{translate text='Projected Publication Date'}: </th>
      <td>
        {$coreProjectedPublicationDate}
      </td>
    </tr>
    {/if}

    {if $coreDissertationNote}
    <tr valign="top" class="coreDissertationNote">
      <th>{translate text='Dissertation Note'}: </th>
      <td>
        {$coreDissertationNote}
      </td>
    </tr>
    {/if}
    
    {if !empty($coreManufacturer)}
    <tr valign="top" class="recordManufacturer">
      <th>{translate text='Manufacturer'}: </th>
      <td>
        {$coreManufacturer|escape}
      </td>
    </tr>
    {/if}

    {if !empty($coreEdition)}
    <tr valign="top" class="recordEdition">
      <th>{translate text='Edition'}: </th>
      <td>
        {$coreEdition|escape}
      </td>
    </tr>
    {/if}

    {* Display series section if at least one series exists. *}
    {if !empty($coreSeries)}
    <tr valign="top" class="recordSeries">
      <th>{translate text='Series'}: </th>
      <td>
        {foreach from=$coreSeries item=field name=loop}
          {* Depending on the record driver, $field may either be an array with
             "name" and "number" keys or a flat string containing only the series
             name.  We should account for both cases to maximize compatibility. *}
          {if is_array($field)}
            {if !empty($field.name)}
              <a href="{$url}/Search/Results?lookfor=%22{$field.name|escape:"url"}%22&amp;type=Series">{$field.name|escape}</a>
              {if !empty($field.number)}
                {$field.number|escape}
              {/if}
              <br/>
            {/if}
          {else}
            <a href="{$url}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Series">{$field|escape}</a><br/>
          {/if}
        {/foreach}
      </td>
    </tr>
    {/if}

    {if !empty($coreSubjects)}
    <tr valign="top" class="recordSubjects">
      <th>{translate text='Subjects'}: </th>
      <td>
        <div class="truncateField">
        {foreach from=$coreSubjects item=field name=loop}
        <div class="subjectLine">
          {assign var=subject value=""}
          {foreach from=$field item=subfield name=subloop}
            {if !$smarty.foreach.subloop.first} &gt; {/if}
            {if $subject}
              {assign var=subject value="$subject $subfield"}
            {else}
              {assign var=subject value="$subfield"}
            {/if}
            <a title="{$subject|escape}" href="{$url}/Search/Results?lookfor=%22{$subject|escape:"url"}%22&amp;type=Subject" class="subjectHeading">{$subfield|escape}</a>
          {/foreach}
        </div>
        {/foreach}
        </div>
      </td>
    </tr>
    {/if}

    {if !empty($coreGenres)}
    <tr valign="top" class="recordGenres">
      <th>{translate text='Genre'}: </th>
      <td>
        {foreach from=$coreGenres item=field name=loop}
        <div class="subjectLine">
          {assign var=subject value=""}
          {foreach from=$field item=subfield name=subloop}
            {if !$smarty.foreach.subloop.first} &gt; {/if}
            {if $subject}
              {assign var=subject value="$subject $subfield"}
            {else}
              {assign var=subject value="$subfield"}
            {/if}
            <a title="{$subject|escape}" href="{$url}/Search/Results?lookfor=%22{$subject|escape:"url"}%22&amp;type=Subject" class="subjectHeading">{$subfield|translate|escape}</a>
          {/foreach}
        </div>
        {/foreach}
      </td>
    </tr>
    {/if}

    {if !empty($coreClassifications)}
    <tr valign="top" class="recordClassifications">
      <th>{translate text='Classification'}: </th>
      <td>
        <div class="truncateField">
        {* This is a single-line mess due to Smarty otherwise adding spaces *}
        {foreach from=$coreClassifications key=class item=field name=loop}{if !$smarty.foreach.loop.first}, {/if}{foreach from=$field item=subfield name=subloop}{if !$smarty.foreach.subloop.first}, {/if}{$class|escape} {$subfield|escape}{/foreach}{/foreach}
        </div>
      </td>
    </tr>
    {/if}

    {if $extendedMetadata}
      {include file=$extendedMetadata}
    {/if}

    {if $coreComponentPartCount > 0 && !$hasContainedComponentParts}
    <tr valign="top" class="recordComponentParts">
      <th>{translate text='component_part_count_label'} </th>
      <td>
        {translate text='component_part_count_prefix'} <a href="{$url}/Search/Results?lookfor={$id|escape:"url"}&amp;type=hierarchy_parent_id">{$coreComponentPartCount|escape} {translate text='component_part_count_suffix'}</a>
      </td>
    </tr>
    {/if}

    {assign var="idPrefix" value=$id|substr:0:8}
    {if !empty($coreURLs) || $coreOpenURL || $idPrefix == 'metalib_'}
    <tr valign="top" class="recordURLs">
      <th>{translate text='Online Access'}: </th>
      <td>
        {foreach from=$coreURLs item=desc key=currentUrl name=loop}
          <a href="{$currentUrl|proxify|escape}" target="_blank">{$desc|translate_prefix:'link_'|escape}</a><br/>
        {/foreach}
        {if $coreOpenURL}
          {include file="Search/openurl.tpl" openUrl=$coreOpenURL}
          {include file="Search/rsi.tpl"}
          {include file="Search/openurl_autocheck.tpl"}
        {/if}
        {if $idPrefix == 'metalib_'}
          <span class="metalib_link">
            <span id="metalib_link_{$id|escape}" class="hide"><a href="{$path}/MetaLib/Home?set=_ird%3A{$id|regex_replace:'/^.*?\./':''|escape}">{translate text='Search in this database'}</a></span>
            <span id="metalib_link_na_{$id|escape}" class="hide">{translate text='metalib_not_authorized_single'}<br/></span>
          </span>
        {/if}
      </td>
    </tr>
    {/if}
    
    {*
    {if !empty($coreRecordLinks)}
    {foreach from=$coreRecordLinks item=coreRecordLink}
    <tr valign="top" class="recordLinks">
      <th>{translate text=$coreRecordLink.title}: </th>
      <td><a href="{$coreRecordLink.link|escape}">{$coreRecordLink.value|escape}</a></td>
    </tr>
    {/foreach}
    {/if}
    *}
    
    {if $toc}
    <tr valign="top" class="recordTOC">
      <th>{translate text='Table of Contents'}: </th>
      <td>
        <div class="truncateField">
      {foreach from=$toc item=line}
        {$line|escape}<br />
      {/foreach}
        </div>
      </td>
    </tr>
    {/if}
    
    <tr valign="top" class="recordTags">
      <th>{translate text='Tags'}: </th>
      <td>
        <span style="float:right;">
          <a href="{$url}/Record/{$id|escape:"url"}/AddTag" class="tool add tagRecord" title="{translate text='Add Tag'}" id="tagRecord{$id|escape}">{translate text='Add Tag'}</a>
        </span>
        <div id="tagList">
          {if $tagList}
            {foreach from=$tagList item=tag name=tagLoop}
          <a href="{$url}/Search/Results?tag={$tag->tag|escape:"url"}">{$tag->tag|escape:"html"}</a> ({$tag->cnt}){if !$smarty.foreach.tagLoop.last}, {/if}
            {/foreach}
          {else}
            {translate text='No Tags'}
          {/if}
        </div>
      </td>
    </tr>
  </table>
  {* End Main Details *}
</div>

<div class="span-4 last">

  {* Display the lists that this record is saved to *}
  <div class="savedLists info hide" id="savedLists{$id|escape}">
    <strong>{translate text="Saved in"}:</strong>
  </div>

  {if $showPreviews && (!empty($holdingLCCN) || !empty($isbn) || !empty($holdingArrOCLC))}
    {if $googleOptions}
      <div class="googlePreviewDiv__{$googleOptions}">
        <a title="{translate text='Preview from'} Google Books" class="hide previewGBS{if $isbn} ISBN{$isbn}{/if}{if $holdingLCCN} LCCN{$holdingLCCN}{/if}{if $holdingArrOCLC} OCLC{$holdingArrOCLC|@implode:' OCLC'}{/if}" target="_blank">
          <img src="https://www.google.com/intl/en/googlebooks/images/gbs_preview_button1.png" alt="{translate text='Preview'}"/>
        </a>
      </div>
    {/if}
    {if $olOptions}
      <div class="olPreviewDiv__{$olOptions}">
        <a title="{translate text='Preview from'} Open Library" href="" class="hide previewOL{if $isbn} ISBN{$isbn}{/if}{if $holdingLCCN} LCCN{$holdingLCCN}{/if}{if $holdingArrOCLC} OCLC{$holdingArrOCLC|@implode:' OCLC'}{/if}" target="_blank">
          <img src="{$path}/images/preview_ol.gif" alt="{translate text='Preview'}"/>
        </a>
      </div>
    {/if}
    {if $hathiOptions}
      <div class="hathiPreviewDiv__{$hathiOptions}">
        <a title="{translate text='Preview from'} HathiTrust" class="hide previewHT{if $isbn} ISBN{$isbn}{/if}{if $holdingLCCN} LCCN{$holdingLCCN}{/if}{if $holdingArrOCLC} OCLC{$holdingArrOCLC|@implode:' OCLC'}{/if}" target="_blank">
          <img src="{$path}/images/preview_ht.gif" alt="{translate text='Preview'}"/>
        </a>
      </div>
    {/if}
    <span class="previewBibkeys{if $isbn} ISBN{$isbn}{/if}{if $holdingLCCN} LCCN{$holdingLCCN}{/if}{if $holdingArrOCLC} OCLC{$holdingArrOCLC|@implode:' OCLC'}{/if}"></span>
  {/if}
</div>

<div class="clear"></div>

<!-- END of: RecordDrivers/Index/core.tpl -->
