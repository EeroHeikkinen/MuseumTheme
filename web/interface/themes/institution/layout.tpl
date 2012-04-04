{if $smarty.request.subPage && $subTemplate}
  {include file="$module/$subTemplate"}
{else}
  {include file="layout-main.tpl"}
{/if} 
