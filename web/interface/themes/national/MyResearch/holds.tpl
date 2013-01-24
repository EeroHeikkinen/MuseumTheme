<!-- START of: MyResearch/holds.tpl -->

{include file="MyResearch/menu.tpl"}

<div class="myResearch holdsList{if $sidebarOnLeft} last{/if}">
  <div class="content">
  {if $user->cat_username}
  <div class="resultHead">
    {if $holdResults.success}
      <div class="holdsMessage"><p class="success">{translate text=$holdResults.status}</p></div>
    {/if}
    {if $callSlipResults.success}
      <div class="holdsMessage"><p class="success">{translate text=$callSlipResults.status}</p></div>
    {/if}

    {if $errorMsg}
       <div class="holdsMessage"><p class="error">{translate text=$errorMsg}</p></div>
    {/if}

    {if $cancelResults.count > 0}
      <div class="holdsMessage"><p class="info">{$cancelResults.count|escape} {translate text="hold_cancel_success_items"}</p></div>
    {/if}
    {if $cancelCallSlipResults.count > 0}
      <div class="holdsMessage"><p class="info">{$cancelCallSlipResults.count|escape} {translate text="call_slip_cancel_success_items"}</p></div>
    {/if}
    <span class="hefty">{translate text='Holds and Recalls'}</span>
  </div>
    {if $cancelForm && $recordList}
  <form name="cancelForm" action="{$url|escape}/MyResearch/Holds" method="post" id="cancelHold">
    <div class="bulkActionButtons">
        <div class="allCheckboxBackground"><input type="checkbox" class="selectAllCheckboxes" name="selectAll" id="addFormCheckboxSelectAll" /></div>
        <div class="floatright">
          <input type="submit" class="button holdCancel" name="cancelSelected" value="{translate text="hold_cancel_selected"}" onclick="return confirm('{translate text="confirm_hold_cancel_selected_text}')" />
          <input type="submit" class="button holdCancelAll" name="cancelAll" value="{translate text='hold_cancel_all'}" onclick="return confirm('{translate text="confirm_hold_cancel_all_text}')" />
        </div>
        <div class="clear"></div>
      </div>
    {/if}

    <div class="clear"></div>

    {if is_array($recordList)}
    <ul class="recordSet">
    {foreach from=$recordList item=resource name="recordLoop"}
      <li class="result{if ($smarty.foreach.recordLoop.iteration % 2) == 0} alt{/if}">
        {if $cancelForm && $resource.ils_details.cancel_details}
          <div class="resultCheckbox">
          <input type="hidden" name="cancelAllIDS[]" value="{$resource.ils_details.cancel_details|escape}" />
          <input type="checkbox" name="cancelSelectedIDS[]" value="{$resource.ils_details.cancel_details|escape}" class="checkbox" />
          </div>
        {/if}
        <div id="record{$resource.id|escape}">
        	{assign var=summImages value=$resource.summImages}
        	{assign var=summThumb value=$resource.summThumb}        	
        	{assign var=summId value=$resource.id}        	
			{assign var=img_count value=$summImages|@count}
			<div class="coverDiv">
			  <div class="resultNoImage"><p>{translate text='No image'}</p></div>
				{if $img_count > 0}
					<div class="resultImage"><a href="{$url}/Record/{$resource.id|escape:"url"}"><img id="thumbnail_{$summId|escape:"url"}" src="{$summThumb|escape}" class="summcover" alt="{translate text='Cover Image'}"/></a></div>
				{else}
					<div class="resultImage"><a href="{$url}/Record/{$resource.id|escape:"url"}"><img src="{$path}/images/NoCover2.gif" width="62" height="62" alt="{translate text='No Cover Image'}"/></a></div>
				{/if}
			
			{* Multiple images *}
			{if $img_count > 1}
			  <div class="imagelinks">
			{foreach from=$summImages item=desc name=imgLoop}
				<a href="{$path}/thumbnail.php?id={$summId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large" class="title" onmouseover="document.getElementById('thumbnail_{$summId|escape:"url"}').src='{$path}/thumbnail.php?id={$summId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=small'; document.getElementById('thumbnail_link_{$summId|escape:"url"}').href='{$path}/thumbnail.php?id={$summId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large'; return false;">
				  {if $desc}{$desc|escape}{else}{$smarty.foreach.imgLoop.iteration + 1}{/if}
				</a>
			{/foreach}
			  </div>
			{/if}
			</div>
          <div class="resultColumn2">
            {* If $resource.id is set, we have the full Solr record loaded and should display a link... *}
            {if !empty($resource.id)}
              <a href="{$url}/Record/{$resource.id|escape:"url"}" class="title">{$resource.title|escape}</a>
            {* If the record is not available in Solr, perhaps the ILS driver sent us a title we can show... *}
            {elseif !empty($resource.ils_details.title)}
              {$resource.ils_details.title|escape}
            {* Last resort -- indicate that no title could be found. *}
            {else}
              {translate text='Title not available'}
            {/if}
            <br/>
            {if $resource.author}
              {translate text='by'}: <a href="{$url}/Search/Results?lookfor={$resource.author|escape:"url"}&amp;type=Author">{$resource.author|escape}</a><br/>
            {/if}
            {if $resource.tags}
              <strong>{translate text='Your Tags'}:</strong>
              {foreach from=$resource.tags item=tag name=tagLoop}
                <a href="{$url}/Search/Results?tag={$tag->tag|escape:"url"}">{$tag->tag|escape}</a>{if !$smarty.foreach.tagLoop.last},{/if}
              {/foreach}
              <br/>
            {/if}
            {if $resource.notes}
              <strong>{translate text='Notes'}:</strong> {$resource.notes|escape}<br/>
            {/if}

 			  {if is_array($resource.format)}
				{assign var=mainFormat value=$resource.format.0} 
				{assign var=displayFormat value=$resource.format|@end} 
			  {else}
				{assign var=mainFormat value=$resource.format} 
				{assign var=displayFormat value=$resource.format} 
			  {/if}
			  <span class="iconlabel format{$mainFormat|lower|regex_replace:"/[^a-z0-9]/":""} format{$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$displayFormat prefix='format_'}</span>

            {if $resource.ils_details.volume}
              <strong>{translate text='Volume'}:</strong> {$resource.ils_details.volume|escape}<br />
            {/if}

            {if $resource.ils_details.publication_year}
              <strong>{translate text='Year of Publication'}:</strong> {$resource.ils_details.publication_year|escape}<br />
            {/if}
            </div>
            <div class="dueDate">
              {assign var=source value=$user->cat_username|regex_replace:'/\..*?$/':''}
              {translate text=$source prefix='source_'},
            {* Depending on the ILS driver, the "location" value may be a string or an ID; figure out the best value to display... *}
            {assign var="pickupDisplay" value=""}
            {assign var="pickupTranslate" value="0"}
            {if isset($resource.ils_details.location)}
              {if $pickup}
                {foreach from=$pickup item=library}
                  {if $library.locationID == $resource.ils_details.location}
                    {assign var="pickupDisplay" value=$library.locationDisplay}
                    {assign var="pickupTranslate" value="1"}
                  {/if}
                {/foreach}
              {/if}
              {if empty($pickupDisplay)}
                {assign var="pickupDisplay" value=$resource.ils_details.location}
              {/if}
            {/if}
            {if !empty($pickupDisplay)}
              <strong>{translate text='pick_up_location'}:</strong>
              {if $pickupTranslate}{translate text=$pickupDisplay}{else}{$pickupDisplay|escape}{/if}
              <br />
            {/if}

            <strong>{translate text='Created'}:</strong> {$resource.ils_details.create|escape},
            <strong>{translate text='Expires'}:</strong> {$resource.ils_details.expire|escape}
            <br />

            {foreach from=$cancelResults.items item=cancelResult key=itemId}
              {if $itemId == $resource.ils_details.item_id && $cancelResult.success == false}
                <div class="error">{translate text=$cancelResult.status}{if $cancelResult.sysMessage} : {translate text=$cancelResult.sysMessage|escape}{/if}</div>
              {/if}
            {/foreach}

            {if $resource.ils_details.available == true}
              <span class="available">{translate text="hold_available"}</span>
            {else}
              {if $resource.ils_details.position}
              <p><strong>{translate text='hold_queue_position'}:</strong> {$resource.ils_details.position|escape}</p>
              {/if}
            {/if}
            {if $resource.ils_details.cancel_link}
              <p><a href="{$resource.ils_details.cancel_link|escape}">{translate text='hold_cancel'}</a></p>
            {/if}

          </div>
          <div class="clear"></div>
        </div>
      </li>
    {/foreach}
    </ul>
    {if $cancelForm}
    </form>
    {/if}
    {else}
      <div class="noContentMessage">{translate text='You do not have any holds or recalls placed'}.</div>
    {/if}

    <div style="clear:both;padding-top: 2em;"></div>

  {* Call Slips *}
  <span class="hefty myResearchTitle">{translate text='Call Slips'}</span>
    {if is_array($callSlipList)}
  <form name="cancelCallSlipForm" action="{$url|escape}/MyResearch/Holds" method="post" id="cancelCallSlip">

    <div class="bulkActionButtons">
        <div class="allCheckboxBackground"><input type="checkbox" class="selectAllCheckboxes floatleft" name="selectAll" id="addFormCheckboxSelectAllCallSlips" /></div>
        <div class="floatright">
          <input type="submit" class="button holdCancel" name="cancelSelectedCallSlips" value="{translate text="call_slip_cancel_selected"}" onclick="return confirm('{translate text="confirm_call_slip_cancel_selected_text}')" />
          <input type="submit" class="button holdCancelAll" name="cancelAllCallSlips" value="{translate text='call_slip_cancel_all'}" onclick="return confirm('{translate text="confirm_call_slip_cancel_all_text}')" />
        </div>
      </div>

    <div class="clear"></div>

    <ul class="recordSet">
    {foreach from=$callSlipList item=resource name="recordLoop"}
      <li class="result{if ($smarty.foreach.recordLoop.iteration % 2) == 0} alt{/if}">
        {if $resource.ils_details.cancel_details}
          <div class="resultCheckbox">
          <input type="hidden" name="cancelAllCallSlipIDS[]" value="{$resource.ils_details.cancel_details|escape}" />
          <input type="checkbox" name="cancelSelectedCallSlipIDS[]" value="{$resource.ils_details.cancel_details|escape}" class="checkbox" />
          </div>
        {/if}
        <div id="record{$resource.id|escape}">
          {assign var=summImages value=$resource.summImages}
          {assign var=summThumb value=$resource.summThumb}          
          {assign var=summId value=$resource.id}          
      {assign var=img_count value=$summImages|@count}
      <div class="coverDiv">
        <div class="resultNoImage"><p>{translate text='No image'}</p></div>
        {if $img_count > 0}
          <div class="resultImage"><a href="{$url}/Record/{$resource.id|escape:"url"}"><img id="thumbnail_{$summId|escape:"url"}" src="{$summThumb|escape}" class="summcover" alt="{translate text='Cover Image'}"/></a></div>
        {else}
          <div class="resultImage"><a href="{$url}/Record/{$resource.id|escape:"url"}"><img src="{$path}/images/NoCover2.gif" width="62" height="62" /></a></div>
        {/if}
      
      {* Multiple images *}
      {if $img_count > 1}
        <div class="imagelinks">
      {foreach from=$summImages item=desc name=imgLoop}
        <a href="{$path}/thumbnail.php?id={$summId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large" class="title" onmouseover="document.getElementById('thumbnail_{$summId|escape:"url"}').src='{$path}/thumbnail.php?id={$summId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=small'; document.getElementById('thumbnail_link_{$summId|escape:"url"}').href='{$path}/thumbnail.php?id={$summId|escape:"url"}&index={$smarty.foreach.imgLoop.iteration-1}&size=large'; return false;">
          {if $desc}{$desc|escape}{else}{$smarty.foreach.imgLoop.iteration + 1}{/if}
        </a>
      {/foreach}
        </div>
      {/if}
      </div>
          {*
          <div class="coverDiv">
            {if $resource.isbn.0}
              <img src="{$path}/bookcover.php?isn={$resource.isbn.0|@formatISBN}&amp;size=small" class="summcover" alt="{translate text='Cover Image'}"/>
            {else}
              <img src="{$path}/bookcover.php" class="summcover" alt="{translate text='No Cover Image'}"/>
            {/if}
          </div>
          *}
          <div class="resultColumn2">
            {* If $resource.id is set, we have the full Solr record loaded and should display a link... *}
            {if !empty($resource.id)}
              <a href="{$url}/Record/{$resource.id|escape:"url"}" class="title">{$resource.title|escape}</a>
            {* If the record is not available in Solr, perhaps the ILS driver sent us a title we can show... *}
            {elseif !empty($resource.ils_details.title)}
              {$resource.ils_details.title|escape}
            {* Last resort -- indicate that no title could be found. *}
            {else}
              {translate text='Title not available'}
            {/if}
            <br/>
            {if $resource.author}
              {translate text='by'}: <a href="{$url}/Search/Results?lookfor={$resource.author|escape:"url"}&amp;type=Author">{$resource.author|escape}</a><br/>
            {/if}
            {if $resource.tags}
              <strong>{translate text='Your Tags'}:</strong>
              {foreach from=$resource.tags item=tag name=tagLoop}
                <a href="{$url}/Search/Results?tag={$tag->tag|escape:"url"}">{$tag->tag|escape}</a>{if !$smarty.foreach.tagLoop.last},{/if}
              {/foreach}
              <br/>
            {/if}
            {if $resource.notes}
              <strong>{translate text='Notes'}:</strong> {$resource.notes|escape}<br/>
            {/if}

        {if is_array($resource.format)}
        {assign var=mainFormat value=$resource.format.0} 
        {assign var=displayFormat value=$resource.format|@end} 
        {else}
        {assign var=mainFormat value=$resource.format} 
        {assign var=displayFormat value=$resource.format} 
        {/if}
        <span class="iconlabel format{$mainFormat|lower|regex_replace:"/[^a-z0-9]/":""} format{$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$displayFormat prefix='format_'}</span>

            {if $resource.ils_details.volume}
              <strong>{translate text='Volume'}:</strong> {$resource.ils_details.volume|escape}<br />
            {/if}

            {if $resource.ils_details.publication_year}
              <strong>{translate text='Year of Publication'}:</strong> {$resource.ils_details.publication_year|escape}<br />
            {/if}
            </div>
            <div class="dueDate">
              {assign var=source value=$user->cat_username|regex_replace:'/\..*?$/':''}
              {translate text=$source prefix='source_'},
            {* Depending on the ILS driver, the "location" value may be a string or an ID; figure out the best value to display... *}
            {assign var="pickupDisplay" value=""}
            {assign var="pickupTranslate" value="0"}
            {if isset($resource.ils_details.location)}
              {if $pickup}
                {foreach from=$pickup item=library}
                  {if $library.locationID == $resource.ils_details.location}
                    {assign var="pickupDisplay" value=$library.locationDisplay}
                    {assign var="pickupTranslate" value="1"}
                  {/if}
                {/foreach}
              {/if}
              {if empty($pickupDisplay)}
                {assign var="pickupDisplay" value=$resource.ils_details.location}
              {/if}
            {/if}
            {if !empty($pickupDisplay)}
              <strong>{translate text='pick_up_location'}:</strong>
              {if $pickupTranslate}{translate text=$pickupDisplay}{else}{$pickupDisplay|escape}{/if}
              <br />
            {/if}

            <strong>{translate text='Created'}:</strong> {$resource.ils_details.create|escape}
            {if $resource.ils_details.processed}<br/><strong>{translate text='Processed'}:</strong> {$resource.ils_details.processed|escape}{/if}
            <br />

            {foreach from=$cancelCallSlipResults.items item=cancelResult key=itemId}
              {if $itemId == $resource.ils_details.item_id && $cancelResult.success == false}
                <div class="error">{translate text=$cancelCallSlipResult.status}{if $cancelResult.sysMessage} : {translate text=$cancelResult.sysMessage|escape}{/if}</div>
              {/if}
            {/foreach}

            {if $resource.ils_details.available}
              <span class="available">{translate text="call_slip_available"}</span>
            {/if}
            {if $resource.ils_details.cancelled}
              <span class="cancelled"><strong>{translate text="call_slip_cancelled"}:</strong> {$resource.ils_details.cancelled}</span>
            {/if}

          </div>
          <div class="clear"></div>
        </div>
      </li>
    {/foreach}
    </ul>
    </form>
    {else}
      <div class="noContentMessage">{translate text='You do not have any holds or recalls placed'}.</div>
    {/if}
  {else}
    {include file="MyResearch/catalog-login.tpl"}
  {/if}
  </div>
</div>

<div class="clear"></div>

<!-- END of: MyResearch/holds.tpl -->
