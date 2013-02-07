<!-- START of: Record/breadcrumbs.tpl -->

{if $lastsearch}
  {if $lastsearch|strstr:'Search/NewItem'}
<a href="{$lastsearch|escape}#record{$id|escape:"url"}">{translate text="New Items"}</a>
  {else}
<a href="{$lastsearch|escape}#record{$id|escape:"url"}">{translate text="Search"}{if $lastsearchdisplayquery}: {$lastsearchdisplayquery|truncate:20:'...':FALSE|escape:"html"}{/if}</a>
  {/if}
{/if}
<span>&gt;</span>
{if $breadcrumbText}
<em>{$breadcrumbText|truncate:30:"..."|escape}</em> 
{/if}
{if $subTemplate && !$dynamicTabs}
<span>&gt;</span><em>{$subTemplate|replace:'view-':''|replace:'.tpl':''|replace:'../MyResearch/':''|capitalize|translate}</em> 
{/if}

<!-- END of: Record/breadcrumbs.tpl -->
