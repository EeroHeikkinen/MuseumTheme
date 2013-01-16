{* This file is used for emails. Note that the line feeds are important for nice text layout. *}
{if is_array($summFormats)}
{assign var=displayFormat value=$summFormats|@end}
{else}
{assign var=displayFormat value=$summFormats}
{/if}
{translate text="Title"}: {if $summTitle}{$summTitle}{else}{translate text='Title not available'}{/if}
{if !empty($summOrigination)}{translate text='Archive Origination:'} {$summOrigination}{/if}
{if $displayFormat != 'Document/ArchiveFonds'}

{translate text='Archive:'} {foreach from=$summHierarchyTopId name=loop key=topKey item=topId}{$summHierarchyTopTitle.$topKey|truncate:180:"..."}{if !$smarty.foreach.loop.last}, {/if}{/foreach}{/if}  
{if $displayFormat != 'Document/ArchiveFonds' && $displayFormat != 'Document/ArchiveSeries'}
{translate text='Archive Series:'} {foreach from=$summHierarchyParentId name=loop key=parentKey item=parentId} {$summHierarchyParentTitle.$parentKey|truncate:180:"..."}{if !$smarty.foreach.loop.last}, {/if}{/foreach}{/if}  
{if $summDate}

{translate text='Published'}: {$summDate.0|escape}{/if}{if $summPublicationEndDate} - {if $summPublicationEndDate != 9999}{$summPublicationEndDate}{/if}{/if}

{translate text="Full Record"}: {$url}/{if $summCollection}Collection{else}Record{/if}/{$summId|escape:"url"}
