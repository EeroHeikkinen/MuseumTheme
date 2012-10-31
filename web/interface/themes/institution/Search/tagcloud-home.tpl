<!-- START of: Search/tagcloud-home.tpl -->

{* display tag cloud *}
<h3>{translate text='Browse by Tag'}</h3>
{if $tagCloud}
{foreach from=$tagCloud item=values key=tag}
  <span class="cloud{$values.font}">
    <a href="{$path}/Search/Results?tag={$tag|escape:"url"}">{$tag|escape}</a> ({$values.count})
  </span>
{/foreach}
{else}
<h4>{translate text='No Tags'}</h4>
{/if}

<!-- END of: Search/tagcloud-home.tpl -->
