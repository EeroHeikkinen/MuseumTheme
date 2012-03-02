{if $componentPartsTemplate}
  <b>{translate text='Contents/Parts'}: </b>
  {include file=$componentPartsTemplate}
{else}
  {translate text="Contents/Parts unavailable"}.
{/if}
