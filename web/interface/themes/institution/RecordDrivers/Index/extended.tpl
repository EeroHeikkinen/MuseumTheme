<!-- START of: RecordDrivers/Index/extended.tpl -->

{*<table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Description'}">*}
  {if !empty($extendedSummary)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Summary'}: </th>
    <td>
      {foreach from=$extendedSummary item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}
  
  {* BTJ description start *}
  <tr valign="top" id="btjdescription" style="display: none;">
    <th>{translate text='Description'}: </th>
    <td id="btjdescription_text"><img src="{$path}/interface/themes/institution/images/ajax_loading.gif" alt="{translate text='Loading'}..."/></td>  
  </tr>
  <script type="text/javascript">
     var path = {$path|@json_encode};
     var id = {$id|@json_encode};
     {literal}
     $(document).ready(function() {
       var url = path + '/description.php?id=' + id;
       $("#btjdescription_text").load(url, function(response, status, xhr) {
       if (response.length != 0) {
         $("#btjdescription").show();
       }
       });
     });
     {/literal}
  </script>  
  {* BTJ description end *}

  {if !empty($extendedDateSpan)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Dates of Publication'}: </th>
    <td>
      {foreach from=$extendedDateSpan item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedNotes)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Item Description'}: </th>
    <td>
      {foreach from=$extendedNotes item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedFrequency)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Publication Frequency'}: </th>
    <td>
      {foreach from=$extendedFrequency item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedPlayTime)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Playing Time'}: </th>
    <td>
      {foreach from=$extendedPlayTime item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedSystem)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='System Details'}: </th>
    <td>
      {foreach from=$extendedSystem item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedAudience)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Audience'}: </th>
    <td>
      {foreach from=$extendedAudience item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedAwards)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Awards'}: </th>
    <td>
      {foreach from=$extendedAwards item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedCredits)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Production Credits'}: </th>
    <td>
      {foreach from=$extendedCredits item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedBibliography)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Bibliography'}: </th>
    <td>
      {foreach from=$extendedBibliography item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedISBNs)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='ISBN'}: </th>
    <td>
      {foreach from=$extendedISBNs item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedISSNs)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='ISSN'}: </th>
    <td>
      {foreach from=$extendedISSNs item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedRelated)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Related Items'}: </th>
    <td>
      {foreach from=$extendedRelated item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedAccess)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Access'}: </th>
    <td>
      {foreach from=$extendedAccess item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedFindingAids)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Finding Aid'}: </th>
    <td>
      {foreach from=$extendedFindingAids item=field name=loop}
        {$field|escape}<br/>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedAuthorNotes)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Author Notes'}: </th>
    <td>
      {foreach from=$extendedAuthorNotes item=providerList key=provider}
        {foreach from=$providerList item=field name=loop}
          {$field.Content}<br/>
        {/foreach}
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($extendedVideoClips)}
  {assign var=extendedContentDisplayed value=1}
  <tr valign="top">
    <th>{translate text='Video Clips'}: </th>
    <td>
      {foreach from=$extendedVideoClips item=providerList key=provider}
        {foreach from=$providerList item=field name=loop}
          {$field.Content}<br/>
          {$field.Copyright}<br/>
        {/foreach}
      {/foreach}
    </td>
  </tr>
  {/if}

  {* Avoid errors if there were no rows above *}
  {if !$extendedContentDisplayed}
  {*<tr><td>&nbsp;</td></tr>*}
  {/if}
   
{*</table>*}

<!-- END of: RecordDrivers/Index/extended.tpl -->
