<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
  <head>
    <title>{translate text="MyResearch Help"}</title>
    {css media="screen" filename="help.css"}
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
  </head>
  <body>
    {if $warning}
      <p class="warning">
        {translate text='Sorry, but the help you requested is unavailable in your language.'}
      </p>
    {/if}
    {include file="$pageTemplate"}
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
