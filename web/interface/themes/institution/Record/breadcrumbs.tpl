<!-- START of: Record/breadcrumbs.tpl -->

{if $lastsearch}
<a href="{$lastsearch|escape}#record{$id|escape:"url"}">{translate text="Search"}{if $lastsearchdisplayquery}: {$lastsearchdisplayquery|truncate:20:'...':FALSE|escape:"html"}{/if}</a> <span>&gt;</span>
{/if}
{if $breadcrumbText}
<em>{$breadcrumbText|truncate:30:"..."|escape}</em> 
{/if}
{if $subTemplate && $subTemplate != "view-dynamic-tabs.tpl"}
<span>&gt;</span><em>{$subTemplate|replace:'view-':''|replace:'.tpl':''|replace:'../MyResearch/':''|capitalize|translate}</em> 
{/if}

<!-- END of: Record/breadcrumbs.tpl -->
