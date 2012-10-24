<!-- START of: MyResearch/fines.tpl -->

{include file="MyResearch/menu.tpl"}

<div class="myResearch finesList{if $sidebarOnLeft} last{/if}">
  <span class="hefty">{translate text='Your Fines'}</span>
  {if $user->cat_username}
  <div class="resultHead"></div>
    {if empty($rawFinesData)}
      {translate text='You do not have any fines'}
    {else}
      <table class="datagrid fines" summary="{translate text='Your Fines'}">
      <tr>
        <th>{translate text='Title'}</th>
        <th>{translate text='Checked Out'}</th>
        <th>{translate text='Due Date'}</th>
        <th>{translate text='Fine'}</th>
        {* <th>{translate text='Fee'}</th> *}
        <th>{translate text='Balance'}</th>
      </tr>
      {foreach from=$rawFinesData item=record}
        <tr>
          <td>
			  {if is_array($record.format)}
				{assign var=mainFormat value=$record.format.0} 
				{assign var=displayFormat value=$record.format|@end} 
			  {else}
				{assign var=mainFormat value=$record.format} 
				{assign var=displayFormat value=$record.format} 
			  {/if}
			  <span class="iconlabel format{$mainFormat|lower|regex_replace:"/[^a-z0-9]/":""} format{$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{*translate text=format_$displayFormat*}</span>
            {if empty($record.title)}
              {translate text='not_applicable'}
            {else}
              <a href="{$path}/Record/{$record.id|escape}">{$record.title|trim:'/:'|escape}</a>
            {/if}
            {* {if $record.checkedOut}<span class="highlight">{translate text="fined_work_still_on_loan"}</span>{/if} *}
          </td>
          <td>{$record.checkout|escape}</td>
          <td>{$record.duedate|escape}</td>
          <td>{$record.fine|escape}</td>
          {* <td>{$record.amount/100.00|safe_money_format|escape}</td> *}
          <td>{$record.balance/100.00|safe_money_format|escape}</td>
        </tr>
      {/foreach}
      <tr><td colspan="4" style="font-weight:bold;">{translate text='Balance'}:</td><td style="font-weight:bold;">{$sum/100.00|safe_money_format|escape}</td></tr>
      </table>
    {/if}
  {else}
    {include file="MyResearch/catalog-login.tpl"}
  {/if}
</div>
<div class="clear"></div>

<!-- END of: MyResearch/fines.tpl -->
