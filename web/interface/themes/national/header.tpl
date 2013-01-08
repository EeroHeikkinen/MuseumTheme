<!-- START of: header.tpl -->

{js filename="jquery.cookie.js"}
{if $bookBag}
  {js filename="cart.js"}
  {assign var=bookBagItems value=$bookBag->getItems()}
{/if}
<div id="headerTop">
  <a id="logo" href="{$url}" title="{translate text="Home"}"></a>
  
  <ul id="headerMenu">
    <li class="menuAbout"><span>{translate text='navigation_about_finna'}</span>
      <ul>
        <li>
          <div class="submenuHeader">Tietoa Finnasta</div>
          <div class="submenuText">Keräsimme aineistotiedot useista Suomen arkistoista, kirjastoista.</div>
        </li>
        <li>
          <div class="submenuHeader">Mukana olevat organisaatiot</div>
          <div class="submenuText">Yhdellä haulla saat tuloksia kaikista mukana olevista kokoelmista.</div>
        </li>
        <li>
          <div class="submenuHeader">Yhteystiedot</div>
          <div class="submenuText">Finna-tunnukseesi voit yhdistää useiden organisaatioiden tunnuksia.</div>
        </li>
      </ul>
    </li>
    {if $userLang != 'sv'}
      <li class="menuTips"><span>{translate text='Search Tips'}</span></li>
    {/if}
    <li class="menuFeedback"><span>{translate text='navigation_feedback'}</span></li>
    {if !$hideLogin}
      <li class="menuLogin"><span>{if $user}{translate text="Your Account"}{else}{translate text="Login"}{/if}</span>
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
    </li>
  </ul>
  <div class="lang">
    {if is_array($allLangs) && count($allLangs) > 1}
      <ul>
        {foreach from=$allLangs key=langCode item=langName}
          {if $userLang != $langCode}
            <li><a href="{$fullPath|removeURLParam:'lng'|addURLParams:"lng=$langCode"|encodeAmpersands}">
              {translate text=$langName}</a>
            </li>
          {/if}
        {/foreach}
      </ul>
    {/if}
  </div>

</div>
<div id="headerBottom">
  <div class="breadcrumbs">
    {if $showBreadcrumbs}
      <div class="breadcrumbinner">
        <a href="{$url}">{translate text="Home"}</a><span></span>
        {include file="$module/breadcrumbs.tpl"}
      </div>
    {/if}
  </div>
  {if !$showTopSearchBox}
    <div class="headerHomeContent">
      <h2>Suomen arkistojen, kirjastojen ja museoiden aarteet <span class="color-turquoise">yhdellä haulla</span></h2>
      <h3>Yli <span class="color-finnaBlue">11 280 392</span> aineistotietoa</h3>
    </div>
  {/if}
  <div id="searchFormHeader">
    <div class="searchbox">
      {if $pageTemplate != 'advanced.tpl'}
        {if $module=="Summon" || $module=="EBSCO" || $module=="PCI" 
          || $module=="WorldCat"  || $module=="Authority" || $module=="MetaLib"}
          {include file="`$module`/searchbox.tpl"}
        {else} 
          {include file="Search/searchbox.tpl"}
        {/if}
      {/if}
    </div>
  </div>
</div>
    
<!-- END of: header.tpl -->
