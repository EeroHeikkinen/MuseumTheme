<!-- START of: Record/view-excerpt.tpl -->

{foreach from=$excerpts item=providerList key=provider}
  {foreach from=$providerList item=excerpt}
    <p class="summary">{$excerpt.Content}</p>
    {$excerpt.Copyright}
    <hr/>
  {/foreach}
{foreachelse}
  {translate text='No excerpts were found for this record.'}
{/foreach}

<!-- END of: Record/view-excerpt.tpl -->
