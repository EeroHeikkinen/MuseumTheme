<!-- START of: MyResearch/accounts.tpl -->

{include file="MyResearch/menu.tpl"}

<div class="content myResearch accounts{if $sidebarOnLeft} last{/if}">
  <span class="hefty">{translate text='Library Cards'}</span>
  {if empty($accounts)}
    <br />
    {translate text='You do not have any library cards'}
  {else}
    <table class="datagrid accountList" summary="{translate text='Library Cards'}">
    <tr>
      <th>{translate text='Name'}</th>
      <th>{translate text='Description'}</th>
      <th>{translate text='Added'}</th>
      <th>{translate text='Source'}</th>
      <th>{translate text='Actions'}</th>
    </tr>
    {foreach from=$accounts item=account}
      <tr>
        <td>
          {$account.account_name|escape}
        </td>
        <td>
          {$account.description|truncate:50:"..."|escape}
        </td>
        <td>{$account.created|escape}</td>
        <td>
          {assign var=source value=$account.cat_username|regex_replace:'/\..*?$/':''}
          {translate text=$source prefix='source_'}
        </td>
        <td>
          <a href="{$url}/MyResearch/Accounts?edit={$account.id|escape:"url"}" class="edit tool"></a>
          <a href="{$url}/MyResearch/Accounts?delete={$account.id|escape:"url"}" class="delete tool" onclick="return confirm('{translate text='confirm_delete'}');"></a>
        </td>
      </tr>
    {/foreach}
    </table>
  {/if}
  <form method="get" action="" id="add_form">
    <input type="hidden" name="add" value="1" />
    <input class="button buttonTurquoise" type="submit" value="{translate text='Add'}..." />
  </form>  
</div>
<div class="clear"></div>

<!-- END of: MyResearch/accounts.tpl -->
