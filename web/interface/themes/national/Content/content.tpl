<!-- START of: Content/content.tpl -->

<div class="contentHeader"><div class="content"><h1>{$title}</h1></div></div>
{foreach from=$sections item=section name=section}
  <div class="contentSection {if $smarty.foreach.section.index is odd}odd{/if}"><div class="content">{$section}</div></div>
{/foreach}

<!-- END of: Content/content.tpl -->