<!-- START of: MyResearch/profile.tpl -->

{include file="MyResearch/menu.tpl"}

{if count($pickup) > 1}
  {assign var='showHomeLibForm' value=true}
{else}
  {assign var='showHomeLibForm' value=false}
{/if}
<div class="myResearch profile{if $sidebarOnLeft} last{/if}">
  <div class="content">
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
      <table class="profileGroup">
      <caption>{translate text='Local Settings'}</caption>
      <tr>
        <th>{translate text='Email'}</th><td><input type="text" name="email" value="{$email|escape}" class="{jquery_validation email='Email address is invalid'}"></input></td>
      {if $showHomeLibForm}
      </tr>
      <tr>
        <th><label for="home_library">{translate text="Preferred Library"}</label></th>
        {if count($pickup) > 1}
          {if $profile.home_library != ""}
            {assign var='selected' value=$profile.home_library}
          {else}
            {assign var='selected' value=$defaultPickUpLocation}
          {/if}
            <td><select id="home_library" name="home_library">
          {foreach from=$pickup item=lib name=loop}
            <option value="{$lib.locationID|escape}" {if $selected == $lib.locationID}selected="selected"{/if}>{$lib.locationDisplay|escape}</option>
          {/foreach}
        </select></td>
        {else}
          {$pickup.0.locationDisplay}
        {/if}
        </tr>
      {/if}
    </table>
        <input class="button buttonTurquoise" type="submit" value="{translate text='Save'}" />
    </form>
    <div class="clear"></div>
    
    {if $user->cat_username}
    <table class="profileGroup">
      <caption>
      {translate text='Source of information'}:
        {assign var=source value=$user->cat_username|regex_replace:'/\..*?$/':''}
        {translate text=$source prefix='source_'}
      </caption>
      
      <tr>
        <th>{translate text='First Name'}</th><td>{if $profile.firstname}{$profile.firstname|escape}{else}-{/if}</td>
      </tr>

      <tr>
        <th>{translate text='Last Name'}</th><td>{if $profile.lastname}{$profile.lastname|escape}{else}-{/if}</td>
      </tr>

      <tr>
        <th>{translate text='Address'} 1</th><td>{if $profile.address1}{$profile.address1|escape}{else}-{/if}</td>
      </tr>

      <tr>
        <th>{translate text='Address'} 2</th><td>{if $profile.address2}{$profile.address2|escape}{else}-{/if}</td>
      </tr>

      <tr>
        <th>{translate text='Zip'}</th><td>{if $profile.zip}{$profile.zip|escape}{else}-{/if}</td>
      </tr>
    
      <tr>
        <th>{translate text='Phone Number'}</th><td>{if $profile.phone}{$profile.phone|escape}{else}-{/if}</td>
      </tr>

      <tr>
        <th>{translate text='Email'}</th><td>{if $info.email}{$info.email|escape}{else}-{/if}</td>
      </tr>
    
      <tr>
        <th>{translate text='Group'}</th><td>{$profile.group|escape}</td>
      </tr>
    
      <tr>
      {foreach from=$profile.blocks item=block name=loop}
        {if $smarty.foreach.loop.first}
          <th>{translate text='Borrowing Blocks'}</th>
        {else}
          <th>&nbsp;</th>
        {/if}
        <td>{$block|escape}</td>
      {/foreach}
      </tr>
    </table>
      
      {if $changePassword}
      <br class="clear"/>
      <form method="post" action="{$url}/MyResearch/Profile" id="password_form">      
      <table class="profileGroup">
        <caption>{translate text='change_password_title'}</caption>
        <tr>
          <th colspan="2">{translate text='change_password_instructions'}</th>
        </tr>
        <tr>
	        <th>{translate text='change_password_old_password'}:</th>
	        <td><input type="password" id="oldPassword" name="oldPassword" value=""></input></td>
        </tr>
        <tr>
	        <th>{translate text='change_password_new_password'}:</th>
	        <td><input type="password" id="newPassword" name="newPassword" value=""></input></td>
        </tr>
        <tr>
	        <th>{translate text='change_password_new_password_again'}:</th>
	        <td><input type="password" id="newPassword2" name="newPassword2" value=""></input></td>
        </tr>
      </table>
      <input class="button buttonTurquoise" type="submit" value="{translate text='change_password_submit'}" />
      </form>
      {/if}
      
      {else}
        {include file="MyResearch/catalog-login.tpl"}
      {/if}
    </div>
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
