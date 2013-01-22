{js filename="record.js"}
{js filename="openurl.js"}
<div class="span-10{if $sidebarOnLeft} push-3 last{/if}">
  <div class="toolbar">
    <ul>
      {* TODO: citations <li><a href="{$url}/MetaLib/Cite?id={$id|escape:"url"}" class="citeRecord metalibRecord cite" id="citeRecord{$id|escape}" title="{translate text="Cite this"}">{translate text="Cite this"}</a></li> *}
      <li><a href="{$url}/MetaLib/SMS?id={$id|escape:"url"}" class="smsRecord smsMetaLib sms" id="smsRecord{$id|escape}" title="{translate text="Text this"}">{translate text="Text this"}</a></li>
      <li><a href="{$url}/MetaLib/Email?id={$id|escape:"url"}" class="mailRecord mailMetaLib mail" id="mailRecord{$id|escape}" title="{translate text="Email this"}">{translate text="Email this"}</a></li>
      {* TODO: export 
      {if is_array($exportFormats) && count($exportFormats) > 0}
      <li>
        <a href="{$url}/MetaLib/Export?id={$id|escape:"url"}&amp;style={$exportFormats.0|escape:"url"}" class="export exportMenu">{translate text="Export Record"}</a>
        <ul class="menu offscreen" id="exportMenu">
        {foreach from=$exportFormats item=exportFormat}
          <li><a {if $exportFormat=="RefWorks"}target="{$exportFormat}Main" {/if}href="{$url}/MetaLib/Export?id={$id|escape:"url"}&amp;style={$exportFormat|escape:"url"}">{translate text="Export to"} {$exportFormat|escape}</a></li>
        {/foreach}
        </ul>
      </li>
      {/if}
      *}
      <li id="saveLink"><a href="{$url}/MetaLib/Save?id={$id|escape:"url"}" class="saveMetaLibRecord metalibRecord fav" id="saveRecord{$id|escape}" title="{translate text="Add to favorites"}">{translate text="Add to favorites"}</a></li>
    </ul>
    <div class="clear"></div>
  </div>

  <div class="record recordId" id="record{$id|escape}">

    <div class="alignright"><span class="{$record.ContentType.0|replace:" ":""|escape}">{$record.ContentType.0|escape}</span></div>

    {* Display Title *}
    <h1>{$record.Title.0|escape}</h1>
    {* End Title *}

    {* Display Cover Image *}
    <div class="alignleft">
      <img alt="{translate text='Cover Image'}" src="{$path}/bookcover.php?size=small{if $record.ISBN.0}&amp;isn={$record.ISBN.0|@formatISBN}{/if}{if $record.ContentType.0}&amp;contenttype={$record.ContentType.0|escape:"url"}{/if}"/>
    </div>
    {* End Cover Image *}
    
    {* Display Abstract/Snippet *}
    {if $record.Abstract}
      <p class="snippet">{$record.Abstract.0|escape}</p>
    {elseif $record.Snippet.0 != ""}
      <blockquote>
        <span class="quotestart">&#8220;</span>{$record.Snippet.0|escape}<span class="quoteend">&#8221;</span>
      </blockquote>
    {/if}

    {* Display Main Details *}
    <table cellpadding="2" cellspacing="0" border="0" class="citation">
    
    {if $record.Author}
      <tr valign="top">
        <th>{translate text='Author'}: </th>
        <td>
      {foreach from=$record.Author item="author" name="loop"}
          <a href="{$url}/MetaLib/Search?type=Author&amp;lookfor={$author|escape:"url"}">{$author|escape}</a>{if !$smarty.foreach.loop.last},{/if} 
      {/foreach}
        </td>
      </tr>
    {/if}

    {if $record.AdditionalAuthors}
      <tr valign="top">
        <th>{translate text='Other Authors'}: </th>
        <td>
      {foreach from=$record.AdditionalAuthors item="author" name="loop"}
          {$author|escape}{if !$smarty.foreach.loop.last},{/if} 
      {/foreach}
        </td>
      </tr>
    {/if}

    {if $record.PublicationTitle}
      <tr valign="top">
        <th>{translate text='Publication'}: </th>
        <td>{$record.PublicationTitle.0|escape}</td>
      </tr>
    {/if}

    {assign var=pdxml value="PublicationDate_xml"}
    {if $record.$pdxml || $record.PublicationDate}
      <tr valign="top">
        <th>{translate text='Published'}: </th>
        <td>
      {if $record.$pdxml}
        {if $record.$pdxml.0.month}{$record.$pdxml.0.month|escape}/{/if}{if $record.$pdxml.0.day}{$record.$pdxml.0.day|escape}/{/if}{if $record.$pdxml.0.year}{$record.$pdxml.0.year|escape}{/if}
        {else}
          {$record.PublicationDate.0|escape}
        {/if}
        </td>
      </tr>
      {/if}

      {if $record.ISSN}
      <tr valign="top">
        <th>{translate text='ISSN'}: </th>
        <td>
        {foreach from=$record.ISSN item="value"}
          {$value|escape}<br/>
        {/foreach}
        </td>
      </tr>
      {/if}
      
      {if $record.RelatedAuthor}
      <tr valign="top">
        <th>{translate text='Related Author'}: </th>
        <td>
        {foreach from=$record.RelatedAuthor item="author"}
          {$author|escape}
        {/foreach}
        </td>
      </tr>
      {/if}

      {if $record.Language}
      <tr valign="top">
        <th>{translate text='Language'}: </th>
        <td>{$record.Language.0|escape}</td>
      </tr>
      {/if}

      {if $record.SubjectTerms}
      <tr valign="top">
        <th>{translate text='Subjects'}: </th>
        <td>
        {foreach from=$record.SubjectTerms item=field name=loop}
          {$field|escape}<br/>
        {/foreach}
        </td>
      </tr>
      {/if}

      {foreach from=$record.Notes item=field name=loop}
      <tr valign="top">
        <th>{if $smarty.foreach.loop.first}{translate text='Notes'}:{/if}</th>
        <td>{$field|escape}</td>
      </tr>
      {/foreach}

      {if $record.Source}
      <tr valign="top">
        <th>{translate text='Source'}: </th>
        <td>{$record.Source.0|escape}</td>
      </tr>
      {/if}

      {foreach from=$record.url key=recordurl item=urldesc}
      <tr valign="top">
        <th></th>
        <td><a href="{if $record.proxy}{$recordurl|proxify|escape}{else}{$recordurl|escape}{/if}" class="fulltext" target="_blank">{$urldesc|translate_prefix:'link_'|escape}</a></td>
      </tr>
      {/foreach}
      {if $openUrlBase && $record.openUrl}
      <tr valign="top">
        <th></th>
        <td>{include file="Search/openurl.tpl" openUrl=$record.openUrl}</td>
      </tr>
        {include file="Search/rsi.tpl"}
        {include file="Search/openurl_autocheck.tpl"}
      {/if}

    </table>
    {* End Main Details *}
    
  </div>
  {* End Record *} 
  
  {* Add COINS *}  
  <span class="Z3988" title="{$record.openUrl|escape}"></span>
</div>

<div class="span-3 {if $sidebarOnLeft}pull-10 sidebarOnLeft{else}last{/if}">
  {if $bXEnabled}
    {include file="Record/bx.tpl"}
  {/if}
</div>

<div class="clear"></div>
