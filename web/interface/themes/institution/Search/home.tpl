<!-- START of: Search/home.tpl -->

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
    <div class="searchHomeLogo">
      <img src="{$path}/interface/themes/institution/images/finna_logo.png" alt="Finna" />
{* Slogan is not necessarily needed if it is integrated into the logo or not use at all *}
{*
      <h3 id="slogan">{translate text="searchbox_headline_text"}</h3>
*}
    </div>
    <div class="searchHomeForm">
      <div class="searchbox">
        {include file="Search/searchbox.tpl"}
      </div>
    </div>
  </div>
</div>

{include file="Search/home-navigation.tpl"} 

{include file="Search/home-content.tpl"}

{* Search by browsing switched off for now.
   Instead of reversed condition with '!' it might be better to switch off in the settings *}

{if !$facetList}
  {include file="Search/browse.tpl"}
{/if}

<!-- END of: Search/home.tpl -->
