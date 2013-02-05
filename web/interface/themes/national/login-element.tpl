<!-- START of: login-element.tpl -->

{if !$hideLogin}
<li class="menuLogin"><a href="{$path}/MyResearch/Home">
    <span id="userId">{if $user}{if $mozillaPersonaCurrentUser}{$mozillaPersonaCurrentUser|truncate:25:'...':true:false}{elseif $user->lastname}{if $user->firstname}{$user->firstname}&nbsp;{$user->lastname}{/if}{else}{translate text="Your Account"}{/if}{else}{translate text="Login"}{/if}</span></a>
    <ul class="subMenu" style="display:none">
{if !$hideLogin}
    {if $user}
        <li>
            <a class="account" href="{$path}/MyResearch/Home">
                <span>{translate text="Your Account"}</span>
                <span>{translate text="your_account_info"}</span>
            </a>
        </li>
        {if $mozillaPersonaCurrentUser}
        <li>
            <a id="personaLogout" class="logout" href="">
                <span>{translate text="Log Out"}</span>
                <span> </span>
            </a>
        </li>
        {else}
        <li>
            <a class="logout" href="{$path}/MyResearch/Logout">
                <span>{translate text="Log Out"}</span>
                <span> </span>
            </a>
        </li>
        {/if}
    {/if}
{/if}
    </ul>
</li>
{/if}

<!-- END of: login-element.tpl -->
