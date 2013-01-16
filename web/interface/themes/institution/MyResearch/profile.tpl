<!-- START of: MyResearch/profile.tpl -->

{include file="MyResearch/menu.tpl"}

{if count($pickup) > 1}
  {assign var='showHomeLibForm' value=true}
{else}
  {assign var='showHomeLibForm' value=false}
{/if}
<div class="myResearch{if $sidebarOnLeft} last{/if}">
  <span class="hefty">{translate text='Your Profile'}</span>
    <form method="post" action="{$url}/MyResearch/Profile" id="profile_form">
    <div class="profileInfo">
      <div class="profileGroup">
      <h4>{translate text='Local Settings'}</h4>
      </div>
      <div class="profileGroup">
        <span>{translate text='Email'}:</span><span><input type="text" name="email" value="{$email|escape}"></input></span><br class="clear"/>
      {if $showHomeLibForm}
      </div>
      <div class="profileGroup">
        <span><label for="home_library">{translate text="Preferred Library"}:</label></span>
        {if count($pickup) > 1}
          {if $profile.home_library != ""}
            {assign var='selected' value=$profile.home_library}
          {else}
            {assign var='selected' value=$defaultPickUpLocation}
          {/if}
            <span><select id="home_library" name="home_library">
          {foreach from=$pickup item=lib name=loop}
            <option value="{$lib.locationID|escape}" {if $selected == $lib.locationID}selected="selected"{/if}>{$lib.locationDisplay|escape}</option>
          {/foreach}
        </select></span>
        {else}
          {$pickup.0.locationDisplay}
        {/if}
        </div>
      {/if}
      <div class="profileGroup">
        <input class="button" type="submit" value="{translate text='Save'}" />
      </div>
    </div>
    </form>
    <div class="clear"></div>
    
    {if $user->cat_username}
    <div class="resultHead">
    {if $userMsg}
      <p class="success">{translate text=$userMsg}</p>
    {/if}
    </div>
    <div class="profileInfo">
      <div class="profileGroup">
        <h4>{translate text='Source of information'}:
          {assign var=source value=$user->cat_username|regex_replace:'/\..*?$/':''}
          {translate text=$source prefix='source_'}
        </h4>
      </div>
      
      <div class="profileGroup">
        <span>{translate text='First Name'}:</span><span>{if $profile.firstname}{$profile.firstname|escape}{else}-{/if}</span><br class="clear"/>
        <span>{translate text='Last Name'}:</span><span>{if $profile.lastname}{$profile.lastname|escape}{else}-{/if}</span><br class="clear"/>
      </div>

      <div class="profileGroup">
        <span>{translate text='Address'} 1:</span><span>{if $profile.address1}{$profile.address1|escape}{else}-{/if}</span><br class="clear"/>
        <span>{translate text='Address'} 2:</span><span>{if $profile.address2}{$profile.address2|escape}{else}-{/if}</span><br class="clear"/>
        <span>{translate text='Zip'}:</span><span>{if $profile.zip}{$profile.zip|escape}{else}-{/if}</span><br class="clear"/>
      </div>
    
      <div class="profileGroup">
        <span>{translate text='Phone Number'}:</span><span>{if $profile.phone}{$profile.phone|escape}{else}-{/if}</span><br class="clear" />
        <span>{translate text='Email'}:</span><span>{if $info.email}{$info.email|escape}{else}-{/if}</span><br class="clear" />
      </div>
    
      <div class="profileGroup">
        <span>{translate text='Group'}:</span><span>{$profile.group|escape}</span><br class="clear"/>
      </div>
    
      <div class="profileGroup">
      {foreach from=$profile.blocks item=block name=loop}
        {if $smarty.foreach.loop.first}
          <span>{translate text='Borrowing Blocks'}:</span>
        {else}
          <span>&nbsp;</span>
        {/if}
        <span>{$block|escape}</span><br class="clear"/>
      {/foreach}
      </div>
      
      {else}
        {include file="MyResearch/catalog-login.tpl"}
      {/if}
    </div>
  </div>
  <div class="clear"></div>

<!-- END of: MyResearch/profile.tpl -->
