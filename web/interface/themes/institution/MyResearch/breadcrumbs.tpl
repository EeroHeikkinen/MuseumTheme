<!-- START of: MyResearch/breadcrumbs.tpl -->

<a href="{$url}/MyResearch/Home">{translate text='Your Account'}</a> <span></span>
{if $pageTemplate == 'view-alt.tpl'}
<em>{$pageTitle}</em>
{else}
<em>{$pageTemplate|replace:'.tpl':''|capitalize|translate}</em>
{/if}
<span></span>

<!-- END of: MyResearch/breadcrumbs.tpl -->
