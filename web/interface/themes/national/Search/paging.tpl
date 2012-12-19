{if $recordCount > 0}
  <div class="pagination{if $position} pagination{$position}{/if}">
    <strong>{$recordStart}</strong>-<strong>{$recordEnd}</strong> / <strong>{$recordCount}</strong>
    {if $pageLinks.back}{$pageLinks.back}{else}<span class="pagingDisabled">{$pageLinks.pagerOptions.prevImg}</span>{/if}
    {if $pageLinks.next}{$pageLinks.next}{else}<span class="pagingDisabled">{$pageLinks.pagerOptions.nextImg}</span>{/if}
  </div>
{/if}