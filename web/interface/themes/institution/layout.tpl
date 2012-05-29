<!-- START of: layout.tpl -->

{if $smarty.request.subPage && $subTemplate}
  {include file="$module/$subTemplate"}
{else}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

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
    <link rel="shortcut icon" href="{$url}/interface/themes/institution/images/favicon_line.ico" type="image/x-icon" />

    {if $module=='Record' && $hasRDF}
    <link rel="alternate" type="application/rdf+xml" title="RDF Representation" href="{$url}/Record/{$id|escape}/RDF"/>    
    {/if}

    <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="{$url}/Search/OpenSearch?method=describe"/>

    {* Load Blueprint CSS framework *}
    {css media="screen, projection" filename="blueprint/screen.css"}
    {css media="print" filename="blueprint/print.css"}
    <!--[if lt IE 8]><link rel="stylesheet" href="{$url}/interface/themes/institution/css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
    {* Adjust some default Blueprint CSS styles *}
    {css media="screen, projection" filename="blueprint/blueprint-adjust.css"}

    {* Load VuFind specific stylesheets *}
    {css media="screen, projection" filename="styles.css"}
    {css media="screen" filename="datatables.css"}
    {css media="print" filename="print.css"}
    <!--[if lt IE 8]><link rel="stylesheet" href="{$url}/interface/themes/institution/css/ie.css" type="text/css" media="screen, projection"><![endif]-->
    <!--[if lt IE 7]><link rel="stylesheet" href="{$url}/interface/themes/institution/css/iepngfix/iepngfix.css" type="text/css" media="screen, projection"><![endif]-->

    {* Set global javascript variables *}
    <script type="text/javascript">
    <!--//--><![CDATA[//><!--
      var path = '{$url}';
    //--><!]]>
    </script>

    {* Load jQuery framework and plugins *}
    {js filename="jquery-1.7.1.min.js"}
    {js filename="jquery.form.js"}
    {js filename="jquery.metadata.js"}
    {js filename="jquery.validate.min.js"}

    {* Load QRCode *}
    {js filename="jquery.qrcode.js"}

    {* Component parts *}
    {js filename="jquery.dataTables.js"}   
    
    {* Load jQuery UI *}
    {js filename="jquery-ui-1.8.7.custom/js/jquery-ui-1.8.7.custom.min.js"}
    <link rel="stylesheet" type="text/css" media="screen, projection" href="{$url}/interface/themes/institution/js/jquery-ui-1.8.7.custom/css/smoothness/jquery-ui-1.8.7.custom.css" />
        
    {* Load dialog/lightbox functions *}
    {js filename="lightbox.js"}

    {* Load common javascript functions *}
    {js filename="common.js"}

    {* **** IE fixes **** *}
    {* Load IE CSS1 background-repeat and background-position fix *}
    <!--[if lt IE 7]><script type="text/javascript" src="{$url}/interface/themes/institution/css/iepngfix/iepngfix_tilebg.js"></script><![endif]-->
    {* Enable HTML5 in old IE - http://code.google.com/p/html5shim/
       can also use src="//html5shiv.googlecode.com/svn/trunk/html5.js" *}
    <!--[if lt IE 9]>
      {js filename="html5.js"}
    <![endif]-->

    {* For mobile devices *}
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <!-- Adding "maximum-scale=1" fixes the Mobile Safari auto-zoom bug: http://filamentgroup.com/examples/iosScaleBug/ -->

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

    <div class="container">
      {* Work-In-Progress disclaimer, remove when appropriate *}
      <div class="w-i-p">{translate text="development_disclaimer"}</div>
      
      <div class="breadcrumbs">
      {if $showBreadcrumbs}
        <div class="breadcrumbinner">
          <a href="{$url}">{translate text="Home"}</a> <span>&gt;</span>
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
                <li><a 
             href="{$fullPath|removeURLParam:'lng'|addURLParams:"lng=$langCode"}">{translate text=$langName}</a></li>
                {/if}
              {/foreach}
              </ul>
          {/if}
        </div>
      </div>

      <div class="header{if !$showTopSearchBox}-home{/if} clear">
        {include file="header.tpl"}
        <div class="clear"></div>
      </div>
      
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
  </body>
</html>
{/if}

<!-- END of: layout.tpl -->
