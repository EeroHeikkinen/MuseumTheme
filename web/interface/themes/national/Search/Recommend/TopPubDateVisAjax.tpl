<!-- START of: Search/Recommend/TopPubDateVisAjax.tpl -->

{if $visFacets}
  <div class="resultDates {if !empty($visFacets.main_date_str[0])}expanded{/if}">
    <div class="content">
        {* load jQuery flot *}
        <!--[if IE]>{js filename="flot/excanvas.min.js"}<![endif]--> 
        {js filename="flot/jquery.flot.js"}
        {js filename="flot/jquery.flot.selection.js"}
        {js filename="pubdate_vis.js"}

        {foreach from=$visFacets item=facetRange key=facetField}
          <div id="topPubDateVis" class="{if $facetRange.label == "adv_search_year"}span-10{if $sidebarOnLeft} last{/if}{/if}">
              <div class="dateVis" id="datevis{$facetField}x"></div>
              <div id="clearButtonText" style="display: none">x</div>  
          </div>
        {/foreach}
        <script type="text/javascript">
          //<![CDATA[
          loadVis('{$facetFields|escape:'javascript'}', '{$searchParams|escape:'javascript'}', '{$url}', {$zooming}{if $collectionName}, '{$collectionID|urlencode}', '{$collectionAction}'{/if});
          //]]>
        </script>
        </div>
    </div>

{/if}

<!-- END of: Search/Recommend/TopPubDateVisAjax.tpl -->
