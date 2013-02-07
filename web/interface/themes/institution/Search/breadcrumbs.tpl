<!-- START of: Search/breadcrumbs.tpl -->

{if $searchId}
<em>{translate text="Search"}{if $lookfor}: {$lookfor|escape:"html"}{/if}</em>
{elseif $pageTemplate=="newitem.tpl" || $pageTemplate=="newitem-list.tpl"}
<em>{translate text="New Items"}</em>
{elseif $pageTemplate=="tagcloud-home.tpl"}
<em>{translate text="Browse by Tag"}</em>
{elseif $pageTemplate=="view-alt.tpl"}
<em>{translate text=$subTemplate|replace:'.tpl':''|capitalize|translate}</em>
{elseif $pageTemplate!=""}
<em>{translate text=$pageTemplate|replace:'.tpl':''|capitalize|translate}</em>
{/if}

<!-- END of: Search/breadcrumbs.tpl -->
