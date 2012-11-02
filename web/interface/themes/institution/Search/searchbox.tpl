<!-- START of: Search/searchbox.tpl -->

<div id="searchForm" class="searchform last">

{if $searchType == 'advanced'}
  <a href="{$path}/Search/Advanced?edit={$searchId}" class="small">{translate text="Edit this Advanced Search"}</a> |
  <a href="{$path}/Search/Advanced" class="small">{translate text="Start a new Advanced Search"}</a> |
  <a href="{$path}/" class="small">{translate text="Start a new Basic Search"}</a>
  <br/>{translate text="Your search terms"} : "<span class="strong">{$lookfor|escape:"html"}
  {foreach from=$orFilters item=values key=filter}
    AND ({foreach from=$values item=value name=orvalues}{translate text=$filter|ucfirst}:{translate text=facet_$value}{if !$smarty.foreach.orvalues.last} OR {/if}{/foreach}){/foreach}"</span>
{else}
  <form method="get" action="{$path}/Search/Results" name="searchForm" id="searchForm" class="search">
    <div>
      <label for="searchForm_input" class="offscreen">{translate text="Search Terms"}</label>
      <input id="searchForm_input" type="text" name="lookfor" size="40" value="{$lookfor|escape}" class="span-4 last{if $autocomplete} autocomplete typeSelector:searchForm_type{/if} clearable" placeholder="{translate text="Find"}&hellip;"/>
  {if $prefilterList}
{* DELETE THIS
  <div class="styled_select">
      <select id="source" class="">
        <option selected="selected" value="BR">Brasil</option>
        <option value="FR">France</option>
        <option value="DE">Germany</option>
        <option value="IN">India</option>
        <option value="JP">Japan</option>
        <option value="RS">Serbia</option>
        <option value="UK">United Kingdom</option>
        <option value="US">United States</option>
      </select>
  </div>
*}
      <div class="styled_select">
        <select id="searchForm_filter" class="searchForm_styled" name="prefilter">
    {foreach from=$prefilterList item=searchDesc key=searchVal}    
          <option value="{$searchVal|escape}"{if $searchVal == $activePrefilter || ($activePrefilter == null && $searchVal == "-") } selected="selected"{/if}>{$searchDesc|translate}</option>
    {/foreach}
        </select>
      </div>

  {/if}
      <input id="searchForm_searchButton" type="submit" name="SearchForm_submit" value="{translate text="Find"}"/>
      <div class="clear"></div>
    </div>
    <div class="advanced-link-wrapper clear">
      <a href="{$path}/Search/Advanced" class="small advancedLink">{translate text="Advanced Search"}</a>
  {if $metalibEnabled}
      <a href="{$path}/MetaLib/Home" class="small last metalibLink">{translate text="MetaLib Search"}</a>
  {/if}
    </div>
  {* Do we have any checkbox filters? *}
  {assign var="hasCheckboxFilters" value="0"}
  {if isset($checkboxFilters) && count($checkboxFilters) > 0}
    {foreach from=$checkboxFilters item=current}
      {if $current.selected}
        {assign var="hasCheckboxFilters" value="1"}
      {/if}
    {/foreach}
  {/if}

  {if $shards}
    <br />
    {foreach from=$shards key=shard item=isSelected}
    <input type="checkbox" {if $isSelected}checked="checked" {/if}name="shard[]" value='{$shard|escape}' /> {$shard|translate}
    {/foreach}
  {/if}

  {if ($filterList || $hasCheckboxFilters) && !$disableKeepFilterControl}
    <div class="keepFilters">
      <input type="checkbox" {if $retainFiltersByDefault}checked="checked" {/if} id="searchFormKeepFilters"/>
      <label for="searchFormKeepFilters">{translate text="basic_search_keep_filters"}</label>

      <div class="offscreen">
    {foreach from=$filterList item=data key=field name=filterLoop}
      {foreach from=$data item=value}
        <input id="applied_filter_{$smarty.foreach.filterLoop.iteration}" type="checkbox" {if $retainFiltersByDefault}checked="checked" {/if} name="filter[]" value="{$value.field|escape}:&quot;{$value.value|escape}&quot;" />
        <label for="applied_filter_{$smarty.foreach.filterLoop.iteration}">{$value.field|escape}:&quot;{$value.value|escape}&quot;</label>
      {/foreach}
    {/foreach}

    {foreach from=$checkboxFilters item=current name=filterLoop}
      {if $current.selected}
        <input id="applied_checkbox_filter_{$smarty.foreach.filterLoop.iteration}" type="checkbox" {if $retainFiltersByDefault}checked="checked" {/if} name="filter[]" value="{$current.filter|escape}" />
        <label for="applied_checkbox_filter_{$smarty.foreach.filterLoop.iteration}">{$current.filter|escape}</label>
      {/if}
    {/foreach}
      </div>

    </div>
  {/if}

  {* Load hidden limit preference from Session *}
  {if $lastLimit}<input type="hidden" name="limit" value="{$lastLimit|escape}" />{/if}
  {if $lastSort}<input type="hidden" name="sort" value="{$lastSort|escape}" />{/if}

  </form>
  {literal}
  <script type="text/javascript">$("#searchForm_lookfor").focus()</script>
  <script type="text/javascript">
    $(function() {
      // init plugin (with callback)
      $('.clearable').clearSearch({ callback: function() { console.log("cleared"); } } );

      // update value
      valueContent = $(".clearable").attr("value");
      if (valueContent == null) {
        $(".clearable").val("").change();
      };
      
      // change width
      $(".clearable").width("200px").change();
    });
  </script>
  {/literal}
  {js filename="dropdown.js"}
{/if}

</div>

<!-- END of: Search/searxhbox.tpl -->
