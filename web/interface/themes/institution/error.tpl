<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$userLang}" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>

    <title>{translate text='An error has occurred'}</title>

    <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="{$url}/Search/OpenSearch?method=describe" />
    
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

  </head>

  <body>
    <div class="container">
	  <div class="header">
          {include file="header.tpl"}
	  </div>

      {if $showBreadcrumbs}
      <div class="breadcrumbs">
        <div class="breadcrumbinner">
          <a href="{$url}">{translate text="Home"}</a> <span>&gt;</span>
          {include file="$module/breadcrumbs.tpl"}
        </div>
      </div>
      {/if}

	  <div class="main">
        <div class="error fatalError">
          <h1>{translate text="An error has occurred"}</h1>
          <p class="errorMsg">{$error->getMessage()}</p>
          {if $debug}
            <p class="errorStmt">{$error->getDebugInfo()}</p>
          {/if}
          <p>
            {translate text="Please contact the Library Reference Department for assistance"}
            <br/>
            <a href="mailto:{$supportEmail}">{$supportEmail}</a>
          </p>
          {if $debug}
          <div class="debug">
            <h2>{translate text="Debug Information"}</h2>
            {assign var=errorCode value=$error->getCode()}
            {if $errorCode}
            <p class="errorMsg">{translate text="Code"}: {$errorCode}</p>
            {/if}
            <p>{translate text="Backtrace"}:</p>
            <code>
            {foreach from=$error->backtrace item=trace}
              [{$trace.line}] {$trace.file}<br/>
            {/foreach}
            </code>
          </div>
          {/if}
        </div>
	  </div>

	  <div class="footer">
          {include file="footer.tpl"}
	  </div>
    </div>
  </body>
</html>