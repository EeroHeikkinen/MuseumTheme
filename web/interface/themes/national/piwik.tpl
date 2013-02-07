{if $piwikUrl}
  {literal}    
<!-- Piwik -->
<script type="text/javascript">
var pkBaseURL = "{/literal}{$piwikUrl}{literal}";
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try { {/literal}
  var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", {$piwikSiteId});
  piwikTracker.enableLinkTracking();
    {if ($module eq "Record")}
      {if $recordFormat}
        {if $recordFormat.1}
          {assign var=newRecordFormat value=$recordFormat.1}
        {else}
          {assign var=newRecordFormat value=$recordFormat.0}
        {/if}
  piwikTracker.setCustomVariable(1, 'RecordFormat', '{$newRecordFormat|escape:"html"}', 'page');
      {/if}
      {if $id and $coreMainAuthor and $coreShortTitle}

  piwikTracker.setCustomVariable(2, 'RecordData', '{$id|escape:"html"}|{$coreMainAuthor|escape:"html"}|{$coreShortTitle|escape:"html"}', 'page');
      {/if}
      {if $id and !$coreMainAuthor and $coreShortTitle and $coreContributors}

  piwikTracker.setCustomVariable(2, 'RecordData', '{$id|escape:"html"}|{$coreContributors.0|escape:"html"}|{$coreShortTitle|escape:"html"}', 'page');
      {/if}
      {if $id and !$coreMainAuthor and $coreShortTitle and !$coreContributors}

  piwikTracker.setCustomVariable(2, 'RecordData', '{$id|escape:"html"}|-|{$coreShortTitle|escape:"html"}', 'page');
      {/if}
      {if $coreInstitutions}

  piwikTracker.setCustomVariable(3, 'RecordInstitution', '{$coreInstitutions.0|escape:"html"}', 'page');
      {/if}
  piwikTracker.trackPageView();
    {elseif ($module eq "Search" or $module eq "MetaLib")}
      {if $filterList}
      
  piwikTracker.setCustomVariable(1, 'Facets', '{foreach from=$filterList item=filters}{foreach from=$filters item=filter}{$filter.field|escape:"html"}|{$filter.display|escape:"html"}\t{/foreach}{/foreach}', 'page');
  piwikTracker.setCustomVariable(2, 'FacetTypes', '{foreach from=$filterList item=filters}{foreach from=$filters item=filter}{$filter.field|escape:"html"}\t{/foreach}{/foreach}', 'page');
      {/if}
      {if $searchType}

  piwikTracker.setCustomVariable(3, 'SearchType', '{$searchType|escape:"html"}', 'page');
      {/if}
      
  piwikTracker.trackSiteSearch('{if $lookfor}{$lookfor|escape:"html"}{/if}', '{if $activePrefilter}{$activePrefilter|escape:"html"}{else}-{/if}'{if $recordCount}, {$recordCount|escape:"html"}{/if});
    {/if} {literal}
} catch( err ) {} {/literal}
</script>
<noscript><p><img src="{$piwikUrl}piwik.php?idsite={$piwikSiteId}" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Code -->
{/if} 
