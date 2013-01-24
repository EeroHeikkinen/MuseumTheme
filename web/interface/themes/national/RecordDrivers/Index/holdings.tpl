<!-- START of: RecordDrivers/Index/holdings.tpl -->

{if $id|substr:0:7 == 'helmet.'}
  <br/>
  <span class="native_link">
    <a href="http://haku.helmet.fi/iii/encore/record/C|R{$id|substr:7|escape}" target="_blank">{translate text='Holdings details from'} HelMet</a><br/>
  </span>
{/if}

{if !empty($holdings)}
<h3>{translate text=$source prefix='source_'}</h3>
{/if}

{if !$hideLogin && $offlineMode != "ils-offline"}
  {if ($driverMode || $titleDriverMode) && !empty($holdings)}
    {if $showLoginMsg || $showTitleLoginMsg}
      <div class="userMsg">
        <a href="{$path}/MyResearch/Home?followup=true&followupModule=Record&followupAction={$id}">{translate text="Login"}</a> {translate text="hold_login"}
      </div>
    {/if}
    {if $user && !$user->cat_username}
      <div class="userMsg">
        <a href="{$path}/MyResearch/Profile">{translate text="Add an account to place holds"}</a>
      </div>
    {/if}
  {/if}
{/if}

{if $holdingTitleHold && $holdingTitleHold != 'block'}
    <a class="holdPlace" href="{$holdingTitleHold|escape}">{translate text="title_hold_place"}</a>
{/if}
{if $holdingTitleHold == 'block'}
    {translate text="hold_error_blocked"}
{/if}

{if !empty($holdingURLs) || $holdingsOpenURL}
  <h5>{translate text="Internet"}</h5>
  {if !empty($holdingURLs)}
    {foreach from=$holdingURLs item=desc key=currentUrl name=loop}
      <a href="{$currentUrl|proxify|escape}" target="_blank">{$desc|translate_prefix:'link_'|escape}</a><br/>
    {/foreach}
  {/if}
  {if $holdingsOpenURL}
    {include file="Search/openurl.tpl" openUrl=$holdingsOpenURL}
  {/if}
{/if}
{foreach from=$holdings item=holding key=location}
<h5>{$location|translate|escape}</h5>
<table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Holdings details from'} {translate text=$location}">
  {if $holding.0.callnumber}
  <tr>
    <th>{translate text="Call Number"}: </th>
    <td>{$holding.0.callnumber|escape}</td>
  </tr>
  {/if}
  {if $holding.0.summary}
  <tr>
    <th>{translate text="Volume Holdings"}: </th>
    <td>
      {foreach from=$holding.0.summary item=summary}
      {$summary|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  {if $holding.0.notes}
  <tr>
    <th>{translate text="Notes"}: </th>
    <td>
      {foreach from=$holding.0.notes item=data}
      {$data|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  {foreach from=$holding item=row}
    {if $row.barcode != ""}
  <tr>
    <th>{translate text="Copy"} {$row.number|escape}</th>
    <td>
      {if $row.reserve == "Y"}
      {translate text="On Reserve - Ask at Circulation Desk"}
      {elseif $row.use_unknown_message}
      <span class="unknown">{translate text="status_unknown_message"}</span>
      {else}
        {if $row.availability}
        {* Begin Available Items (Holds) *}
          <div>
           <span class="available">{translate text="Available"}</span>
          {if $row.link}
            <a class="holdPlace{if $row.check} checkRequest{/if}" href="{$row.link|escape}"><span>{if !$row.check}{translate text="Place a Hold"}{else}{translate text="Check Hold"}{/if}</span></a>
          {/if}
          {if $row.callSlipLink}
            <a class="callSlipPlace{if $row.checkCallSlip} checkCallSlipRequest{/if}" href="{$row.callSlipLink|escape}"><span>{if !$row.checkCallSlip}{translate text="Call Slip Request"}{else}{translate text="Check Call Slip Request"}{/if}</span></a>
          {/if}
          </div>
        {else}
        {* Begin Unavailable Items (Recalls) *}
          <div>
          <span class="checkedout">{translate text=$row.status prefix='status_'}</span>
          {if $row.returnDate} <span class="statusExtra">{$row.returnDate|escape}</span>{/if}
          {if $row.duedate}
          <span class="statusExtra">{translate text="Due"}: {$row.duedate|escape}</span>
          {/if}
          {if $row.requests_placed > 0}
            <span>{translate text="Requests"}: {$row.requests_placed|escape}</span>
          {/if}
          {if $row.link}
            <a class="holdPlace{if $row.check} checkRequest{/if}" href="{$row.link|escape}"><span>{if !$row.check}{translate text="Recall This"}{else}{translate text="Check Recall"}{/if}</span></a>
          {/if}
          </div>
        {/if}
      {/if}
    </td>
  </tr>
    {/if}
  {/foreach}
</table>
{/foreach}

{if $history}
<h5>{translate text="Most Recent Received Issues"}</h5>
<ul>
  {foreach from=$history item=row}
  <li>{$row.issue|escape}</li>
  {/foreach}
</ul>
{/if}

<!-- END of: RecordDrivers/Index/holdings.tpl -->
