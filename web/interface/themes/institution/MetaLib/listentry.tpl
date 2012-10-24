<!-- START of: MetaLib/listentry.tpl -->
      <div class="listentry recordId" id="record{$record.ID.0|escape}">
        <div class="resultCheckbox">
        <label for="checkbox_{$record.ID.0|regex_replace:'/[^a-z0-9]/':''|escape}" class="offscreen">{translate text="Select this record"}</label>
        <input id="checkbox_{$record.ID.0|regex_replace:'/[^a-z0-9]/':''|escape}" type="checkbox" name="ids[]" value="{$record.ID.0|escape}" class="checkbox_ui"/>
        <input type="hidden" name="idsAll[]" value="{$record.ID.0|escape}" />
        </div>
        <div class="coverDiv">
          <div class="resultNoImage"><p>{translate text='No image'}</p></div>
          <div class="resultImage"><a href="{$url}/MetaLib/Record?id={$record.ID.0|escape:"url"}"><img src="{$path}/bookcover.php?size=small{if $record.ISBN.0}&amp;isn={$record.ISBN.0|@formatISBN}{/if}{if $record.ContentType.0}&amp;contenttype={$record.ContentType.0|escape:"url"}{/if}" class="summcover" alt="{translate text="Cover Image"}"/></a></div>
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

          <span class="iconlabel format{$record.ContentType.0|getSummonFormatClass|escape}">{translate text=$record.ContentType.0}</span>
        </div>
        
      {if $listEditAllowed}
        <div class="last floatright editItem">
          <a href="{$url}/MyResearch/Edit?id={$record.ID.0|escape:"url"}{if !is_null($listSelected)}&amp;list_id={$listSelected|escape:"url"}{/if}" class="edit tool"></a>
          {* Use a different delete URL if we're removing from a specific list or the overall favorites: *}
          <a
          {if is_null($listSelected)}
            href="{$url}/MyResearch/Favorites?delete={$record.ID.0|escape:"url"}"
          {else}
            href="{$url}/MyResearch/MyList/{$listSelected|escape:"url"}?delete={$record.ID.0|escape:"url"}"
          {/if}
          class="delete tool" onclick="return confirm('{translate text='confirm_delete'}');"></a>
        </div>
      {/if}
        <div class="clear"></div>
        <span class="Z3988" title="{$record.openUrl|escape}"></span>
      </div>
<!-- END of: MetaLib/listentry.tpl -->