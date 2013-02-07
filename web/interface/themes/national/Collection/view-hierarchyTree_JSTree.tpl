<!-- START of: Collection/view-hierarchyTree_JSTree.tpl -->

{js filename="jsTree/jquery.jstree.js"}
<script type="text/javascript">
{literal}$.jstree._themes = "{/literal}{path filename="js/jsTree/themes/"}";
</script>
{js filename="hierarchyTree_JSTree.js"}
<script type="text/javascript">
vufindString.lightboxMode = false;
vufindString.fullHierarchy = {if $treeSettings.fullHierarchyRecordView == true || $disablePartialHierarchy == true}true{else}false{/if};
vufindString.showTree = "{translate text="hierarchy_show_tree"}";
vufindString.hideTree = "{translate text="hierarchy_hide_tree"}";
</script>
{*
<div class="span-13">
*}
    {if $showTreeSelector}
    <div id="treeSelector">
    {foreach from=$hasHierarchyTree item=hierarchyTitle key=hierarchy}
    <a class="tree{if $hierarchyID == $hierarchy} currentTree{/if}" href="{$url}/Collection/{$id}/HierarchyTree?hierarchy={$hierarchy}">{$hierarchyTitle}</a>
    {/foreach}
    </div>
    {/if}
    {if $hierarchyID}
    	<div id="hierarchyTreeHolder">
    	  {if $showTreeSearch}
            <div id="treeSearch" >
              <span id="treeSearchNoResults">{translate text="No results"}</span>
              <input id="search" type="button" value="search" />
              <select id="treeSearchType" name="type" >
                <option value="AllFields">{translate text="All Fields"}</option>
                <option value="Title">{translate text="Title"}</option>
              </select>
              <input id="treeSearchText" type="text" value="" />
              <span id="treeSearchLoadingImg"><img src="{$path}/images/loading.gif" alt="" /></span>
    	    </div>
    	    <div id="treeSearchLimitReached">{translate text="Your search returned too many results to display in the tree. Showing only the first"} <b>{$treeSearchLimit}</b> {translate text="items. For a full search click"} <a id="fullSearchLink" href="{$treeSearchFullURL}" target="_blank">{translate text="here"}.</a></div>
    	  {/if}
    	  <div class="clearer"></div>
          <div id="hierarchyTree">
              <input type="hidden" value="{$id|escape}" class="hiddenRecordId" />
              <input type="hidden" value="{$hierarchyID|escape}" class="hiddenHierarchyId" />
              <input type="hidden" value="{$context|escape}" class="hiddenContext" />
            <noscript>
              <!--//--><![CDATA[//><!--
                {$hierarchyTree}
              //--><!]]>
            </noscript>
          </div>
        </div>
    {/if}
{*
</div>
*}

{*
<div class="span-13">
*}
    <div id="hierarchyRecord">
    {if $collectionRecord}
        {if $collectionRecord == "unknown"}
          <h1>Unknown Record</h1>
          <p>We have been unable to locate record <strong>{$recordID}</strong></p>
        {else}
          {include file=$collectionRecord}
        {/if}
    {/if}
   </div>
{*
</div>
*}
<div class="clear">&nbsp;</div>

<!-- END of: Collection/view-hierarchyTree_JSTree.tpl -->
