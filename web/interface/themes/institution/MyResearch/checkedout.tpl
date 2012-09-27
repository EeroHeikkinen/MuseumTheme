<!-- START of: MyResearch/checkedout.tpl -->

<div id="resultList" class="myResearch {if $sidebarOnLeft} push-5 last{/if}">
 <div class="resultHead">
  {if $user->cat_username}
    <h3>{translate text='Your Checked Out Items'}</h3>
    {if $blocks}
      {foreach from=$blocks item=block}
        <p class="info">{translate text=$block}</p>
      {/foreach}
    {/if}

    {if $transList}

      {if $renewForm}
      <div class="floatright">
      <form name="renewals" action="{$url}/MyResearch/CheckedOut" method="post" id="renewals">
        <div class="toolbar">
          <ul>
            <li><input type="submit" class="button renew" name="renewSelected" value="{translate text="renew_selected"}" /></li>
            <li><input type="submit" class="button renewAll" name="renewAll" value="{translate text='renew_all'}" /></li>
          </ul>
        </div>
    </div>
      {/if}

      {if $errorMsg}
        <p class="error">{translate text=$errorMsg}</p>
      {/if}
      <div class="clear"></div>
  </div>
    <ul class="recordSet">
    {foreach from=$transList item=resource name="recordLoop"}
      <li class="result{if ($smarty.foreach.recordLoop.iteration % 2) == 0} alt{/if}">
      {if $renewForm}
        <div class="resultCheckbox">
        {if $resource.ils_details.renewable && isset($resource.ils_details.renew_details)}
            <label for="checkbox_{$resource.id|regex_replace:'/[^a-z0-9]/':''|escape}" class="offscreen">{translate text="Select"} 
            {if !empty($resource.id)} 
              {$resource.title|escape}
            {else}
              {translate text='Title not available'}
            {/if}</label>
            <input type="checkbox" name="renewSelectedIDS[]" value="{$resource.ils_details.renew_details}" class="checkbox" style="margin-left: 0" id="checkbox_{$resource.id|regex_replace:'/[^a-z0-9]/':''|escape}" />
            <input type="hidden" name="renewAllIDS[]" value="{$resource.ils_details.renew_details}" />
        {/if}
        </div>
      {/if}
        <div id="record{$resource.id|escape}">
          <div class="coverDiv">
            {if $resource.isbn}
              <img src="{$path}/bookcover.php?isn={$resource.isbn|@formatISBN}&amp;size=small" class="summcover" alt="{translate text='Cover Image'}"/>
            {else}
              <img src="{$path}/bookcover.php" class="summcover" alt="{translate text='No Cover Image'}"/>
            {/if}
          </div>
          <div class="resultColumn2">
            {* If $resource.id is set, we have the full Solr record loaded and should display a link... *}
            {if !empty($resource.id)}
              <a href="{$url}/Record/{$resource.id|escape:"url"}" class="title">{$resource.title|escape}</a>
            {* If the record is not available in Solr, perhaps the ILS driver sent us a title we can show... *}
            {elseif !empty($resource.ils_details.title)}
              {$resource.ils_details.title|escape}
            {* Last resort -- indicate that no title could be found. *}
            {else}
              {translate text='Title not available'}
            {/if}
            <br/>
            {if $resource.author}
              {translate text='by'}: <a href="{$url}/Author/Home?author={$resource.author|escape:"url"}">{$resource.author|escape}</a><br/>
            {/if}
            {if $resource.tags}
              {translate text='Your Tags'}:
              {foreach from=$resource.tags item=tag name=tagLoop}
                <a href="{$url}/Search/Results?tag={$tag->tag|escape:"url"}">{$tag->tag|escape}</a>{if !$smarty.foreach.tagLoop.last},{/if}
              {/foreach}
              <br/>
            {/if}
            {if $resource.notes}
              {translate text='Notes'}: {$resource.notes|escape}<br/>
            {/if}
            {if isset($resource.format)}
              {assign var=mainFormat value=$resource.format.0} 
              {assign var=displayFormat value=$resource.format|@end} 
              <span class="iconlabel format{$mainFormat|lower|regex_replace:"/[^a-z0-9]/":""} format{$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=format_$displayFormat}</span>
              <br/>
            {/if}
            {if $resource.ils_details.volume}
              <strong>{translate text='Volume'}:</strong> {$resource.ils_details.volume|escape}
              <br />
            {/if}

            {if $resource.ils_details.publication_year}
              <strong>{translate text='Year of Publication'}:</strong> {$resource.ils_details.publication_year|escape}
              <br />
            {/if}
        </div>
        <div class="dueDate">
            {assign var="showStatus" value="show"}
            {if $renewResult[$resource.ils_details.item_id]}
              {if $renewResult[$resource.ils_details.item_id].success}
                {assign var="showStatus" value="hide"}
                <strong>{translate text='Due Date'}: {$renewResult[$resource.ils_details.item_id].new_date}</strong>
                <div class="success">{translate text='renew_success'}</div>
              {else}
                <strong>{translate text='Due Date'}: {$resource.ils_details.duedate|escape} {if $resource.ils_details.dueTime} {$resource.ils_details.dueTime|escape}{/if}</strong>
                <div class="error">{translate text='renew_fail'}{if $renewResult[$resource.ils_details.item_id].sysMessage}: {$renewResult[$resource.ils_details.item_id].sysMessage|escape}{/if}</div>
              {/if}
            {else}
              <strong>{translate text='Due Date'}: {$resource.ils_details.duedate|escape} {if $resource.ils_details.dueTime} {$resource.ils_details.dueTime|escape}{/if}</strong>
              {if $showStatus == "show"}
                {if $resource.ils_details.dueStatus == "overdue"}
                  <div class="error">{translate text="renew_item_overdue"}</div>
                {elseif $resource.ils_details.dueStatus == "due"}
                  <div class="notice">{translate text="renew_item_due"}</div>
                {/if}
              {/if}
            {/if}

            {if $showStatus == "show" && $resource.ils_details.message}
              <div class="info">{translate text=$resource.ils_details.message}</div>
            {/if}
            {if $resource.ils_details.renewable && $resource.ils_details.renew_link}
              <a href="{$resource.ils_details.renew_link|escape}">{translate text='renew_item'}</a>
            {/if}
          </div> <!-- class="dueDate" -->
          <div class="clear"></div>
        </div> <!-- record{$resource.id|escape} -->
      </li>
    {/foreach}
    </ul>
      {if $renewForm}
        </form>
      {/if}
    {else}
      <div style="clear:both;padding-top: 2em;">{translate text='You do not have any items checked out'}.</div>
    {/if}
  {else}
    {include file="MyResearch/catalog-login.tpl"}
  {/if}
</div>
</div>

<div id="sidebarFacets" class="{if $sidebarOnLeft}pull-18 sidebarOnLeft{else}last{/if}">
  {include file="MyResearch/menu.tpl"}
</div>

<div class="clear"></div>
</div>

<!-- END of: MyResearch/checkedout.tpl -->
