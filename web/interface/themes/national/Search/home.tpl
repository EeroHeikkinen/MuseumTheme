<!-- START of: Search/home.tpl -->

{* include file="Search/home-navigation.tpl" *} 

{include file="Search/home-content.$userLang.tpl"}

{* Search by browsing switched off for now.
   Instead of reversed condition with '!' it might be better to switch off in the settings *}

{if !$facetList}
  {include file="Search/browse.tpl"}
{/if}

<!-- END of: Search/home.tpl -->
