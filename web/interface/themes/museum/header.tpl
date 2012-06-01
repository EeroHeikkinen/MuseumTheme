<!-- START of: header.tpl -->

{if $showTopSearchBox}
<div id="logoHeader" class="span-3">
  <a id="logo" href="{$url}"></a>
</div>
<div id="searchFormHeader" class="span-7">
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
{/if}

{js filename="jquery.cookie.js"}
{if $bookBag}
  {js filename="cart.js"}
  {assign var=bookBagItems value=$bookBag->getItems()}
{/if}
<div id="loginHeader" class="span-3 last right small">
<!-- 2 columns TEST
<div class="right alignright" style="width:50%; padding-right:.5em;">
-->
  {if !$hideLogin}
  <div id="logoutOptions"{if !$user} class="hide"{/if}>
    <a class="account" href="{$path}/MyResearch/Home">{translate text="Your Account"}</a> |
    <a class="logout" href="{$path}/MyResearch/Logout">{translate text="Log Out"}</a>
  </div>
  <div id="loginOptions"{if $user} class="hide"{/if}>
  {if $authMethod == 'Shibboleth'}
    <a class="login" href="{$sessionInitiator}">{translate text="Institutional Login"}</a>
    <br/><a class="" href="">{translate text="Create Account"}</a>
  {else}
    <a class="" href="{$path}/MyResearch/Home">{translate text="Login"}</a>
    <a class="right" href="">{translate text="Create Account"}</a>
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

<!-- END of: header.tpl -->
