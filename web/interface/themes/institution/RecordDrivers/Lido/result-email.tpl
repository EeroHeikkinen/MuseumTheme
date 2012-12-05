{* This file is used for emails. Note that the line feeds are important for nice text layout. *}
{translate text="Title"}: {if $summTitle}{$summTitle}{else}{translate text='Title not available'}{/if}
{if $summAuthor}

{translate text='by'}: {$summAuthor}{/if}
{if $summDate}

{translate text='Main Year'}: {$summDate.0|escape}{/if}
{if is_array($summFormats)}
  {assign var=displayFormat value=$summFormats|@end}
{else}
  {assign var=displayFormat value=$summFormats}
{/if}

{translate text="Full Record"}: {$url}/{if $summCollection}Collection{else}Record{/if}/{$summId|escape:"url"}
