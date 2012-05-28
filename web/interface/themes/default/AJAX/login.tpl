{if $offlineMode == "ils-offline"}
  <div class="sysInfo">
    <h2>{translate text="ils_offline_title"}</h2>
    <p><strong>{translate text="ils_offline_status"}</strong></p>
    <p>{translate text="ils_offline_login_message"}</p>
    <p><a href="mailto:{$supportEmail}">{$supportEmail}</a></p>
  </div>
{elseif $hideLogin}
  <div class="error">{translate text='login_disabled'}</div>
{/if}

{if !$hideLogin}
  {if $message}<div class="error">{$message|translate}</div>{/if}
  <form method="post" action="{$url}/MyResearch/Home" name="loginForm"
        onSubmit="SaltedLogin(this, '{$followupModule}', '{$followupAction}', '{$recordId}', null, '{$title|escape}'); {$followUp} return false;">
  <input type="hidden" name="salt" value="">
  <table class="citation">
    <tr>
      <td>{translate text='Username'}: </td>
      <td><input id="mainFocus" type="text" name="username" value="{$username|escape:"html"}" size="15"></td>
    </tr>
  
    <tr>
      <td>{translate text='Password'}: </td>
      <td><input type="password" name="password" size="15"></td>
    </tr>
  
    <tr>
      <td></td>
      <td><input type="submit" name="submit" value="{translate text='Login'}"></td>
    </tr>
  </table>
  </form>
  
  {if $authMethod == 'DB'}
  <a href="{$url}/MyResearch/Account">{translate text='Create New Account'}</a>
  {/if}
{/if}