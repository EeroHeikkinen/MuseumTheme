<!-- START of: login-element.tpl -->

{if !$hideLogin}
  <li class="menuLogin"><a href="{$path}/MyResearch/Home">
    <span>{if $user}{if $mozillaPersonaCurrentUser}{$mozillaPersonaCurrentUser}{elseif $user->lastname}{if $user->firstname}{$user->firstname}&nbsp;{$user->lastname}{/if}{else}{translate text="Your Account"}{/if}{else}{translate text="Login"}{/if}</span></a>
    <ul>
      <li>
        {if !$hideLogin}
          {if $user}
            <div id="logoutOptions">
              <a class="account" href="{$path}/MyResearch/Home">{translate text="Your Account"}</a>
              {if $mozillaPersonaCurrentUser}
                <a id="personaLogout" class="logout" href="">{translate text="Log Out"}</a>
              {else}
                <a class="logout" href="{$path}/MyResearch/Logout">{translate text="Log Out"}</a>
              {/if}
            </div>
          {*else}
            <div id="loginOptions">
              {if $authMethod == 'Shibboleth'}
                <a class="login" href="{$sessionInitiator}">{translate text="Institutional Login"}</a>
              {else}
                <a href="{$path}/MyResearch/Home">{translate text="Login"}</a>
              {/if}
            </div> *}
          {/if}
        {/if}
      </li>
    </ul>
  </li>
{/if}

<!-- END of: login-element.tpl -->
