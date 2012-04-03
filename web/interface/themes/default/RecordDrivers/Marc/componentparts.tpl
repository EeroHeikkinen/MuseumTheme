<br>&nbsp;<br>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="componentparts" width="100%">
	<thead>
		<tr>
			<th>{translate text="No."}</th>
			<th>{translate text="Title"}</th>
			<th>{translate text="Author(s)"}</th>
		</tr>
	</thead>    
	<tbody>
	    {if !empty($componentparts)}
        {foreach from=$componentparts item=componentpart}
        <tr valign="top">
            <td>{$componentpart.number|escape}</td>
            <td><a href="{$componentpart.link|escape}">{$componentpart.title|escape}</a></td>
            <td>{$componentpart.author|escape}</td>
        </tr>
        {/foreach}
        {/if}
	</tbody>
</table>

{literal}
<script type="text/javascript" charset="utf-8">
    $('table#componentparts').dataTable({
	    "oLanguage": {
{/literal}
            "sSearch": "{translate text="component_parts_search"}",
            "sLengthMenu": "{translate text="component_parts_show_entries"}",
            "sInfoFiltered": "{translate text="component_parts_filtered"}",
            "sInfo": "{translate text="component_parts_entries_on_page"}",
{literal}
            "oPaginate": {
{/literal}
                "sNext": "{translate text="component_parts_next"}",
                "sPrevious": "{translate text="component_parts_previous"}"
{literal}
            }
        }				
	});
</script>
{/literal}



