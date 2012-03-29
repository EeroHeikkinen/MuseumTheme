<div class="searchHome">
  <div class="searchHomeContent">
    <div class="searchHomeLogo">
      <img src="{$path}/interface/themes/institution/images/morgan_logo.gif" alt="MORGAN - KIRJASTO, ARKISTO JA MUSEO" />
{* Slogan is not necessarily needed if it is integrated into the logo or not use at all *}

      <h3 id="slogan">{translate text="searchbox_headline_text"}</h3>

    </div>
    <div class="searchHomeForm">
      <div class="searchbox">
        {include file="Search/searchbox.tpl"}
      </div>
    </div>
{* Work-In-Progress disclaimer, remove when appropriate *}
    <span class="span-9 push-1 " style="position:absolute; top:.5em; left:0;">
      {translate text="development_disclaimer"}
    </span>
  </div>
</div>

{* Search by browsing switched off for now.
   Instead of reversed condition with '!' it might be better to switch off in the settings *}

{if !$facetList}
  {include file="Search/browse.tpl"}
{/if}

