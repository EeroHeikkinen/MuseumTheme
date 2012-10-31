{translate text="New results found for search"}:
{if empty($info.description)}{translate text="history_empty_search"}{else}{$info.description|escape}{/if}
{if $info.filters}


{translate text="Limits"}:
{foreach from=$info.filters item=filters key=field}
{foreach from=$filters item=filter name="loop"}
{if !$smarty.foreach.loop.first}

{/if}
{translate text=$field}: {$filter.display}
{/foreach}{/foreach}
{else}

{/if}

{translate text="Link to full results"}: {$info.url}

{translate text="Search Results"} {$summary.startRecord} - {$summary.endRecord} / {$info.hits}:

{foreach from=$recordSet item=record name="recordLoop"}
{$record}

{/foreach}


