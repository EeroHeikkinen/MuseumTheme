<!-- START of: RecordDrivers/Index/collection-record.tpl -->

{* Display Title *}
  <h1>{$collRecordShortTitle|escape}
  {if $collRecordSubtitle}{$coreSubtitle|escape}{/if}
  {if $collRecordTitleSection}{$coreTitleSection|escape}{/if}
  {* {if $collRecordTitleStatement}{$coreTitleStatement|escape}{/if} *}
  </h1>
{* End Title *}
{*link to the full page, either collection page or record page*}
{if $collCollection}
  <a href="{$url}/Collection/{$collRecordID|escape:"url"}/HierarchyTree#tabnav" class="title">{translate text="View Full Collection"}</a>
{else}
  <a href="{$url}/Record/{$collRecordID|escape:"url"}/HierarchyTree#tabnav" class="title">{translate text="View Full Record"}</a>
{/if}

<table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Bibliographic Details'}">
  {if !empty($collRecordSummary)}
  {assign var=collRecordElementId value='summary'}
  <tr valign="top">
    <th>{translate text='Description'}: </th>
    <td id="{$collRecordElementId}TD">
      <span id="{$collRecordElementId}Full">
      {foreach from=$collRecordSummary item=field name=loop}
        {$field|escape}<br />{if !$smarty.foreach.loop.last}<br />{/if}
      {/foreach}
      </span>
      <a id="{$collRecordElementId}Less" href="#" onClick="showTruncated('{$collRecordElementId}'); return false;" style="display:none;">{translate text='Hide full summary'} ...</a>
      <span id="{$collRecordElementId}Truncated" style="display:none;">
      {foreach from=$collRecordSummary item=field name=loop}
      {if $smarty.foreach.loop.first}
        {$field|escape|truncate:150:"&nbsp;..."}<br>
        <a href="#" onClick="showFull('{$collRecordElementId}'); return false;">{translate text='Show full summary'} ...</a>
      {/if}
      {/foreach}
      </span>
    </td>
  </tr>
  {/if}

  {if !empty($collRecordMainAuthor)}
  <tr valign="top">
    <th>{translate text='Main Author'}: </th>
    <td><a href="{$url}/Search/Results?lookfor={$collRecordMainAuthor|escape:"url"}&amp;type=Author">{$collRecordMainAuthor|escape}</a></td>
  </tr>
  {/if}

  {if !empty($collRecordCorporateAuthor)}
  <tr valign="top">
    <th>{translate text='Corporate Author'}: </th>
    <td><a href="{$url}/Search/Results?lookfor={$collRecordCorporateAuthor|escape:"url"}&amp;type=Author">{$collRecordCorporateAuthor|escape}</a></td>
  </tr>
  {/if}

  {if !empty($collDateDescription)}
  <tr valign="top">
    <th>{translate text='Date'}: </th>
    <td>{$collDateDescription|escape}</a></td>
  </tr>
  {/if}

  {if (!empty($recordLanguage)) || (!empty($collRecordLanguageNotes))}
  <tr valign="top">
    <th>{translate text='Language'}: </th>
    <td>{foreach from=$recordLanguage item=lang name=loop}{$lang|escape}{if !$smarty.foreach.loop.last},&nbsp;{/if}{/foreach}
        {if (!empty($recordLanguage)) && (!empty($collRecordLanguageNotes))}<br />Note:{/if}
        {foreach from=$collRecordLanguageNotes item=field name=loop}
            {$field|escape}<br />{if !$smarty.foreach.loop.last}<br />{/if}
        {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($collExtent)}
  <tr valign="top">
    <th>{translate text='Extent'}: </th>
    <td>{$collExtent|escape}</a></td>
  </tr>
  {/if}

  {if !empty($collRecordRelated)}
    {assign var=collRecordElementId value='relItems'}
    <tr valign="top">
        <th>{translate text='Related Item(s)'}:</th>
        <td id="{$collRecordElementId}TD">
        <span id="{$collRecordElementId}Full">
            {foreach from=$collRecordRelated item=field name=loop}
                {$field|escape}<br />
            {/foreach}
      </span>
      <a id="{$collRecordElementId}Less" href="#" onClick="showTruncated('{$collRecordElementId}'); return false;" style="display:none;">{translate text='Hide related items information'} ...</a>
      <span id="{$collRecordElementId}Truncated" style="display:none;">
            {foreach from=$collRecordRelated item=field name=loop}
                {if $smarty.foreach.loop.first}
                    {$field|escape|truncate:150:"&nbsp;..."}<br>
                    <a href="#" onClick="showFull('{$collRecordElementId}'); return false;">{translate text='Show all related items information'} ...</a>
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

  {if !empty($collRecordExtent)}
  <tr valign="top">
    <th>{translate text='Extent'}: </th>
    <td>
      {foreach from=$collRecordExtent item=field name=loop}
        {$field|escape}<br>
      {/foreach}
   </td>
  </tr>
  {/if}

  {if !empty($collRecordDateDescription)}
  <tr valign="top">
    <th>{translate text='Date'}: </th>
    <td>
      {foreach from=$collRecordDateDescription item=field name=loop}
        {$field|escape}<br>
      {/foreach}
   </td>
  </tr>
  {/if}

  {if !empty($collRecordBiographicalHistory)}
  <tr valign="top">
    <th>{translate text='Biographical History'}: </th>
    <td>{$collRecordDateDescription|escape}</td>
  </tr>
  {/if}

  {if !empty($collRecordAccess)}
  <tr valign="top">
    <th>{translate text='Access Conditions'}: </th>
    <td>
      {foreach from=$collRecordAccess item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($collRecordRelated)}
  <tr valign="top">
    <th>{translate text='Related Material'}: </th>
    <td>
      {foreach from=$collRecordRelated item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($collRecordPublicationNotes)}
  <tr valign="top">
    <th>{translate text='Publication Notes'}: </th>
    <td>
      {foreach from=$collRecordPublicationNotes item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($collRecordActionNotes)}
  <tr valign="top">
    <th>{translate text='Action Notes'}: </th>
    <td>
      {foreach from=$collRecordActionNotes item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($collRecordCopiesNotes)}
  <tr valign="top">
    <th>{translate text='Copy Notes'}: </th>
    <td>
      {foreach from=$collRecordCopiesNotes item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($collRecordOriginalNotes)}
  <tr valign="top">
    <th>{translate text='Original Notes'}: </th>
    <td>
      {foreach from=$collRecordOriginalNotes item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($collRecordRepositoryCode)}
  <tr valign="top">
    <th>{translate text='Repository Code'}: </th>
    <td>{$collRecordRepositoryCode|escape}</td>
  </tr>
  {/if}
</table>

<!-- END of: RecordDrivers/Index/collection-record.tpl -->
