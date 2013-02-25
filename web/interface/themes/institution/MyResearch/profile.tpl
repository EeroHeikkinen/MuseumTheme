<!-- START of: MyResearch/profile.tpl -->

{include file="MyResearch/menu.tpl"}

{if count($pickup) > 1}
  {assign var='showHomeLibForm' value=true}
{else}
  {assign var='showHomeLibForm' value=false}
{/if}
<div class="myResearch profile{if $sidebarOnLeft} last{/if}">
  <div class="resultHead">
  {if $userMsg}
    <p class="success">{translate text=$userMsg}</p>
  {/if}
  {if $userError}
    <p class="error">{translate text=$userError}</p>
  {/if}
  </div>
  <span class="hefty">{translate text='Your Profile'}</span>
    <form method="post" action="{$url}/MyResearch/Profile" id="profile_form">
    <div class="profileInfo">
      <div class="profileGroup">
      <h4>{translate text='Local Settings'}</h4>
      </div>
      <div class="profileGroup">
        <span>{translate text='Email'}:</span><span><input type="text" name="email" value="{$email|escape}" class="{jquery_validation email='Email address is invalid'}"></input></span><br class="clear"/>
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
      
      {if $changePassword}
      <form method="post" action="{$url}/MyResearch/Profile" id="password_form">
      <div class="profileGroup">
        <h4>{translate text='change_password_title'}</h4>
        <p>{translate text='change_password_instructions'}</p>
        <br class="clear"/>
        <span>{translate text='change_password_old_password'}:</span><span><input type="password" id="oldPassword" name="oldPassword" value=""></input></span>
        <br class="clear"/>
        <span>{translate text='change_password_new_password'}:</span><span><input type="password" id="newPassword" name="newPassword" value=""></input></span>
        <br class="clear"/>
        <span>{translate text='change_password_new_password_again'}:</span><span><input type="password" id="newPassword2" name="newPassword2" value=""></input></span>
        <br class="clear"/>
        <input class="button" type="submit" value="{translate text='change_password_submit'}" />
      </div>
      </form>
      {/if}
      
      {else}
        {include file="MyResearch/catalog-login.tpl"}
      {/if}
    </div>
  </div>
  <div class="clear"></div>

  <script>
    {literal}
    $(document).ready(function() {
      $("#profile_form").validate();     
    {/literal}
    {if $changePassword}
      {literal} 
      $("#password_form").validate();
      $("#password_form input[type='password']").each(function() {
          $(this).rules("add", {
              minlength: {/literal}{$changePassword.minLength}{literal},
              maxlength: {/literal}{$changePassword.maxLength}{literal},
              messages: {
                  {/literal}minlength: jQuery.format("{translate text="Minimum length `$smarty.ldelim`0`$smarty.rdelim` characters"}"){literal},
                  {/literal}maxlength: jQuery.format("{translate text="Maximum length `$smarty.ldelim`0`$smarty.rdelim` characters"}"){literal}
              },
          });
      });
      $("#newPassword2").rules("add", {
          equalTo: '#newPassword',
          messages: {
              equalTo: "{/literal}{translate text='change_password_error_verification'}{literal}"
          }
      });
      {/literal}
    {/if}
    {literal} 
    });
    {/literal}
  </script>

<!-- END of: MyResearch/profile.tpl -->
