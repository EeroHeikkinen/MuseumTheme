<!-- START of: Record/view-componentparts.tpl -->

{if $componentPartsTemplate}
  {* <h5 class="recordTabHeader">{translate text='Contents/Parts'}:</h5> *}
  {include file=$componentPartsTemplate}
{else}
  {translate text="Contents/Parts unavailable"}.
{/if}

<!-- END of: Record/view-componentparts.tpl -->
