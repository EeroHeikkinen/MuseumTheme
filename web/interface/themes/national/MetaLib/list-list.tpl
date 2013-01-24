{* check save statuses via AJAX *}
{js filename="check_save_statuses.js"}
{js filename="jquery.cookie.js"}
{include file="Search/rsi.tpl"}
{include file="Search/openurl_autocheck.tpl"}

<form method="post" name="addForm" action="{$url}/Cart/Home">
  <ul class="recordSet">
  {foreach from=$recordSet item=record name="recordLoop"}
    <li class="result{if ($smarty.foreach.recordLoop.iteration % 2) == 0} alt{/if}">
      <span class="recordNumber">{$recordStart+$smarty.foreach.recordLoop.iteration-1}</span>
      <div class="recordId" id="record{$record.ID.0|escape}">
        {* hide until complete
        <label for="checkbox_{$record.ID.0|regex_replace:'/[^a-z0-9]/':''|escape}" class="offscreen">{translate text="Select this record"}</label>
        <input id="checkbox_{$record.ID.0|regex_replace:'/[^a-z0-9]/':''|escape}" type="checkbox" name="id[]" value="{$record.ID.0|escape}" class="checkbox addToCartCheckbox"/>
         *}
        <div class="resultColumn1">
          <div class="coverDiv">
            <div class="resultNoImage"><p>{translate text='No image'}</p></div>
            <div class="resultImage"><a href="{$url}/MetaLib/Record?id={$record.ID.0|escape:"url"}"><img src="{$path}/bookcover.php?size=small{if $record.ISBN.0}&amp;isn={$record.ISBN.0|@formatISBN}{/if}{if $record.ContentType.0}&amp;contenttype={$record.ContentType.0|escape:"url"}{/if}" class="alignleft" alt="{translate text="Cover Image"}"/></a></div>
          </div>
        </div>
        
        <div class="resultColumn2">
          <div class="resultItemLine1">
            <a href="{$url}/MetaLib/Record?id={$record.ID.0|escape:"url"}"
            class="title">{if !$record.Title.0}{translate text='Title not available'}{else}{$record.Title.0|highlight}{/if}</a>
          </div>

          <div class="resultItemLine2">
            {if $record.Author}
            {translate text='by'}
            {foreach from=$record.Author item=author name="loop"}
              <a href="{$url}/MetaLib/Search?type=Author&amp;lookfor={$author|unhighlight|escape:"url"}">{$author|highlight}</a>{if !$smarty.foreach.loop.last},{/if} 
            {/foreach}
            <br/>
            {/if}

            {if $record.PublicationTitle}{translate text='Published in'} {$record.PublicationTitle.0|highlight}<br/>{/if}
            {assign var=pdxml value="PublicationDate_xml"}
            {if $record.$pdxml}({if $record.$pdxml.0.month}{$record.$pdxml.0.month|escape}/{/if}{if $record.$pdxml.0.day}{$record.$pdxml.0.day|escape}/{/if}{if $record.$pdxml.0.year}{$record.$pdxml.0.year|escape}){/if}{elseif $record.PublicationDate}{$record.PublicationDate.0|escape}{/if}
            
            {foreach from=$record.Source item=source name="sourceloop"}
              <br/>{$source} 
            {/foreach}
          </div>

          <div class="resultItemLine3">
            {if $record.Snippet}
            <blockquote>
              <span class="quotestart">&#8220;</span>{$record.Snippet.0}<span class="quoteend">&#8221;</span>
            </blockquote>
            {/if}
          </div>

          <div class="resultItemLine4">
            {foreach from=$record.url key=recordurl item=urldesc}
              <br/><a href="{if $record.proxy}{$recordurl|proxify|escape}{else}{$recordurl|escape}{/if}" class="fulltext" target="_blank" title="{$recordurl}">{if $recordurl == $urldesc}{$recordurl|truncate_url}{else}{$urldesc|translate_prefix:'link_'|escape}{/if}</a>
            {/foreach}
            {if $openUrlBase && $record.openUrl}
              {if $record.url}<br/>{/if}
              <br/>{include file="Search/openurl.tpl" openUrl=$record.openUrl}
            {/if}
          </div>

          <span class="iconlabel {$record.ContentType.0|getSummonFormatClass|escape}">{translate text=$record.ContentType.0}</span>
        </div>
      
        <div class="span-3 last addToFavLink">
          <a id="saveRecord{$record.ID.0|escape}" href="{$url}/MetaLib/Save?id={$record.ID.0|escape:"url"}" class="fav tool saveMetaLibRecord" title="{translate text='Add to favorites'}">{translate text='Add to favorites'}</a>
      
          {* Display the lists that this record is saved to *}
          <div class="savedLists info hide" id="savedLists{$record.ID.0|escape}">
            <strong>{translate text="Saved in"}:</strong>
          </div>
        </div>      
        <div class="clear"></div>
      </div>
      <span class="Z3988" title="{$record.openUrl|escape}"></span>
    </li>
  {/foreach}
  </ul>
</form>
