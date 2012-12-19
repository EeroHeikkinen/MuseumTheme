<!-- START of: Collection/disambiguation.tpl -->

<div id="bd">
  <div id="yui-main" class="content">
    <div class="disambiguationDiv" >
      <h1>{translate text="Collection Disambiguation"}</h1>
      <div id="disambiguationItemsDiv">
      	{foreach from=$collections item=collection name="disambLoop"}
      	 <div class="dismbiguationItem {if ($smarty.foreach.disambLoop.iteration % 2) != 0}alt {/if}record{$smarty.foreach.disambLoop.iteration}">
      	   <a href="{$url}/Collection/{$collection.id|urlencode}">{$collection.title|escape}</a>
      	   <p>{$collection.description|escape}</p>
      	   {if $collection.in_collection|@count >= 2}
      	     {translate text="This collection is part of the following collections:"}
      	     <ul>
      	     {foreach from=$collection.in_collection item=in_collection}
      	       <li>{$in_collection|escape}</li>
      	     {/foreach}
      	     </ul>
      	   {/if}
      	 </div>
      	{/foreach}
      </div>
    </div>
  </div>
</div>

<!-- END of: Collection/disambiguation.tpl -->
