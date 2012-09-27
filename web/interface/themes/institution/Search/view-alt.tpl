<!-- START of: Search/view-alt.tpl -->

<div class="record">
  {if $lastsearch}
    <p><a href="{$lastsearch|escape}" class="backtosearch">&laquo; {translate text="Back to Search Results"}</a></p>
  {/if}
  {include file="Search/$subTemplate"}
</div>

<!-- END of: Search/view-alt.tpl -->
