<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="{$language}">
  <head>
    <title>VuFind Administration - {$pageTitle}</title>
    {css media="screen" filename="styles.css"}
    {css media="print" filename="print.css"}
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
  </head>

  <body>
  
    <div id="doc2" class="yui-t5"> <!-- Change id for page width, class for menu layout. -->

      <div id="hd">
        <!-- Your header. Could be an include. -->
        <a href="{$url}"><img src="{$path}/images/vufind.jpg" alt="vufinder"></a>
        Administration
      </div>
    
      {include file="$module/$pageTemplate"}

      <div id="ft">
      </div>
      
    </div>
    {if $piwikUrl neq false}
      {literal}    
      <!-- Piwik -->
      <script type="text/javascript">
        var pkBaseURL = "{/literal}{$piwikUrl}{literal}";
        document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
      </script>
      <script type="text/javascript">
        try {
        var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
        piwikTracker.trackPageView();
        piwikTracker.enableLinkTracking();
        } catch( err ) {}
        </script><noscript><p><img src="{/literal}{$piwikUrl}{literal}piwik.php?idsite=1" style="border:0" alt="" /></p></noscript>
        <!-- End Piwik Tracking Code -->
      </script>
      {/literal}
    {/if}
  </body>
</html>