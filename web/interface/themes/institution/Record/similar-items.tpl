<div class="sidegroup">
  <h4>{translate text="Similar Items"}</h4>
  {if is_array($similarRecords)}
  <ul class="similar">
    {foreach from=$similarRecords item=similar}
    <li>
      {*{if is_array($similar.format)}
      <span class="icon format{$similar.format[0]|lower|regex_replace:"/[^a-z]/":""}">
      {else}
      <span class="icon format{$similar.format|lower|regex_replace:"/[^a-z]/":""}">
      {/if}*}
        <a href="{$url}/Record/{$similar.id|escape:"url"}" title="{$similar.title|escape}">{$similar.title|truncate:70:"..."|escape}</a>
      {*</span>*}
      <br/>
      {if $similar.author}{$similar.author|escape}{/if}
      {if $similar.publishDate} {$similar.publishDate.0|escape}{/if}
    </li>
    {/foreach}
  </ul>
  {else}
    <p>{translate text='Cannot find similar records'}</p>
  {/if}
</div>

{if is_array($editions)}
<div class="sidegroup">
  <h4>{translate text="Other Editions"}</h4>
  <ul class="similar">
    {foreach from=$editions item=edition}
    <li>
      {*{if is_array($edition.format)}
        <span class="{$edition.format[0]|lower|regex_replace:"/[^a-z0-9]/":""}">
      {else}
        <span class="{$edition.format|lower|regex_replace:"/[^a-z0-9]/":""}">
      {/if}*}
      <a href="{$url}/Record/{$edition.id|escape:"url"}" title="{$edition.title|escape}">{$edition.title|truncate:70:"..."|escape}</a>
      {*</span>*}
      <br/>
      {$edition.edition|escape}
      {if $edition.publishDate}{$edition.publishDate.0|escape}{/if}
    </li>
    {/foreach}
  </ul>
</div>
{/if}
