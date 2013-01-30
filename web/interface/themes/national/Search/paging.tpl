{if $recordCount > 0}
  {if !empty($pageLinks.pages)} 
  <div class="resultPagination">
    <div class="content">
      <div id="bottomPagination">
        <span class="paginationMove paginationBack {if !empty($pageLinks.back)}visible{/if}">{$pageLinks.back}<span>&#9668;</span></span>
        <span class="paginationPages">{$pageLinks.pages}</span>
        <span class="paginationMove paginationNext {if !empty($pageLinks.next)}visible{/if}">{$pageLinks.next}<span>&#9654;</span></span>
      </div>
    </div>
  </div>
  {/if}
{/if}
