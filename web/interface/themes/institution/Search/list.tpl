<!-- START of: Search/list.tpl -->

{* Main Listing *}
<div id="topFacets" class="span-13 authorbox" style="padding-left:0;">
  {* Recommendations *}
  {if $topRecommendations}
    {foreach from=$topRecommendations item="recommendations"}
      {include file=$recommendations}
    {/foreach}
  {/if}
</div>

<div id="resultList" class="span-10{if $sidebarOnLeft} push-3 last{/if}">
  {* Listing Options *}
  <div class="resulthead" style="padding-top:0;">
    {if $recordCount}
    <h3 style="margin:0;">{if $searchType == 'basic'}{$lookfor|escape:"html"}{/if}</h3>
    <div class="floatleft">
        {translate text="Showing"}
        <strong>{$recordStart}</strong> - <strong>{$recordEnd}</strong>
        {translate text='of'} <strong>{$recordCount}</strong>
        {if $searchType == 'basic'}{translate text='for search'}: <strong>'{$lookfor|escape:"html"}'</strong>,{/if}
      {/if}
      {translate text='query time'}: {$qtime}s
      {if $spellingSuggestions}
      <div class="correction">
        <strong>{translate text='spell_suggest'}</strong>:
        {foreach from=$spellingSuggestions item=details key=term name=termLoop}
          <br/>{$term|escape} &raquo; {foreach from=$details.suggestions item=data key=word name=suggestLoop}<a href="{$data.replace_url|escape}">{$word|escape}</a>{if $data.expand_url} <a href="{$data.expand_url|escape}"><img src="{$path}/images/silk/expand.png" alt="{translate text='spell_expand_alt'}"/></a> {/if}{if !$smarty.foreach.suggestLoop.last}, {/if}{/foreach}
        {/foreach}
      </div>
      {/if}
    </div>

    <div class="floatright">
      <div class="viewButtons">
      {if $viewList|@count gt 1}
        {foreach from=$viewList item=viewData key=viewLabel}
          {if !$viewData.selected}<a href="{$viewData.viewUrl|escape}" title="{translate text='Switch view to'} {translate text=$viewData.desc}" >{/if}<img src="{$path}/images/view_{$viewData.viewType}.png" {if $viewData.selected}title="{translate text=$viewData.desc} {translate text='view already selected'}"{/if}/>{if !$viewData.selected}</a>{/if}
        {/foreach}
      {/if}
      </div>
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
      </div>
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
    <div class="clear"></div>
  </div>
  {* End Listing Options *}

  {if $subpage}
    {include file=$subpage}
  {else}
    {$pageContent}
  {/if}

  {if $pageLinks.all}<div class="pagination">{$pageLinks.all}</div>{/if}
  <div class="searchtools">
    <strong>{translate text='Search Tools'}:</strong>
    <a href="{$rssLink|escape}" class="feed">{translate text='Get RSS Feed'}</a>
    <a href="{$url}/Search/Email" class="mailSearch mail" id="mailSearch{$searchId|escape}" title="{translate text='Email this Search'}">{translate text='Email this Search'}</a>
    {if $savedSearch}<a href="{$url}/MyResearch/SaveSearch?delete={$searchId}" class="delete">{translate text='save_search_remove'}</a>{else}<a href="{$url}/MyResearch/SaveSearch?save={$searchId}" class="add">{translate text='save_search'}</a>{/if}
  </div>
</div>
{* End Main Listing *}

{* Narrow Search Options *}
<div id="sidebarFacets" class="span-3 {if $sidebarOnLeft}pull-10 sidebarOnLeft{else}last{/if}">
  {if $sideRecommendations}
    {foreach from=$sideRecommendations item="recommendations"}
      {include file=$recommendations}
    {/foreach}
  {/if}
</div>
{* End Narrow Search Options *}

<div class="clear"></div>

<!-- END of: Search/list.tpl -->
