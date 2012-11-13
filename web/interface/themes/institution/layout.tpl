{if $smarty.request.subPage && $subTemplate}
  {include file="$module/$subTemplate"}
{else}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{* Do not use HTML comments before DOCTYPE to avoid quirks-mode in IE *} 
<!-- START of: layout.tpl -->

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$userLang}" lang="{$userLang}">

{* We should hide the top search bar and breadcrumbs in some contexts - TODO, remove xmlrecord.tpl when the actual record.tpl has been taken into use: *}
{if ($module=="Search" || $module=="Summon" || $module=="EBSCO" || $module=="PCI" || $module=="WorldCat" || $module=="Authority" || $module=="MetaLib") && $pageTemplate=="home.tpl" || $pageTemplate=="xmlrecord.tpl"}
    {assign var="showTopSearchBox" value=0}
    {assign var="showBreadcrumbs" value=0}
{else}
    {assign var="showTopSearchBox" value=1}
    {assign var="showBreadcrumbs" value=1}
{/if}

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    {if $addHeader}{$addHeader}{/if}

    <title>{$pageTitle|truncate:64:"..."}</title>
    <link rel="shortcut icon" href="{path filename="images/favicon_line.ico"}" type="image/x-icon" />
    <link rel="apple-touch-icon-precomposed" href="{path filename="images/apple-touch-icon.png"}" />

    {if $module=='Record' && $hasRDF}
    <link rel="alternate" type="application/rdf+xml" title="RDF Representation" href="{$url}/Record/{$id|escape}/RDF"/>    
    {/if}

    <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="{$url}/Search/OpenSearch?method=describe"/>

    {css media="screen, projection" filename="../js/jquery-ui-1.8.23.custom/css/smoothness/jquery-ui-1.8.23.custom.css"}

    {* Load Blueprint CSS framework *}
    {css media="screen, projection" filename="blueprint/screen.css"}
    {css media="print" filename="blueprint/print.css"}
    <!--[if lt IE 8]>{css media="screen, projection" filename="blueprint/ie.css"}<![endif]-->
    {* Adjust some default Blueprint CSS styles *}
    {css media="screen, projection" filename="blueprint/blueprint-adjust.css"}

    {* Load VuFind specific stylesheets *}
    {css media="screen" filename="ui.dynatree.css"}
    {css media="screen" filename="datatables.css"}
    
    {*  Set of css files based loosely on
        Less Framework 4 http://lessframework.com by Joni Korpi
        License: http://opensource.org/licenses/mit-license.php  *}
    {css media="screen, projection" filename="typography.css"}
    {css media="screen, projection" filename="default.css"}
    {css media="screen, projection" filename="breadcrumbs.css"}
    {css media="screen, projection" filename="home.css"}
    {css media="screen, projection" filename="footer.css"}
    {css media="screen, projection" filename="768tablet.css"}
    {css media="screen, projection" filename="320mobile.css"}
    {css media="screen, projection" filename="480mobilewide.css"}
    {css media="screen, projection" filename="default_custom.css"}
    {css media="screen, projection" filename="home_custom.css"}
    {css media="screen, projection" filename="settings.css"}
    
    {css media="print" filename="print.css"}
    <!--[if lt IE 8]>{css media="screen, projection" filename="ie.css"}<![endif]-->
    <!--[if lt IE 7]>{css media="screen, projection" filename="iepngfix/iepngfix.css"}<![endif]-->

    {* Set global javascript variables *}
    <script type="text/javascript">
    <!--//--><![CDATA[//><!--
      var path = '{$url}';
    //--><!]]>
    </script>

    {* Load jQuery framework and plugins *}
    {js filename="jquery-1.8.0.min.js"}
    {js filename="jquery-ui-1.8.23.custom/js/jquery-ui-1.8.23.custom.min.js"}
    {js filename="jquery.ui.touch-punch.min.js"}
    {js filename="jquery.form.js"}
    {js filename="jquery.metadata.js"}
    {js filename="jquery.validate.min.js"}
    {js filename="jquery.qrcode.js"}
    {js filename="jquery.dataTables.js"}   
    {js filename="jquery.clearsearch.js"}
    {js filename="jquery.collapse.js"}
    {js filename="jquery.dynatree-1.2.2-mod.js"}

    {* Load dynamic facets *}
    {js filename="facets.js"}

    {* Load javascript microtemplating *}
    {js filename="tmpl.js"}

    {* Load dialog/lightbox functions *}
    {js filename="lightbox.js"}
    
    {* Load common javascript functions *}
    {js filename="common.js"}
    
    {* Load dropdown menu modification *}
    {* js filename="dropdown.js" *}

    {* Load Mozilla Persona support *}
    {if $mozillaPersona}
    <script type="text/javascript" src="https://login.persona.org/include.js"></script>
    {js filename="persona.js"}
    {/if}

{literal}
    <script type="text/javascript">
// Long field truncation
$(document).ready(function() {
  $('.truncateField').collapse({maxLength: 150, more: "{/literal}{translate text="more"}{literal}&nbsp;»", less: "«&nbsp;{/literal}{translate text="less"}{literal}"});
{/literal}
{if $mozillaPersona}
    mozillaPersonaSetup({if $mozillaPersonaCurrentUser}"{$mozillaPersonaCurrentUser}"{else}null{/if});
{/if}
{literal}
});
{/literal}
    </script>    
    
    {* **** IE fixes **** *}
    {* Load IE CSS1 background-repeat and background-position fix *}
    <!--[if lt IE 7]>{js filename="../css/iepngfix/iepngfix_tilebg.js"}<![endif]-->
    {* Enable HTML5 in old IE - http://code.google.com/p/html5shim/
       can also use src="//html5shiv.googlecode.com/svn/trunk/html5.js" *}
    <!--[if lt IE 9]>
      {js filename="html5.js"}
    <![endif]-->

    {* For mobile devices *}
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2"/>

  </head>
  <body>
    {* mobile device button*}
    {if $mobileViewLink}
        <div class="mobileViewLink"><a href="{$mobileViewLink|escape}">{translate text="mobile_link"}</a></div>
    {/if}
    {* End mobile device button*}

    {* LightBox *}
    <div id="lightboxLoading" style="display: none;">{translate text="Loading"}...</div>
    <div id="lightboxError" style="display: none;">{translate text="lightbox_error"}</div>
    <div id="lightbox" onclick="hideLightbox(); return false;"></div>
    <div id="popupbox" class="popupBox"><b class="btop"><b></b></b></div>
    {* End LightBox *}

    <div class="container module-{$module}">
      {* Work-In-Progress disclaimer, remove when appropriate *}
      <div class="w-i-p">{translate text="development_disclaimer"}</div>
      
      <div class="breadcrumbs">
      {if $showBreadcrumbs}
        <div class="breadcrumbinner">
          <a href="{$url}">{translate text="Home"}</a> <span></span>
          {include file="$module/breadcrumbs.tpl"}
        </div>
      {/if}
        <div class="lang right">
          {if is_array($allLangs) && count($allLangs) > 1}
              <ul>
              {foreach from=$allLangs key=langCode item=langName}
                {if $userLang == $langCode}
                <li class="strong">{translate text=$langName}</li>
                {else}
                <li><a href="{$fullPath|removeURLParam:'lng'|addURLParams:"lng=$langCode"|encodeAmpersands}">{translate text=$langName}</a></li>
                {/if}
              {/foreach}
              </ul>
          {/if}
        </div>
      </div>

      <div class="header{if !$showTopSearchBox}-home{/if}{if $module!='Search'}{$module}{/if} clear">
        {include file="header.tpl"}
        <div class="clear"></div>
      </div>
      
      {if !$showTopSearchBox}
      <div class="navigationMenu navigationMenu-home">
      {include file="Search/navigation.tpl"} 
      </div>
      {/if}
      
      <div class="main{if !$showTopSearchBox}-home{/if} clear">
        {if $useSolr || $useWorldcat || $useSummon || $useEBSCO || $usePCI || $useMetaLib}
        <div id="toptab">
          <ul>
            {if $useSolr}
            <li{if $module != "WorldCat" && $module != "Summon" && $module != "EBSCO" && $module != "PCI" && $module != "MetaLib"} class="active"{/if}><a href="{$url}/Search/Results?lookfor={$lookfor|escape:"url"}">{translate text="University Library"}</a></li>
            {/if}
            {if $useWorldcat}
            <li{if $module == "WorldCat"} class="active"{/if}><a href="{$url}/WorldCat/Search?lookfor={$lookfor|escape:"url"}">{translate text="Other Libraries"}</a></li>
            {/if}
            {if $useSummon}
            <li{if $module == "Summon"} class="active"{/if}><a href="{$url}/Summon/Search?lookfor={$lookfor|escape:"url"}">{translate text="Journal Articles"}</a></li>
            {/if}
            {if $useEBSCO}
            <li{if $module == "EBSCO"} class="active"{/if}><a href="{$url}/EBSCO/Search?lookfor={$lookfor|escape:"url"}">{translate text="Journal Articles"}</a></li>
            {/if}
            {if $usePCI}
            <li{if $module == "PCI"} class="active"{/if}><a href="{$url}/PCI/Search?lookfor={$lookfor|escape:"url"}">{translate text="Journal Articles"}</a></li>
            {/if}
            {if $useMetaLib}
            <li{if $module == "MetaLib"} class="active"{/if}><a href="{$url}/MetaLib/Search?lookfor={$lookfor|escape:"url"}">{translate text="MetaLib Databases"}</a></li>
            {/if}
          </ul>
        </div>
        {/if}
        {include file="$module/$pageTemplate"}
        
		{if $showTopSearchBox}
		<div class="navigationMenu">
		  {include file="Search/navigation.tpl"} 
		</div>
		{/if}
      
        <div class="footer small clear">
          {include file="footer.tpl"}
        </div>

      </div>
    </div> {* End doc *}
{* Google Analytics, commented out - remove when/if not needed *}
{*
{literal}    
<script type="text/javascript">
  <!--//--><![CDATA[//><!--

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-28376324-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

  //--><!]]>
</script>
{/literal}
*}

{include file="piwik.tpl"}
{include file="AJAX/keepAlive.tpl"}
  </body>
</html>
{/if}

<!-- END of: layout.tpl -->
