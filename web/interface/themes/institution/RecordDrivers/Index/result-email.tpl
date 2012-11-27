{* This file is used for emails. Note that the line feeds are important for nice text layout. *}
{translate text="Title"}: {if $summTitle}{$summTitle}{else}{translate text='Title not available'}{/if}
{if $summAuthor}

{translate text='by'}: {$summAuthor}{/if}
{if $summDate}

{translate text='Published'}: {$summDate.0|escape}{/if}{if $summPublicationEndDate} - {if $summPublicationEndDate != 9999}{$summPublicationEndDate}{/if}{/if}
{if $summISBN}

{translate text='ISBN'}: {$summISBN}{/if}
{if $summISSN}

{translate text='ISSN'}: {$summISSN}{/if}
{if $summInCollection}{foreach from=$summInCollection item=InCollection key=cKey}

{translate text="in_collection_label"} {$InCollection}{/foreach}
{else}
{if $summContainerTitle}

{translate text='component_part_is_part_of'}: {$summContainerTitle}{/if}{if $summContainerReference} {$summContainerReference}{/if}{/if}
{if is_array($summFormats)}
  {assign var=displayFormat value=$summFormats|@end}
{else}
  {assign var=displayFormat value=$summFormats}
{/if}

{translate text="Full Record"}: {$url}/{if $summCollection}Collection{else}Record{/if}/{$summId|escape:"url"}
