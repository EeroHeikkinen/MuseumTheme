<!-- START of: MyResearch/fines.tpl -->

{include file="MyResearch/menu.tpl"}

<div class="myResearch finesList{if $sidebarOnLeft} last{/if}">
  <div class="content">
  {if $user->cat_username}
    <table class="datagrid fines" summary="{translate text='Your Fines'}">
      <caption>{translate text='Your Fines'}</caption>
    {if empty($rawFinesData)}
      <tr><td>{translate text='You do not have any fines'}</td></tr>
    {else}
      <tr>
        <th style="width:50%;">{translate text='Title'}</th>
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
			  <span class="icon format{$mainFormat|lower|regex_replace:"/[^a-z0-9]/":""} format{$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}" title="{translate text=$displayFormat prefix='format_'}">{*translate text=format_$displayFormat*}</span>
            {if empty($record.title)}
              {translate text='not_applicable'}
            {else}
              <a href="{$url}/Record/{$record.id|escape}">{$record.title|trim:'/:'|escape}</a>
            {/if}
          </td>
          <td>{$record.checkout|escape}</td>
          <td>{$record.duedate|escape}{if $record.checkedOut} <span class="highlight">{translate text="fined_work_still_on_loan"}</span>{/if}</td>
          <td>{$record.fine|escape}</td>
          {* <td>{$record.amount/100.00|safe_money_format|escape}</td> *}
          <td style="text-align:right;">{$record.balance/100.00|safe_money_format|escape}</td>
        </tr>
      {/foreach}
      <tr><td colspan="5" class="fineBalance">{translate text='Balance total'}: <span class="hefty">{$sum/100.00|safe_money_format|escape}</span></td></tr>
    {/if}
    </table>
  {else}
    {include file="MyResearch/catalog-login.tpl"}
  {/if}
  </div>
</div>
<div class="clear"></div>

<!-- END of: MyResearch/fines.tpl -->
