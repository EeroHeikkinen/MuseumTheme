<!-- START of: MetaLib/searchbox.tpl -->

<div class="searchform last">
  {if $searchType == 'MetaLibAdvanced'}
    <a href="{$path}/MetaLib/Advanced?edit={$searchId}&set={$searchSet|escape}" class="small">{translate text="Edit this Advanced Search"}</a> |
    <a href="{$path}/MetaLib/Advanced?set={$searchSet|escape}" class="small">{translate text="Start a new Advanced Search"}</a> |
    <a href="{$path}/MetaLib/Home?set={$searchSet|escape}" class="small">{translate text="Start a new Basic Search"}</a>
    <br/>{translate text="Your search terms"} : "<strong>{$lookfor|escape:"html"}</strong>"
  {else}
    <form method="get" action="{$path}/MetaLib/Search" name="searchForm" id="searchForm" class="search">
      <div>
        <label for="searchForm_input" class="offscreen">{translate text="Search Terms"}</label>
        <input id="searchForm_input" type="text" name="lookfor" size="40" style="width:200px;" value="{$lookfor|escape:"html"}"/>
        <div class="styled_select">
          <label for="searchForm_set" class="offscreen">{translate text="Search In"}</label>
          <select id="searchForm_set" name="set" class="searchForm_styled">
          {foreach from=$metalibSearchSets item=searchDesc key=searchVal name=loop}
            <option value="{$searchVal}"{if $searchSet == $searchVal || (!$searchSet && $smarty.foreach.loop.first)} selected="selected"{/if}>{translate text=$searchDesc}</option>
          {/foreach}
          </select>
        </div>
        <input id="searchForm_searchButton" type="submit" name="submit" value="{translate text="Find"}"/>
      </div>
      <div class="advanced-link-wrapper clear">
        <a href="{$path}/MetaLib/Advanced?set={$searchSet|escape}" class="small advancedLink">{translate text="Advanced Search"}</a>
        <a href="{$path}/" class="small last metalibLink">{translate text="Local search"}</a>
      </div>
      {js filename="dropdown.js"}
    
      {* Do we have any checkbox filters? *}
      {assign var="hasCheckboxFilters" value="0"}
      {if isset($checkboxFilters) && count($checkboxFilters) > 0}
        {foreach from=$checkboxFilters item=current}
          {if $current.selected}
            {assign var="hasCheckboxFilters" value="1"}
          {/if}
        {/foreach}
      {/if}
      {if $filterList || $hasCheckboxFilters}
        <div class="keepFilters">
          <input type="checkbox" {if $retainFiltersByDefault}checked="checked" {/if} id="searchFormKeepFilters"/> <label for="searchFormKeepFilters">{translate text="basic_search_keep_filters"}</label>
          <div class="offscreen">
            {foreach from=$filterList item=data key=field}
              {foreach from=$data item=value}
                <input type="checkbox" {if $retainFiltersByDefault}checked="checked" {/if} name="filter[]" value='{$value.field|escape}:"{$value.value|escape}"' />
              {/foreach}
            {/foreach}
            {foreach from=$checkboxFilters item=current}
              {if $current.selected}
                <input type="checkbox" {if $retainFiltersByDefault}checked="checked" {/if} name="filter[]" value="{$current.filter|escape}" />
              {/if}
            {/foreach}
          </div>
        </div>
      {/if}
      {if $lastSort}<input type="hidden" name="sort" value="{$lastSort|escape}" />{/if}
    </form>
    <script type="text/javascript">$("#searchForm_lookfor").focus()</script>
  {/if}
</div>

<!-- END of: MetaLib/searchbox.tpl -->