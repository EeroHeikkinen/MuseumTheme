<!-- START of: Search/tagcloud-home.tpl -->

{* display tag cloud *}
<h3>{translate text='Browse by Tag'}</h3>
{foreach from=$tagCloud item=font_sz key=tag}
  <span class="cloud{$font_sz}">
    <a href="{$path}/Search/Results?tag={$tag|escape:"url"}">{$tag|escape}</a>
  </span>
{/foreach}

<!-- END of: Search/tagcloud-home.tpl -->
