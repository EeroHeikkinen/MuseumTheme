<!-- START of: Search/newitem-list.tpl -->

{* Main Listing *}
<div id="topFacets" class="authorbox">
  {* Recommendations *}
  {if $topRecommendations}
    {foreach from=$topRecommendations item="recommendations"}
      {include file=$recommendations}
    {/foreach}
  {/if}
 <div class="clear"></div>
</div>

<div id="resultList" class="{if $sidebarOnLeft}sidebarOnLeft last{/if}">
  {if $errorMsg || $infoMsg}
    <div class="messages">
      {if $errorMsg}<div class="error">{$errorMsg|translate}</div>{/if}
      {if $infoMsg}<div class="info">{$infoMsg|translate}</div>{/if}
    </div>
  {/if}
  {if empty($recordSet)}
        <p>{translate text="nohit_prefix"} {translate text="nohit_suffix"}</p>
  {else}
    {* Listing Options *}
    <div class="resulthead">
      <h3 style="margin:0;">{translate text='New Items'}</h3>
      <div>{if $range == 1}{translate text='Yesterday'}{else}{translate text='Past'} {$range|escape} {translate text='Days'}{/if}</div>
 
      {include file="Search/paging.tpl" position="Top"}
 
      <div class="floatright small resultOptions">
        <div class="limitSelect"> 
          {if $limitList|@count gt 1}
            <form action="{$path}/Search/LimitResults" method="post">
              <label for="limit">{translate text='Results per page'}</label>
              <select id="limit" name="limit" onChange="document.location.href = this.options[this.selectedIndex].value;">
                {foreach from=$limitList item=limitData key=limitLabel}
                  <option value="{$limitData.limitUrl|escape}"{if $limitData.selected} selected="selected"{/if}>{$limitData.desc|escape}</option>
                {/foreach}
              </select>
              <noscript><input type="submit" value="{translate text="Set"}" /></noscript>
            </form>
          {/if}
          <form action="{$path}/Search/SortResults" method="post">
            <label for="sort_options_1">{translate text='Sort'}</label>
            <select id="sort_options_1" name="sort" class="jumpMenu">
              {foreach from=$sortList item=sortData key=sortLabel}
                <option value="{$sortData.sortUrl|escape}"{if $sortData.selected} selected="selected"{/if}>{translate text=$sortData.desc}</option>
              {/foreach}
            </select>
            <noscript><input type="submit" value="{translate text="Set"}" /></noscript>
          </form>
        </div>
      </div>
      <div class="clear"></div>
    </div>
    {* End Listing Options *}

    {if $subpage}
      {include file=$subpage}
    {else}
      {$pageContent}
    {/if}

    {include file="Search/paging.tpl"}
      
    <div class="searchtools">
      <strong>{translate text='Search Tools'}:</strong>
      <a href="{$rssLink|escape}" class="feed">{translate text='Get RSS Feed'}</a>
      <a href="{$path}/Search/Email" class="mailSearch mail" title="{translate text='Email this Search'}">{translate text='Email this Search'}</a>
    </div>
  {/if}
</div>  
{* End Main Listing *}

{* Narrow Search Options *}
<div id="sidebarFacets" class="{if $sidebarOnLeft}pull-10 sidebarOnLeft{else}last{/if}">
  {if $sideRecommendations}
    {foreach from=$sideRecommendations item="recommendations"}
      {include file=$recommendations}
    {/foreach}
  {/if}
</div>
{* End Narrow Search Options *}

<div class="clear"></div>

<!-- END of: Search/newitem-list.tpl -->
