<!-- START of: header.tpl -->

{js filename="jquery.cookie.js"}
{if $bookBag}
  {js filename="cart.js"}
  {assign var=bookBagItems value=$bookBag->getItems()}
{/if}
<div id="headerTop">
  <a id="logo" href="{$url}" title="{translate text="Home"}"></a>
  
  <ul id="headerMenu">
    <li class="menuAbout"><a href="#"><span>{translate text='navigation_about'}</span></a>
      <ul class="subMenu">
        <li>
          <a href="{$path}/Content/about">
            <span class="submenuHeader">Tietoa Finnasta</span>
            <span class="submenuText">Perustietoa Finnasta ja mukana olevat organisaatiot</span>
          </a>
        </li>
        <li>
          <a href="{$path}/Content/terms_conditions">
            <span class="submenuHeader">Käyttöehdot</span>
            <span class="submenuText">Finnan aineistojen käyttöehdot</span>
          </a>
        </li>
        <li>
          <a href="{$path}/Content/register_details">  
            <span class="submenuHeader">Rekisteriseloste</span>
            <span class="submenuText">Finna-tiedonhakuportaalin asiakasrekisterin seloste</span>
          </a>
        </li>
      </ul>
    </li>
    
    <li class="menuSearch"><a href="#"><span>{translate text='navigation_search'}</span></a>
      <ul class="subMenu">
        <li>
          <a href="{$path}/Search/History">
           <span class="submenuHeader">Hakuhistoria</span>
           <span class="submenuText">Istuntokohtainen hakuhistoriasi. Kirjautumalla voit tallentaa hakusi.</span>
          </a>    
          </li>
        <li>
          <a href="{$path}/Search/Advanced">
            <span class="submenuHeader">Tarkennettu haku</span>
            <span class="submenuText">Tarkemmat hakuehdot ja karttahaku</span>
          </a>
        </li>
        <li>
          <a href="{$path}/Content/searchhelp">
            <span class="submenuHeader">Selaa luetteloa</span>
            <span class="submenuText">Selaa tagien, tekijän, aiheen, genren, alueen tai aikakauden mukaan.</span>
          </a>
        </li>
      </ul>
    </li>
    
    <li class="menuHelp"><a href="#"><span>{translate text='navigation_help'}</span></a>
      <ul class="subMenu">
        <li>
          <a href="{$path}/Content/register_details">
            <span class="submenuHeader">Hakuohje</span>
            <span class="submenuText">Yksityiskohtaiset ohjeet hakuun.</span>
          </a>
        </li>
      </ul> 
    </li>
    
    <li class="menuFeedback"><a href="{$path}/Feedback/Home"><span>{translate text='navigation_feedback'}</span></a></li>
    
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
    <!-- /li -->
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
  {if $showBreadcrumbs}
    <div class="breadcrumbs">
      <div class="breadcrumbinner">
        <a href="{$url}">{translate text="Home"}</a><span></span>
        {include file="$module/breadcrumbs.tpl"}
      </div>
    </div>
  {/if}
  {if !$showTopSearchBox}
    <div class="headerInfoBox">
      <div class="openInfoBox toggleBox">i</div>
      <div class="closeInfoBox toggleBox">x</div>
      <div class="infoBoxText">
        <span class="strong">Hakunila horisontissa 1970-luvun puolivälissä.</span>
          Helsingin seudulle suuntautui 1960-luvun alussa muuttoaalto. Pääkaupunki 
          ei kyennyt asuttamaan suurta muuttajamäärää, mutta Espoossa ja Helsingin 
          maalaiskunnassa maata oli riittävästi. Rakennusliikkeiden kanssa solmittujen 
          aluerakentamissopimusten johdosta pystyttiin rakentamaan paljon ja nopeasti.
        <a href="/" class="more">Lue lisää <span class="moreButton">▶</span></a>
      </div>
    </div>
    <div class="headerHomeContent">
      <h2>Suomen arkistojen, kirjastojen ja museoiden aarteet <span class="color-turquoise">yhdellä haulla</span></h2><br />
      <h3>Yli <span class="color-finnaBlue">11 280 392</span> aineistotietoa!</h3>
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
