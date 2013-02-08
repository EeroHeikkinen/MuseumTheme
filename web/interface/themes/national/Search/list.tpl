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
          <span class="correctionTerms">{foreach from=$details.suggestions item=data key=word name=suggestLoop}<a href="{$data.replace_url|escape}">{$word|escape}</a>{if $data.expand_url} <a class="expandSearch" title="{translate text="spell_expand_alt"}" {* alt="{translate text="spell_expand_alt"}" NOT VALID ATTRIBUTE *} href="{$data.expand_url|escape}"></a> {/if}{if !$smarty.foreach.suggestLoop.last}, {/if}{/foreach}
          </span>
        {/foreach}
      </div>
      {/if}
    </div> {* content *}
  </div> {* resultTerms *}

  <div class="resultViewOptions">
    <div class="content">
      <div class="resultNumbers">
        <span class="currentPage">{translate text="Search Results"} {$recordStart}&#8201;-&#8201;{$recordEnd} / </span>
        <span class="resultTotals">{$recordCount}</span>
      </div>
      <div class="resultOptions">
        <!--
        <div class="viewButtons">
          {if $viewList|@count gt 1}
            {foreach from=$viewList item=viewData key=viewLabel}
              {if !$viewData.selected}<a href="{$viewData.viewUrl|escape}" title="{translate text='Switch view to'} {translate text=$viewData.desc}" >{/if}<img src="{$path}/images/view_{$viewData.viewType}.png" {if $viewData.selected}title="{translate text=$viewData.desc} {translate text="view already selected"}"{/if}/>{if !$viewData.selected}</a>{/if}
            {/foreach}
          {/if}
        </div>
        -->
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
              <select class="jumpMenu" id="limit" name="limit">
                {foreach from=$limitList item=limitData key=limitLabel}
                  <option value="{$limitData.limitUrl|escape}"{if $limitData.selected} selected="selected"{/if}>{$limitData.desc|escape}</option>
                {/foreach}
              </select>
              <noscript><input type="submit" value="{translate text="Set"}" /></noscript>
            </form>
          {/if}
        </div>

      </div> {* resultOptions *}
    </div> {* content *}
  </div> {* resultViewOptions *}

  <div class="resultRecommendations">
    {* Recommendations *}
    {if $topRecommendations}
      {foreach from=$topRecommendations item="recommendations"}
        {include file=$recommendations}
      {/foreach}
    {/if}
  </div>
  <div class="resultDatesHeader {if !empty($visFacets.main_date_str[0])}expanded{/if}">
    <div class="content">
      <span class="dateVisHandle">{translate text='Results timeline'}<span class="dateVisHelp {if !empty($visFacets.main_date_str[0])}visible{/if}"><span class="infoIndicator">i</span>{translate text='You can narrow the search by selecting a period on the timeline'}</span></span>
      <div class="dateVisHandle dateVisOpen {if empty($visFacets.main_date_str[0])}visible{/if}"></div>
      <div class="dateVisHandle dateVisClose {if !empty($visFacets.main_date_str[0])}visible{/if}"></div>
    </div>
  </div>

</div> {* resultHeader *}
{* End Listing Options *}
{* Fancybox for images *}
{js filename="init_fancybox.js"}
{* Main Listing *}
<div class="resultListContainer">
  <div class="content">
    <div id="resultList" class="{if $sidebarOnLeft}sidebarOnLeft last{/if}">
      {if $subpage}
        {include file=$subpage}
      {else}
        {$pageContent}
      {/if}
    </div>
    <div id="sidebarFacets" class="{if $sidebarOnLeft}pull-10 sidebarOnLeft{else}last{/if}">
      {if $sideRecommendations}
        {foreach from=$sideRecommendations item="recommendations"}
          {include file=$recommendations}
        {/foreach}
      {/if}
    </div>
  </div>
</div>

<div class="resultSearchTools">
  <div class="content">
    <div class="searchtools">
      <ul>
        <li class="toolSavedSearch">
          {if $savedSearch}
            <span class="searchtoolsHeader"><a href="{$url}/MyResearch/SaveSearch?delete={$searchId}">{translate text='save_search_remove'}</a></span>
          {else}
            <span class="searchtoolsHeader"><a href="{$url}/MyResearch/SaveSearch?save={$searchId}">{translate text="save_search"}</a></span>
            <span class="searchtoolsText">
            </span>
          {/if}
        </li>
        <li class="toolRssLink">
          <span class="searchtoolsHeader"><a href="{$rssLink|escape}">{translate text="Get RSS Feed"}</a></span>
          <span class="searchtoolsText">
          </span>
        </li>
        <li class="toolMailSearch">
          <span class="searchtoolsHeader"><a href="{$url}/Search/Email" class="mailSearch mail" id="mailSearch{$searchId|escape}" title="{translate text='Email this Search'}">{translate text="Email this Search"}</a></span>
          <span class="searchtoolsText">
          </span>
        </li>
      </ul>  
    </div>
<!--
    </div>
-->
  </div>
</div>
          
{include file="Search/paging.tpl" position="Bottom"}

  {* End Main Listing *}
  {* Narrow Search Options *}
  {* End Narrow Search Options *}
  
<div class="clear"></div>

<!-- END of: Search/list.tpl -->
