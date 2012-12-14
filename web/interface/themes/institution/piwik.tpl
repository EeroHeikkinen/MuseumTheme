{if $piwikUrl neq false}
    {literal}    
    <!-- Piwik -->
    <script type="text/javascript">
        var pkBaseURL = "{/literal}{$piwikUrl}{literal}";
        document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
    try {
        var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 
            {/literal}{$piwikSiteId}{literal});
        piwikTracker.enableLinkTracking();{/literal}
        {if ($module eq "Record")}
            {if $recordFormat}
                {if $recordFormat.1}
                    {assign var=newRecordFormat value=$recordFormat.1}
                {else}
                    {assign var=newRecordFormat value=$recordFormat.0}
                {/if}
                {literal}piwikTracker.setCustomVariable(1, 'RecordFormat',
                '{/literal}{$newRecordFormat|escape:"html"}{literal}',
                'page');{/literal}
            {/if}
            {if $id and $coreMainAuthor and $coreShortTitle}
                {literal}piwikTracker.setCustomVariable(2, 'RecordData',
                '{/literal}{$id|escape:"html"}|{$coreMainAuthor|escape:"html"}|{$coreShortTitle|escape:"html"}{literal}',
                'page');{/literal}
            {/if}
            {if $id and !$coreMainAuthor and $coreShortTitle and $coreContributors}
                {literal}piwikTracker.setCustomVariable(2, 'RecordData',
                '{/literal}{$id|escape:"html"}|{$coreContributors.0|escape:"html"}|{$coreShortTitle|escape:"html"}{literal}',
                'page');{/literal}
            {/if}
            {if $id and !$coreMainAuthor and $coreShortTitle and !$coreContributors}
                {literal}piwikTracker.setCustomVariable(2, 'RecordData',
                '{/literal}{$id|escape:"html"}{literal}|-|{/literal}{$coreShortTitle|escape:"html"}{literal}',
                'page');{/literal}
            {/if}
            {if $coreInstitutions}
                {literal}piwikTracker.setCustomVariable(3, 'RecordInstitution',
                '{/literal}{$coreInstitutions.0|escape:"html"}{literal}',
                'page');{/literal}
            {/if}
            {literal}piwikTracker.trackPageView();{/literal}
        {elseif ($module eq "Search" or $module eq "MetaLib")}
            {if $filterList}
                {literal}piwikTracker.setCustomVariable(1, 'Facets',
                '{/literal}{foreach from=$filterList item=filters}{foreach from=$filters item=filter}{$filter.field|escape:"html"}|{$filter.display|escape:"html"};{/foreach}{/foreach}{literal}',
                'page');
                piwikTracker.setCustomVariable(2, 'FacetTypes',
                '{/literal}{foreach from=$filterList item=filters}{foreach from=$filters item=filter}{$filter.field|escape:"html"};{/foreach}{/foreach}{literal}',
                'page');{/literal}
            {/if}
            {if $searchType}
                {literal}piwikTracker.setCustomVariable(3, 'SearchType',
                '{/literal}{$searchType|escape:"html"}{literal}',
                'page');{/literal}
            {/if}
                
            {literal}
            piwikTracker.trackSiteSearch(
            '{/literal}{if $lookfor}{$lookfor|escape:"html"}{/if}{literal}',{/literal}
            {if $activePrefilter}
                {literal}'{/literal}{$activePrefilter|escape:"html"}{literal}'
                {/literal}
            {else}
                {literal}'-'{/literal}
            {/if}
            {if $recordCount}{literal},{/literal}{$recordCount|escape:"html"}{/if}{literal} 
            );{/literal}
        {/if}    
        {literal}
    } catch( err ) {}
    </script>
    <noscript><p><img src="{/literal}{$piwikUrl}{literal}piwik.php?idsite={/literal}{$piwikSiteId}{literal}" style="border:0" alt="" /></p></noscript>
    <!-- End Piwik Tracking Code -->
  {/literal}
{/if} 
