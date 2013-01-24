<!-- START of: header-menu.fi.tpl -->

<li class="menuAbout"><a href="#"><span>{translate text='navigation_about'}</span></a>
  <ul class="subMenu">
    <li>
      <a href="{$path}/Content/about">
        <span>Tietoa Finnasta</span>
        <span>Perustietoa Finnasta ja sen sisällöistä</span>
      </a>
    </li>
    <li>
      <a href="{$path}/Content/terms_conditions">
        <span>Käyttöehdot</span>
        <span>Finnan aineistojen käyttöehdot</span>
      </a>
    </li>
    <li>
      <a href="{$path}/Content/register_details">  
        <span>Rekisteriseloste</span>
        <span>Finnan asiakasrekisterin seloste</span>
      </a>
    </li>
  </ul>
</li>

<li class="menuSearch"><a href="#"><span>{translate text='navigation_search'}</span></a>
  <ul class="subMenu">
    <li>
      <a href="{$path}/Search/History">
        <span>Hakuhistoria</span>
        <span>Istuntokohtainen hakuhistoriasi. Kirjautumalla voit tallentaa hakusi.</span>
      </a>    
      </li>
    <li>
      <a href="{$path}/Search/Advanced">
        <span>Tarkennettu haku</span>
        <span>Tarkemmat hakuehdot ja karttahaku</span>
      </a>
    </li>
    <li>
      <a href="{$path}/Content/searchhelp">
        <span>Selaa luetteloa</span>
        <span>Selaa tagien, tekijän, aiheen, genren, alueen tai aikakauden mukaan.</span>
      </a>
    </li>
  </ul>
</li>

<li class="menuHelp"><a href="#"><span>Apua</span></a>
  <ul class="subMenu">
    <li>
      <a href="{$path}/Content/register_details">
        <span>Hakuohje</span>
        <span>Yksityiskohtaiset ohjeet hakuun.</span>
      </a>
    </li>
  </ul> 
</li>

<li class="menuFeedback"><a href="{$path}/Feedback/Home"><span>{translate text='navigation_feedback'}</span></a>
<!--
  <ul class="subMenu"></ul>
-->
</li>

{if !$hideLogin}
  <li class="menuLogin"><a href="{$path}/MyResearch/Home"><span>{if $user}{translate text="Your Account"}{else}{translate text="Login"}{/if}</span></a>
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
          {else}
            <div id="loginOptions">
              {if $authMethod == 'Shibboleth'}
                <a class="login" href="{$sessionInitiator}">{translate text="Institutional Login"}</a>
              {else}
                <a href="{$path}/MyResearch/Home">{translate text="Login"}</a>
              {/if}
            </div>
          {/if}
        {/if}
      </li>
    </ul>
  </li>
{/if}

<!-- END of: header-menu.fi.tpl -->
