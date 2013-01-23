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
    <link rel="shortcut icon" href="{path filename="images/favicon.ico"}" type="image/x-icon" />
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
    {css media="screen, projection" filename="default_custom.css"}
    {css media="screen, projection" filename="home.css"}
    {css media="screen, projection" filename="home_custom.css"}
    {css media="screen, projection" filename="breadcrumbs.css"}
    {css media="screen, projection" filename="footer.css"}
    {css media="screen, projection" filename="768tablet.css"}
    {css media="screen, projection" filename="480mobilewide.css"}
    {css media="screen, projection" filename="320mobile.css"}
    {css media="screen, projection" filename="settings.css"}
    
    {css media="print" filename="print.css"}
    {if $dateRangeLimit}
      {css media="screen, projection" filename="jslider/jslider.css"}
    {/if}
    {if $facetList}
      {css media="screen, projection" filename="chosen/chosen.css"}
    {/if}
    <!--[if lt IE 9]>{css media="screen, projection" filename="ie.css"}<![endif]-->
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
    {js filename="jquery.cookie.js"}
    {js filename="jquery.form.js"}
    {js filename="jquery.metadata.js"}
    {js filename="jquery.validate.min.js"}
    {js filename="jquery.qrcode.js"}
    {js filename="jquery.labelOver.js"}
    {js filename="jquery.dataTables.js"}   
    {js filename="jquery.clearsearch.js"}
    {js filename="jquery.collapse.js"}
    {js filename="jquery.dynatree-1.2.2-mod.js"}

    {* Load backgrond switcher *}
    {js filename="bg_switcher.js"}
    
    {* Load dynamic facets *}
    {js filename="facets.js"}

    {* Load javascript microtemplating *}
    {js filename="tmpl.js"}

    {* Load dialog/lightbox functions *}
    {js filename="lightbox.js"}
    
    {* Load common javascript functions *}
    {js filename="common.js"}
    
    {* Load SlidesJS *}
    {js filename="slides.min.jquery.js"}
    
    {* Load national theme functions *}
    {js filename="national.js"}
    
    {* Load QRCodes *}
    {js filename="qrcode.js"} 

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
  $('.truncateField').not('.recordSummary').collapse({maxLength: 150, more: "{/literal}{translate text="more"}{literal}&nbsp;»", less: "«&nbsp;{/literal}{translate text="less"}{literal}"});
  $('.recordSummary.truncateField').collapse({maxLength: 150, maxRows: 5, more: " ", less: " "});
{/literal}
{if $mozillaPersona}
    mozillaPersonaSetup({if $mozillaPersonaCurrentUser}"{$mozillaPersonaCurrentUser}"{else}null{/if}, {if $mozillaPersonaAutoLogout}true{else}false{/if});
{/if}
{literal}
});
{/literal}
    </script>

    {* Apply labelOver placeholder for input fields *}
    <script type="text/javascript">
    {literal}
        $(function(){
            $('#searchFormLabel').labelOver('labelOver')
            $('.mainFocus').focus();
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  </head>
  <body>
    <a class="feedbackButton" href="{$path}/Feedback/Home">{translate text='Give feedback'}</a>
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
    <div class="backgroundContainer"></div>
    <div class="container module-{$module}">

      {* Start BETA BANNER - Remove/comment out when not in beta anymore ===> *}
      {* <=== Remove/comment out when not in beta anymore - End BETA BANNER *}
      {if $developmentSite}
      <div class="w-i-p">{translate text="development_disclaimer"}</div>
      {/if}
      <!--[If lt IE 8]>
        <div class="ie">{translate text="ie_disclaimer"}</div>
      <![endif]-->
      <!--
      <div id="beta-wrapper">
          <a id="beta-banner" href="{$url}{if $module=='MetaLib'}/MetaLib/Home{/if}" title="{translate text="Home"}"></a>
      </div>
      -->

      <div id="header" class="header{if !$showTopSearchBox}-home{/if} {if $module!='Search'}header{$module}{/if} clear">
        <div class="content">{include file="header.tpl"}</div>
      </div>

      <div id="main" class="main{if !$showTopSearchBox}-home{/if} clear">
        {include file="$module/$pageTemplate"}
      </div>

      <div id="footer" class= "clear">
        <div class="content"> {include file="footer.tpl"}</div>
      </div>

    </div> {* End doc *}

    {include file="piwik.tpl"}
    {include file="AJAX/keepAlive.tpl"}
  </body>
</html>
{/if}

<!-- END of: layout.tpl -->
