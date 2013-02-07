<!-- START of: RecordDrivers/Lido/core.tpl -->

<div id="recordMetadata">

{* Display Title *}
{literal}
  <script language="JavaScript" type="text/javascript">
    //<![CDATA[
    function subjectHighlightOn(subjNum, partNum)
    {
        // Create shortcut to YUI library for readability:
        var yui = YAHOO.util.Dom;

        for (var i = 0; i < partNum; i++) {
            var targetId = "subjectLink_" + subjNum + "_" + i;
            var o = document.getElementById(targetId);
            if (o) {
                yui.addClass(o, "hoverLink");
            }
        }
    }

    function subjectHighlightOff(subjNum, partNum)
    {
        // Create shortcut to YUI library for readability:
        var yui = YAHOO.util.Dom;

        for (var i = 0; i < partNum; i++) {
            var targetId = "subjectLink_" + subjNum + "_" + i;
            var o = document.getElementById(targetId);
            if (o) {
                yui.removeClass(o, "hoverLink");
            }
        }
    }
    //]]>
  </script>
{/literal}


{* Display Title *}
<h1 class="recordTitle">{$coreShortTitle|escape}
{if $coreSubtitle}{$coreSubtitle|escape}{/if}
{if $coreTitleSection}{$coreTitleSection|escape}{/if}
{* {if $coreTitleStatement}{$coreTitleStatement|escape}{/if} *}
</h1>
{* End Title *}

{* Display Cover Image, commented out since already in view.tpl

  {if $coreThumbMedium}
    <div>
      {if $coreThumbLarge}<a id="thumbnail_link" href="{$coreThumbLarge|escape}">{/if}
        <img id="thumbnail" alt="{translate text='Cover Image'}" class="recordcover span-3" src="{$coreThumbMedium|escape}">
      {if $coreThumbLarge}</a>{/if}
      {assign var=img_count value=$coreImages|@count}
      {if $img_count > 1}
        <div class="imagelinks">
      {foreach from=$coreImages item=desc name=imgLoop}
          <a href="{$path}/thumbnail.php?id={$id|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large" class="title" onclick="document.getElementById('thumbnail').src='{$path}/thumbnail.php?id={$id|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=medium'; document.getElementById('thumbnail_link').href='{$path}/thumbnail.php?id={$id|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large'; return false;">
            {if $desc}{$desc|escape}{else}{$smarty.foreach.imgLoop.iteration + 1}{/if}
          </a>
        {/foreach}
        </div>
      {/if}
    </div>
  {else}
<img src="{$path}/bookcover.php" alt="{translate text='No Cover Image'}">
  {/if}
<div class="clear"></div>

End Cover Image *}

{if $coreSummary}<div class="recordSummary truncateField">{$coreSummary|escape|nl2br}</div>{/if}

{* Display Main Details *}
<table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Bibliographic Details'}">
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
  
  {if $coreSubjectDetails}
  <tr valign="top">
    <th>{translate text='Subject Detail'}: </th>
    <td>{foreach from=$coreSubjectDetails item=value}{$value|escape}<br>{/foreach}</td>
  </tr>
  {/if}
  
  {if $coreSubjectPlaces}
  <tr valign="top">
    <th>{translate text='Subject Place'}: </th>
    <td>{foreach from=$coreSubjectPlaces item=value}{$value|escape}<br>{/foreach}</td>
  </tr>
  {/if}
  
  {if $coreSubjectDates}
  <tr valign="top">
    <th>{translate text='Subject Date'}: </th>
    <td>{foreach from=$coreSubjectDates item=value}{$value|escape}<br>{/foreach}</td>
  </tr>
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

  {if $corePresenters}
  <tr valign="top" class="recordPresenters">
    <th>{translate text='Presenters'}: </th>
    <td>
      <div class="truncateField">
    {foreach from=$corePresenters item=field name=loop}
        <a href="{$url}/Search/Results?lookfor={$field.name|escape:"url"}&amp;type=Author">{$field.name|escape}{if $field.role}, {$field.role|escape}{/if}</a>{if !$smarty.foreach.loop.last} ; {/if}
    {/foreach}
      </div>
    </td>
  </tr>
  {/if}

  {if !empty($coreInstitutions)}
  <tr valign="top" class="recordCollection">
    <th>{translate text='Location'}: </th>
    <td>
      {foreach from=$coreInstitutions item=field name=loop}
        {translate text=$field prefix='source_'}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($coreCollections)}
  <tr valign="top" class="recordCollection">
    <th>{translate text='Collection'}: </th>
    <td>
      {foreach from=$coreCollections item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($coreIdentifier)}
  <tr valign="top" class="recordIdentifier">
    <th>{translate text='Inventory ID'}: </th>
    <td>
      {foreach from=$coreIdentifier item=field name=loop}
        {$field|escape}{if !$smarty.foreach.loop.last}, {/if}
      {/foreach}
    </td>
  </tr>
  {/if}

  {if is_array($coreMeasurements)}
  <tr valign="top" class="recordMeasurements">
    <th>{translate text='Measurements'}: </th>
    <td>{foreach from=$coreMeasurements item=measurement}{$measurement|escape}<br/>{/foreach}</td>
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
    </td>
  </tr>

  {if is_array($coreEvents)}
    {foreach from=$coreEvents key=eventType item=events}
  <tr valign="top" class="recordEvents">
      <th>{$eventType|translate_prefix:"lido_event_type_$mainFormat\x5f"|escape}:</th> 
      <td>
        <div class="truncateField">
      {foreach from=$events item=event name=eventLoop}
        {if $event.name}{$event.name}<br/>{/if}
        {$event.date|escape} 
        {if !empty($event.method)} -- {$event.method|escape}{/if}
        {if !empty($event.materials)} -- {$event.materials|escape}{/if}
        {if !empty($event.place)} -- {$event.place|escape}{/if}
        {foreach from=$event.actors item=actor name=actorsLoop}
          {if $smarty.foreach.actorsLoop.index > 1}, {/if}
          {$actor.name|escape}{if !empty($actor.role)} ({$actor.role|escape}){/if}
        {/foreach}
        {if !$smarty.foreach.eventLoop.last}<br/><br/>{/if}        
      {/foreach}
        </div>
      </td>
  </tr>
    {/foreach}
  {/if}

  {if !empty($recordLanguage)}
  <tr valign="top" class="recordLanguage">
    <th>{translate text='Language'}: </th>
    <td>{foreach from=$recordLanguage item=lang}{$lang|escape}<br/>{/foreach}</td>
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
        {assign var=subject value=""}
        {foreach from=$field item=subfield name=subloop}
          {if !$smarty.foreach.subloop.first} &gt; {/if}
          {if $subject}
            {assign var=subject value="$subject $subfield"}
          {else}
            {assign var=subject value="$subfield"}
          {/if}
          <a id="subjectLink_{$smarty.foreach.loop.index}_{$smarty.foreach.subloop.index}"
            href="{$url}/Search/Results?lookfor=%22{$subject|escape:"url"}%22&amp;type=Subject"
          onmouseover="subjectHighlightOn({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});"
          onmouseout="subjectHighlightOff({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});">{$subfield|escape}</a>
        {/foreach}
        <br/>
      {/foreach}
      </div>
    </td>
  </tr>
  {/if}

  {if !empty($coreURLs) || $coreOpenURL}
  <tr valign="top" class="recordURLs">
    <th>{translate text='Online Access'}: </th>
    <td>
      {foreach from=$coreURLs item=desc key=currentUrl name=loop}
        <a href="{$currentUrl|proxify|escape}" target="_blank">{$desc|translate_prefix:'link_'|escape}</a><br/>      {/foreach}
      {if $coreOpenURL}
        {include file="Search/openurl.tpl" openUrl=$coreOpenURL}<br/>
      {/if}
    </td>
  </tr>
  {/if}

  {if $coreComponentPartCount > 0}
  <tr valign="top" class="recordComponentParts">
    <th>{translate text='component_part_count_label'}</th>
    <td><a href="{$url}/Search/Results?lookfor=host_id:{$id|escape:"url"}">{translate text='component_part_count_prefix'}{$coreComponentPartCount} {translate text='component_part_count_suffix'}</a></td>
  </tr>
  {/if}

  {if !empty($coreRecordLinks)}
  {foreach from=$coreRecordLinks item=coreRecordLink}
  <tr valign="top" class="recordLinks">
    <th>{translate text=$coreRecordLink.title}: </th>
    <td><a href="{$coreRecordLink.link|escape}">{$coreRecordLink.value|escape}</a></td>
  </tr>
  {/foreach}
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
          {translate text='No Tags'}, {translate text='Be the first to tag this record'}!
        {/if}
      </div>
    </td>
  </tr>
</table>
{* End Main Details *}

</div>

<!-- END of: RecordDrivers/Lido/core.tpl -->
