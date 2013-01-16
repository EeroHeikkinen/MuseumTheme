<!-- START of: Search/list.tpl -->

{* Listing Options *}
  <div class="resultHeader">
    <div class="resultTerms">
      <div class="content">
      {if $searchType == 'advanced'}
        <div class="advancedOptions">
          <a href="{$path}/Search/Advanced?edit={$searchId}">{translate text="Edit this Advanced Search"}</a> |
          <a href="{$path}/Search/Advanced">{translate text="Start a new Advanced Search"}</a> |
          <a href="{$path}/">{translate text="Start a new Basic Search"}</a>
        </div>
      {/if}
      <h3 class="searchTerms">
        {if $lookfor == ''}{translate text="history_empty_search"}
        {else}
          {if $searchType == 'basic'}{$lookfor|escape:"html"}
          {elseif $searchType == 'advanced'}{translate text="Your search terms"} : "{$lookfor|escape:"html"}
          {elseif ($searchType == 'advanced') || ($searchType != 'advanced' && $orFilters)}
            {foreach from=$orFilters item=values key=filter}
            AND ({foreach from=$values item=value name=orvalues}{translate text=$filter|ucfirst}:{translate text=$value prefix='facet_'}{if !$smarty.foreach.orvalues.last} OR {/if}{/foreach}){/foreach}
          {/if}
          {if $searchType == 'advanced'}"{/if}
        {/if}
      </h3>
      {if $spellingSuggestions}
        <div class="correction">
          {translate text="spell_suggest"}:
          {foreach from=$spellingSuggestions item=details key=term name=termLoop}
            <span class="correctionTerms">{foreach from=$details.suggestions item=data key=word name=suggestLoop}<a href="{$data.replace_url|escape}">{$word|escape}</a>{if $data.expand_url} <a class="expandSearch" title="{translate text="spell_expand_alt"}" alt="{translate text="spell_expand_alt"}" href="{$data.expand_url|escape}"></a> {/if}{if !$smarty.foreach.suggestLoop.last}, {/if}{/foreach}
            </span>
          {/foreach}
        </div>
      {/if}
      </div>
    </div>
    <div class="resultViewOptions">
      <div class="content">
        <div class="resultNumbers"><span class="currentPage">Sivu {if !empty($pagelinks)}{$pageLinks.pages}{else}1{/if}{$pageLinks.pages}</span>
          <span class="resultTotals">{$recordCount} tuloksesta</span>
        </div>
      <div class="resultOptions">
        <!--<div class="viewButtons">
          {if $viewList|@count gt 1}
            {foreach from=$viewList item=viewData key=viewLabel}
              {if !$viewData.selected}<a href="{$viewData.viewUrl|escape}" title="{translate text='Switch view to'} {translate text=$viewData.desc}" >{/if}<img src="{$path}/images/view_{$viewData.viewType}.png" {if $viewData.selected}title="{translate text=$viewData.desc} {translate text="view already selected"}"{/if}/>{if !$viewData.selected}</a>{/if}
            {/foreach}
          {/if}
        </div>-->
        <div class="resultOptionSort">
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
        <div class="resultOptionLimit"> 
          {if $limitList|@count gt 1}
            <form action="{$path}/Search/LimitResults" method="post">
              <label for="limit">{translate text='Results per page'}</label>
              <div class="styled_select">
              <select id="limit" name="limit" onChange="document.location.href = this.options[this.selectedIndex].value;">
                {foreach from=$limitList item=limitData key=limitLabel}
                  <option value="{$limitData.limitUrl|escape}"{if $limitData.selected} selected="selected"{/if}>{$limitData.desc|escape}</option>
                {/foreach}
              </select>
              </div>
              <noscript><input type="submit" value="{translate text="Set"}" /></noscript>
            </form>
          {/if}
        </div>
      </div>
      </div>
    </div>
    <div class="resultDates {if !empty($visFacets.main_date_str[0])}expanded{/if}">
      <div class="content">
      {* Recommendations *}
      {if $topRecommendations}
        {foreach from=$topRecommendations item="recommendations"}
          {include file=$recommendations}
        {/foreach}
      {/if}
      </div>
    </div>
    <div class="resultDatesHeader {if !empty($visFacets.main_date_str[0])}expanded{/if}">
      <div class="content">
        <span class="dateVisHandle">Tulokset aikajanalla</span>
        <div class="dateVisHandle dateVisOpen {if empty($visFacets.main_date_str[0])}visible{/if}"></div>
        <div class="dateVisHandle dateVisClose {if !empty($visFacets.main_date_str[0])}visible{/if}"></div>
      </div>
    </div>
  </div>
{* End Listing Options *}
{* Main Listing *}

  <div class="content">
    <div id="resultList" class="{if $sidebarOnLeft}sidebarOnLeft last{/if}">
      {if $subpage}
        {include file=$subpage}
      {else}
        {$pageContent}
      {/if}
    
      {include file="Search/paging.tpl"}
  
      <div class="searchtools">
        <strong>{translate text='Search Tools'}:</strong>
        <a href="{$rssLink|escape}" class="feed">{translate text="Get RSS Feed"}</a>
        <a href="{$url}/Search/Email" class="mailSearch mail" id="mailSearch{$searchId|escape}" title="{translate text='Email this Search'}">{translate text="Email this Search"}</a>
        {if $savedSearch}<a href="{$url}/MyResearch/SaveSearch?delete={$searchId}" class="delete">{translate text='save_search_remove'}</a>{else}<a href="{$url}/MyResearch/SaveSearch?save={$searchId}" class="add">{translate text="save_search"}</a>{/if}
      </div>
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
  </div>
  {* End Narrow Search Options *}
  
<div class="clear"></div>

<!-- END of: Search/list.tpl -->
