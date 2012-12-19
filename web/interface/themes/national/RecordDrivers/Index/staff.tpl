<!-- START of: RecordDrivers/Index/staff.tpl -->

<table class="citation">
  {foreach from=$details key='field' item='values'}
    <tr>
      <th>{$field|escape}</th>
      <td>
        {foreach from=$values item='value'}
          {if is_array($value)}
            {foreach from=$value key=key item=subValue}
              {$key|escape} = {$subValue|escape}<br />
            {/foreach}
          {else}
          {$value|escape}<br />
          {/if}
        {/foreach}
      </td>
    </tr>
  {/foreach}
</table>

<!-- END of: RecordDrivers/Index/staff.tpl -->
