<!-- START of: header.tpl -->

{if $bookBag}
  {js filename="cart.js"}
  {assign var=bookBagItems value=$bookBag->getItems()}
{/if}
<div id="headerTop">

  <a id="logo" href="{$url}" title="{translate text="Home"}"></a>
  <ul id="headerMenu">
    {include file="header-menu.$userLang.tpl"}
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
    <div class="openInfoBox toggleBox"></div>
    <div class="closeInfoBox toggleBox"></div>
    <div class="infoBoxText"></div>
  </div>
  <div class="headerHomeContent">
    {include file="Search/home-blurb.$userLang.tpl"}
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
