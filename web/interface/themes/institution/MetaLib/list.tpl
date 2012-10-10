<!-- START of: MetaLib/list.tpl -->

{js filename="openurl.js"}
{* Main Listing *}
<div class="span-18{if $sidebarOnLeft} push-5 last{/if}">
  {* Recommendations *}
  {if $topRecommendations}
    {foreach from=$topRecommendations item="recommendations"}
      {include file=$recommendations}
    {/foreach}
  {/if}

  {* Listing Options *}
  <div class="resulthead">
    <div class="floatleft">
      {if $failedDatabases}
        <p class="error">
          {translate text='Search failed in:'}<br/>
          {foreach from=$failedDatabases item=failed name=failedLoop}
            {$failed|escape}{if !$smarty.foreach.failedLoop.last}<br/>{/if}
          {/foreach}
        </p>
      {/if}
      {if $disallowedDatabases}
        <p class="notice">
          {translate text='metalib_not_authorized_results'}
          <br/>
          {foreach from=$disallowedDatabases item=failed name=failedLoop}
            {$failed|escape}{if !$smarty.foreach.failedLoop.last}<br/>{/if}
          {/foreach}
        </p>
      {/if}
      {if $recordCount}
        {if $searchType == 'basic'}{translate text='for search'}: <strong>'{$lookfor|escape:"html"}'</strong>,{/if}
      {/if}
      {if $spellingSuggestions}
      <div class="correction">
        <strong>{translate text='spell_suggest'}</strong>:
        {foreach from=$spellingSuggestions item=details key=term name=termLoop}
          <br/>{$term|escape} &raquo; {foreach from=$details.suggestions item=data key=word name=suggestLoop}<a href="{$data.replace_url|escape}">{$word|escape}</a>{if $data.expand_url} <a href="{$data.expand_url|escape}"><img src="{$path}/images/silk/expand.png" alt="{translate text='spell_expand_alt'}"/></a> {/if}{if !$smarty.foreach.termLoop.last}, {/if}{/foreach}
        {/foreach}
      </div>
      {/if}
    </div>
    {include file="Search/paging.tpl" position="Top"}

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
    {* TODO: Implement RSS <a href="{$rssLink|escape}" class="feed">{translate text='Get RSS Feed'}</a> *}
    <a href="{$url}/Search/Email" class="mailSearch mail" id="mailSearch{$searchId|escape}" title="{translate text='Email this Search'}">{translate text='Email this Search'}</a>
    {if $savedSearch}<a href="{$url}/MyResearch/SaveSearch?delete={$searchId}" class="delete">{translate text='save_search_remove'}</a>{else}<a href="{$url}/MyResearch/SaveSearch?save={$searchId}" class="add">{translate text='save_search'}</a>{/if}
  </div>
</div>
{* End Main Listing *}

{* Narrow Search Options *}
<div class="span-5 {if $sidebarOnLeft}pull-18 sidebarOnLeft{else}last{/if}">
  {if $sideRecommendations}
    {foreach from=$sideRecommendations item="recommendations"}
      {include file=$recommendations}
    {/foreach}
  {/if}
</div>
{* End Narrow Search Options *}

<div class="clear"></div>

<!-- END of: MetaLib/list.tpl -->