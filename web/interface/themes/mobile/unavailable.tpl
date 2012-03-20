<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
  <head>
    <title>Library Resource Finder: {$pageTitle}</title>
    {css media="screen" filename="styles.css"}
    {css media="print" filename="print.css"}
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <script language="JavaScript" type="text/javascript">
      path = '{$path}';
    </script>
  </head>

  <body>

    <div align="center">
      <h2>{translate text="System Unavailable"}</h2>
      <p>
        {translate text="The system is currently unavailable due to system maintenance"}.
        {translate text="Please check back soon"}.
      </p>
      <p>
        {translate text="Please contact the Library Reference Department for assistance"}<br>
        <a href="mailto:{$supportEmail}">{$supportEmail}</a>
      </p>
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