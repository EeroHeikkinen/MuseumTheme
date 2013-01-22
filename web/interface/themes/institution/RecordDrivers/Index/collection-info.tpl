<!-- START of: RecordDrivers/Index/collection-info.tpl -->

<div id="collectionInfo" class="collectionInfo">
<table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Bibliographic Details'}">
  {if !empty($collMainAuthor)}
  <tr valign="top">
    <th>{translate text='Main Author'}: </th>
    <td><a href="{$url}/Search/Results?lookfor={$collMainAuthor|escape:"url"}&amp;type=Author">{$collMainAuthor|escape}</a></td>
  </tr>
  {/if}

  {if !empty($collCorporateAuthor)}
  <tr valign="top">
    <th>{translate text='Corporate Author'}: </th>
    <td><a href="{$url}/Search/Results?lookfor={$collCorporateAuthor|escape:"url"}&amp;type=Author">{$collCorporateAuthor|escape}</a></td>
  </tr>
  {/if}

  {*if !empty($collContributors)}
  <tr valign="top">
    <th>{translate text='Other Authors'}: </th>
    <td>
      {foreach from=$collContributors item=field name=loop}
        <a href="{$url}/Search/Results?lookfor={$field|escape:"url"}&amp;type=Author">{$field|escape}</a>{if !$smarty.foreach.loop.last}, {/if}
      {/foreach}
    </td>
  </tr>
  {/if*}

  {if !empty($collContributors)}
  <tr valign="top">
    <th>{translate text='Contributors'}: </th>
    <td>
       <dl class="">
        {foreach from=$collContributors item=field name=loop}
          {if $smarty.foreach.loop.iteration == 4}
          <dd id="moreContributors" name="moreless" style="display:none;"><a href="#" onClick="moreFacets('Contributors'); return false;">{translate text='more'} ...</a></dd>
        </dl>
        <dl class="" id="narrowGroupHidden_Contributors">
          {/if}
            <dd>
        	<a href="{$url}/Search/Results?lookfor={$field|escape:"url"}&amp;type=Author">{$field|escape}</a>
          	</dd>
        {/foreach}
        {if $smarty.foreach.loop.total > 3}<dd id="lessContributors" style="display:none;"><a href="#" onClick="lessFacets('Contributors'); return false;">{translate text='less'} ...</a></dd>{/if}
      </dl>
    </td>
  </tr>
  {/if}

  {if !empty($collSummaryAll)}
  {assign var=collElementId value='summary'}
  <tr valign="top">
    <th>{translate text='Summary'}: </th>
    <td id="{$collElementId}TD">
      <span id="{$collElementId}Full">
      {foreach from=$collSummaryAll key=sumKey item=field name=loop}
        {$field|escape}<br />{if !$smarty.foreach.loop.last}<br />{/if}
      {/foreach}
      </span>
      <a id="{$collElementId}Less" href="#" onClick="showTruncated('{$collElementId}'); return false;" style="display:none;">{translate text='Hide full summary'} ...</a>
      <span id="{$collElementId}Truncated" style="display:none;">
      {foreach from=$collSummary item=field name=loop}
      {if $smarty.foreach.loop.first}
        {$field|escape|truncate:150:"&nbsp;..."}<br>
        <a href="#" onClick="showFull('{$collElementId}'); return false;">{translate text='Show full summary'} ...</a>
      {/if}
      {/foreach}
      </span>
    </td>
  </tr>
  {/if}
  <tr valign="top">
    <th>{translate text='Format'}: </th>
    <td>
     {if is_array($recordFormat)}
      {foreach from=$recordFormat item=displayFormat name=loop}
        <span class="iconlabel {$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$displayFormat}</span>
      {/foreach}
    {else}
      <span class="iconlabel {$recordFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$recordFormat}</span>
    {/if}
    </td>
  </tr>

  {if (!empty($recordLanguage)) || (!empty($collLanguageNotes))}
  <tr valign="top">
    <th>{translate text='Language'}: </th>
    <td>{foreach from=$recordLanguage item=lang name=loop}{$lang|escape}{if !$smarty.foreach.loop.last},&nbsp;{/if}{/foreach}
    	{if (!empty($recordLanguage)) && (!empty($collLanguageNotes))}<br />Note:{/if}
        {foreach from=$collLanguageNotes item=field name=loop}
        	{$field|escape}<br />{if !$smarty.foreach.loop.last}<br />{/if}
      	{/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($collPublications)}
  <tr valign="top">
    <th>{translate text='Published'}: </th>
    <td>
      {foreach from=$collPublications item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($collEdition)}
  <tr valign="top">
    <th>{translate text='Edition'}: </th>
    <td>
      {$collEdition|escape}
    </td>
  </tr>
  {/if}

  {* Display series section if at least one series exists. *}
  {if !empty($collSeries)}
  <tr valign="top">
    <th>{translate text='Series'}: </th>
    <td>
      {foreach from=$collSeries item=field name=loop}
        {* Depending on the record driver, $field may either be an array with
           "name" and "number" keys or a flat string containing only the series
           name.  We should account for both cases to maximize compatibility. *}
        {if is_array($field)}
          {if !empty($field.name)}
            <a href="{$url}/Search/Results?lookfor=%22{$field.name|escape:"url"}%22&amp;type=Series">{$field.name|escape}</a>
            {if !empty($field.number)}
              {$field.number|escape}
            {/if}
            <br>
          {/if}
        {else}
          <a href="{$url}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Series">{$field|escape}</a><br>
        {/if}
      {/foreach}
    </td>
  </tr>
  {/if}

  {*if !empty($collSubjects)}
  <tr valign="top">
    <th>{translate text='Subjects'}: </th>
    <td>
      {foreach from=$collSubjects item=field name=loop}
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
        <br>
      {/foreach}
    </td>
  </tr>
  {/if *}

  {if !empty($collSubjects)}
  <tr valign="top">
    <th>{translate text='Subjects'}: </th>
    <td>
       <dl class="">
        {foreach from=$collSubjects item=field name=loop}
          {if $smarty.foreach.loop.iteration == 4}
          <dd id="moreSubjects" name="moreless" style="display:none;"><a href="#" onClick="moreFacets('Subjects'); return false;">{translate text='more'} ...</a></dd>
        </dl>
        <dl class="" id="narrowGroupHidden_Subjects">
          {/if}
            <dd>        {assign var=subject value=""}
        {foreach from=$field item=subfield name=subloop}
          {if !$smarty.foreach.subloop.first} &gt; {/if}
          {if $subject}
            {assign var=subject value="$subject $subfield"}
          {else}
            {assign var=subject value="$subfield"}
          {/if}
          <a href="{$url}/Search/Results?lookfor=%22{$subject|escape:"url"}%22&amp;type=Subject">{$subfield|escape}</a>
        {/foreach}</dd>
        {/foreach}
        {if $smarty.foreach.loop.total > 3}<dd id="lessSubjects" style="display:none;"><a href="#" onClick="lessFacets('Subjects'); return false;">{translate text='less'} ...</a></dd>{/if}
      </dl>
    </td>
  </tr>
  {/if}

  {if !empty($collURLs) || $collOpenURL}
  <tr valign="top">
    <th>{translate text='Online Access'}: </th>
    <td>
      {foreach from=$collURLs item=desc key=currentUrl name=loop}
        <a href="{$currentUrl|proxify|escape}" target="_blank">{$desc|translate_prefix:'link_'|escape}</a><br/>
      {/foreach}
      {if $collOpenURL}
        {include file="Search/openurl.tpl" openUrl=$collOpenURL}<br/>
      {/if}
    </td>
  </tr>
  {/if }


  {if !empty($collNotes)}
  {assign var=collElementId value='genNotes'}
  <tr valign="top">
    <th>{translate text='Notes'}: </th>
    <td id="{$collElementId}TD">
      <span id="{$collElementId}Full">
      {foreach from=$collNotes item=field key=fieldCode}
        {if ($fieldCode|strstr:"530") && ($field.u|strstr:"http://")}
          <a href="{$field.u}" target="_blank">{$field.a}</a>
        {else}
          {foreach from=$field item=subField key=subFieldCode}
            {if $subFieldCode == 'label'}
              {if $subField != 'noText'}
          	    {translate text=$subField}
              {/if}
            {else}
              {$subField}
            {/if}
          {/foreach}
        {/if}
        <br>
      {/foreach}
      </span>
      <a id="{$collElementId}Less" href="#" onClick="showTruncated('{$collElementId}'); return false;" style="display:none;">{translate text='Hide notes'} ...</a>
      <span id="{$collElementId}Truncated" style="display:none;">
      {foreach from=$collNotes item=field key=fieldCode name=loop}
      {if $smarty.foreach.loop.first}
        {if ($fieldCode|strstr:"530") && ($field.u|strstr:"http://")}
          <a href="{$field.u}" target="_blank">{$field.a|escape|truncate:150:"&nbsp;..."}</a>
        {else}
          {foreach from=$field item=subField key=subFieldCode}
            {if $subFieldCode == 'label'}
              {if $subField != 'noText'}
            	  {translate text=$subField|escape|truncate:150:"&nbsp;..."}
              {/if}
            {else}
              {$subField|escape|truncate:150:"&nbsp;..."}
            {/if}
          {/foreach}
        {/if}
        {if ($collNotes|@count > 1) || (strlen($subField)>150)}
          <br>
          <a href="#" onClick="showFull('{$collElementId}'); return false;">{translate text='Show more notes'} ...</a>
        {/if}
      {/if}

      {/foreach}

      </span>
    </td>
  </tr>
  {/if}


  {if !empty($detailed780)}
    <tr valign="top">

  {if !empty($collLinks)}
  {foreach from=$collLinks item=collLink}
  <tr valign="top">
    <th>{translate text=$collLink.title}: </th>
    <td><a href="{$collLink.link|escape}">{$collLink.value|escape}</a></td>
  </tr>
  {/foreach}
  {/if}
        <th>{translate text='Previous Title(s)'}</th>
    	<td>
    	    {foreach from=$detailed780 item=tmpLabel key=tmpTitle}
        	{$tmpLabel}:&nbsp;<a href="{$url}/Search/Results?lookfor=%22{$tmpTitle|escape:"url"}%22&amp;type=Title">{$tmpTitle|escape}</a><br>
            {/foreach}
        </td>
    </tr>
  {/if}

  {if !empty($detailed785)}
    <tr valign="top">

        <th>{translate text='Subsequent Title(s)'}</th>
    	<td>
    	    {foreach from=$detailed785 item=tmpLabel key=tmpTitle}
        	{$tmpLabel}:&nbsp;<a href="{$url}/Search/Results?lookfor=%22{$tmpTitle|escape:"url"}%22&amp;type=Title">{$tmpTitle|escape}</a><br>
            {/foreach}
        </td>
    </tr>
  {/if}

  {if !empty($collCredits)}
  {assign var=collElementId value='prodCredits'}
  <tr valign="top">
    <th>{translate text='Credits'}: </th>
    <td id="{$collElementId}TD">
      <span id="{$collElementId}Full">
      {foreach from=$collCredits item=field name=loop}
        {$field|escape}<br />
      {/foreach}
      </span>
      <a id="{$collElementId}Less" href="#" onClick="showTruncated('{$collElementId}'); return false;" style="display:none;">{translate text='Hide credits information'} ...</a>
      <span id="{$collElementId}Truncated" style="display:none;">
      {foreach from=$collCredits item=field name=loop}
      {if $smarty.foreach.loop.first}
        {$field|escape|truncate:150:"&nbsp;..."}<br>
        <a href="#" onClick="showFull('{$collElementId}'); return false;">{translate text='Show all creation/production credits information'} ...</a>
      {/if}
      {/foreach}
      </span>
    </td>
  </tr>
  {/if}

  {*if !empty($collPhysical)}
  {assign var=collElementId value='physDesc'}
  <tr valign="top">
    <th>{translate text='Physical Description'}: </th>
    <td id="{$collElementId}TD">
      <span id="{$collElementId}Full">
      {foreach from=$collPhysical item=field name=loop}
        {$field|escape}<br />
      {/foreach}
      </span>
      <a id="{$collElementId}Less" href="#" onClick="showTruncated('{$collElementId}'); return false;" style="display:none;">{translate text='Hide physical description information'} ...</a>
      <span id="{$collElementId}Truncated" style="display:none;">
      {foreach from=$collPhysical item=field name=loop}
      {if $smarty.foreach.loop.first}
        {$field|escape|truncate:150:"&nbsp;..."}<br>
        <a href="#" onClick="showFull('{$collElementId}'); return false;">{translate text='Show more physical description information'} ...</a>
      {/if}
      {/foreach}
      </span>
    </td>
  </tr>
  {/if *}

  {if !empty($collArrangement)}
  {assign var=collElementId value='arrangement'}
  <tr valign="top">
    <th>{translate text='Arrangement'}: </th>
    <td id="{$collElementId}TD">
      <span id="{$collElementId}Full">
      {foreach from=$collArrangement item=field name=loop}
        {$field|escape}<br />
      {/foreach}
      </span>
      <a id="{$collElementId}Less" href="#" onClick="showTruncated('{$collElementId}'); return false;" style="display:none;">{translate text='Hide arrangement information'} ...</a>
      <span id="{$collElementId}Truncated" style="display:none;">
      {foreach from=$collArrangement item=field name=loop}
      {if $smarty.foreach.loop.first}
        {$field|escape|truncate:150:"&nbsp;..."}<br>
        <a href="#" onClick="showFull('{$collElementId}'); return false;">{translate text='Show all arrangement information'} ...</a>
      {/if}
      {/foreach}
      </span>
    </td>
  </tr>
  {/if}

  {*if !empty($collCitationReferences)}
  {assign var=collElementId value='citationReferences'}
  <tr valign="top">
    <th>{translate text='Citations/References'}: </th>
    <td id="{$collElementId}TD">
      <span id="{$collElementId}Full">
      {foreach from=$collCitationReferences item=field name=loop}
        {$field|escape}<br />
      {/foreach}
      </span>
      <a id="{$collElementId}Less" href="#" onClick="showTruncated('{$collElementId}'); return false;" style="display:none;">{translate text='Hide citations/references information'} ...</a>
      <span id="{$collElementId}Truncated" style="display:none;">
      {foreach from=$collCitationReferences item=field name=loop}
      {if $smarty.foreach.loop.first}
        {$field|escape|truncate:150:"&nbsp;..."}<br>
        <a href="#" onClick="showFull('{$collElementId}'); return false;">{translate text='Show all citations/references information'} ...</a>
      {/if}
      {/foreach}
      </span>
    </td>
  </tr>
  {/if *}

  {*!empty($collReproductionRights)}
  {assign var=collElementId value='reproductionRights'}
  <tr valign="top">
    <th>{translate text='Rights'}: </th>
    <td id="{$collElementId}TD">
      <span id="{$collElementId}Full">
      {foreach from=$collReproductionRights item=field name=loop}
        {$field|escape}<br />{if !$smarty.foreach.loop.last}<br />{/if}
      {/foreach}
      </span>
      <a id="{$collElementId}Less" href="#" onClick="showTruncated('{$collElementId}'); return false;" style="display:none;">{translate text='Hide reproduction rights information'} ...</a>
      <span id="{$collElementId}Truncated" style="display:none;">
      {foreach from=$collReproductionRights item=field name=loop}
      {if $smarty.foreach.loop.first}
        {$field|escape|truncate:150:"&nbsp;..."}<br>
        <a href="#" onClick="showFull('{$collElementId}'); return false;">{translate text='Show all reproduction rights information'} ...</a>
      {/if}
      {/foreach}
      </span>
    </td>
  </tr>
  {/if *}

  {if !empty($collIssuingBody)}
  {assign var=collElementId value='issuingBody'}
  <tr valign="top">
    <th>{translate text='Issuing Body'}: </th>
    <td id="{$collElementId}TD">
      <span id="{$collElementId}Full">
      {foreach from=$collIssuingBody item=field name=loop}
        {$field|escape}<br />
      {/foreach}
      </span>
      <a id="{$collElementId}Less" href="#" onClick="showTruncated('{$collElementId}'); return false;" style="display:none;">{translate text='Hide issuing body information'} ...</a>
      <span id="{$collElementId}Truncated" style="display:none;">
      {foreach from=$collIssuingBody item=field name=loop}
      {if $smarty.foreach.loop.first}
        {$field|escape|truncate:150:"&nbsp;..."}<br>
        <a href="#" onClick="showFull('{$collElementId}'); return false;">{translate text='Show all issuing body information'} ...</a>
      {/if}
      {/foreach}
      </span>
    </td>
  </tr>
  {/if}

  {if !empty($collProvenance)}
  {assign var=collElementId value='issuingBody'}
  <tr valign="top">
    <th>{translate text='Provenance'}: </th>
    <td id="{$collElementId}TD">
      <span id="{$collElementId}Full">
      {foreach from=$collProvenance item=field name=loop}
        {$field|escape}<br />
      {/foreach}
      </span>
      <a id="{$collElementId}Less" href="#" onClick="showTruncated('{$collElementId}'); return false;" style="display:none;">{translate text='Hide provenance information'} ...</a>
      <span id="{$collElementId}Truncated" style="display:none;">
      {foreach from=$collProvenance item=field name=loop}
      {if $smarty.foreach.loop.first}
        {$field|escape|truncate:150:"&nbsp;..."}<br>
        <a href="#" onClick="showFull('{$collElementId}'); return false;">{translate text='Show all provenance information'} ...</a>
      {/if}
      {/foreach}
      </span>
    </td>
  </tr>
  {/if}


  {if !empty($collISBNs)}
  {* assign var=collContentDisplayed value=1 *}
  <tr valign="top">
    <th>{translate text='ISBN'}: </th>
    <td>
      {foreach from=$collISBNs item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($collISSNs)}
  {* assign var=collContentDisplayed value=1 *}
  <tr valign="top">
    <th>{translate text='ISSN'}: </th>
    <td>
      {foreach from=$collISSNs item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
</table>
</div>

<!-- END of: RecordDrivers/Index/collection-info.tpl -->
