{if $piwikUrl neq false}
  {literal}    
  <!-- Piwik -->
  <script type="text/javascript">
    var pkBaseURL = "{/literal}{$piwikUrl}{literal}";
    document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
  </script>
  <script type="text/javascript">
    try {
    var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", {/literal}{$piwikSiteId}{literal});
    piwikTracker.trackPageView();
    piwikTracker.enableLinkTracking();
    {/literal}{if $lookfor}{literal}
    piwikTracker.setCustomVariable (1, 'SearchTerms', '{/literal}{$lookfor|escape:"html"}{literal}', 'page');
    {/literal}{/if}{literal}
    } catch( err ) {}
    </script><noscript><p><img src="{/literal}{$piwikUrl}{literal}piwik.php?idsite={/literal}{$piwikSiteId}{literal}" style="border:0" alt="" /></p></noscript>
    <!-- End Piwik Tracking Code -->
  </script>
  {/literal}
{/if}
