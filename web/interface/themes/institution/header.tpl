<!-- START of: header.tpl -->

{js filename="jquery.cookie.js"}
{if $bookBag}
  {js filename="cart.js"}
  {assign var=bookBagItems value=$bookBag->getItems()}
{/if}
<div id="loginHeader" class="last right small">
<!-- 2 columns TEST
<div class="right alignright" style="width:50%; padding-right:.5em;">
-->
  {if !$hideLogin}
  <div id="logoutOptions"{if !$user} class="hide"{/if}>
    <a class="account" href="{$path}/MyResearch/Home">{translate text="Your Account"}</a>
    {if $mozillaPersonaCurrentUser}
    <a id="personaLogout" class="logout" href="">{translate text="Log Out"}</a>
    {else}
    <a class="logout" href="{$path}/MyResearch/Logout">{translate text="Log Out"}</a>
    {/if}
  </div>
  <div id="loginOptions"{if $user} class="hide"{/if}>
  {if $mozillaPersona}
    <a id="personaLogin" class="persona-login" href=""><span>{translate text="Sign in with your Email"}</span></a>
  {/if}
  {if $authMethod == 'Shibboleth'}
    <a class="login" href="{$sessionInitiator}">{translate text="Institutional Login"}</a>
    {*<br/><a class="" href="">{translate text="Create Account"}</a>*}
  {else}
    <a href="{$path}/MyResearch/Home">{translate text="Login"}</a>
    {*<a href="">{translate text="Create Account"}</a>*}
<!--
    <span class="strong account">{translate text="Guest"}</span>
-->
  {/if}
  </div>
  {/if}
<!--
  {* if $bookBagItems|@count > 0 can be used below to show only when items exist but visibility needs to be taken care of somehow to show the bookbag without hitting refresh *}
  {if $bookBag} 
    <span id="cartSummary" class="cartSummary clear">
      <a id="cartItems" title="{translate text='View Book Bag'}" class="bookbag" href="{$url}/Cart/Home"><span class="strong">{$bookBagItems|@count}</span> {translate text='items'} {if $bookBag->isFull()}({translate text='bookbag_full'}){/if}</a>
      <a id="viewCart" title="{translate text='View Book Bag'}" class="viewCart bookbag offscreen" href="{$url}/Cart/Home"><span id="cartSize" class="strong">{$bookBagItems|@count}</span> {translate text='items'}<span id="cartStatus">{if $bookBag->isFull()}({translate text='bookbag_full'}){else}&nbsp;{/if}</span></a>
    </span>
  {/if}
-->
<!-- 2 columns TEST
</div>
<div style="padding-top:.5em;">
    <span class="strong account small">{translate text="Guest"}</span>
</div>
-->
</div>

{if $showTopSearchBox}

{* This is a temporary solution: assign specific id for MetaLib, all others can use the default logo *}
<div id="logoHeader{if $module=='MetaLib'}MetaLib{/if}">
  <a id="logo{if $module=='MetaLib'}MetaLib{/if}" href="{$url}{if $module=='MetaLib'}/MetaLib/Home{/if}" alt="Logo" title="{translate text="Home"}"></a>
</div>
<div id="searchFormHeader">
  <div class="searchbox">
{* Commented out for now
	<h3 class="slogan">{translate text="searchbox_headline_text"}</h3>
*}
  {if $pageTemplate != 'advanced.tpl'}
    {if $module=="Summon" || $module=="EBSCO" || $module=="PCI" || $module=="WorldCat" || $module=="Authority" || $module=="MetaLib"}
      {include file="`$module`/searchbox.tpl"}
    {else}
      {include file="Search/searchbox.tpl"}
    {/if}
  {/if}
  </div>
</div>

{else}

<div class="searchHome">
  <div class="searchHomeContent">
    {if $offlineMode == "ils-offline"}
      <div class="sysInfo">
      <h2>{translate text="ils_offline_title"}</h2>
      <p><strong>{translate text="ils_offline_status"}</strong></p>
      <p>{translate text="ils_offline_home_message"}</p>
      <p><a href="mailto:{$supportEmail}">{$supportEmail}</a></p>
      </div>
    {/if}
    <div class="searchHomeLogo{if $module=='MetaLib'}MetaLib{else}{1|rand:5}{/if}">
{* Slogan is not necessarily needed if it is integrated into the logo or not use at all *}
{*
      <h3 id="slogan">{translate text="searchbox_headline_text"}</h3>
*}
    </div>
    <div class="searchHomeForm">
      <div class="searchbox">
    {if $module=="Summon" || $module=="EBSCO" || $module=="PCI" || $module=="WorldCat" || $module=="Authority" || $module=="MetaLib"}
      {include file="`$module`/searchbox.tpl"}
    {else}
      {include file="Search/searchbox.tpl"}
    {/if}
      </div>
    </div>
  </div>
</div>

{/if}

<!-- END of: header.tpl -->
