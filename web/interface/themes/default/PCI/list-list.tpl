<form name="addForm">
  {foreach from=$recordSet item=record name="recordLoop"}
  {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
  <div class="result alt record{$smarty.foreach.recordLoop.iteration}">
  {else}
  <div class="result record{$smarty.foreach.recordLoop.iteration}">
  {/if}

    <div class="yui-ge">
      <div class="yui-u first">
        <img src="{$path}/bookcover.php?size=small{if $record.ISBN.0}&amp;isn={$record.ISBN.0|@formatISBN}{/if}{if $record.ContentType.0}&amp;contenttype={$record.ContentType.0|escape:"url"}{/if}" class="alignleft" alt="{translate text="Cover Image"}"/>

        <div class="resultitem">
          <div class="resultItemLine1">
            <a href="{$record.url}"
            class="title">{if !$record.title}{translate text='Title not available'}{else}{$record.title|highlight}{/if}</a>
          </div>

          <div class="resultItemLine2">
            {if $record.author}
            {translate text='by'}
            {foreach from=$record.author item=author name="loop"}
              <a href="{$url}/PCI/Search?type=Author&amp;lookfor={$author|unhighlight|escape:"url"}">{$author|highlight}</a>{if !$smarty.foreach.loop.last},{/if}
            {/foreach}
            <br>
            {/if}

            {if $record.PublicationTitle}{translate text='Published in'} {$record.PublicationTitle.0|highlight}{/if}
            {assign var=pdxml value="PublicationDate_xml"}
            {if $record.$pdxml}({if $record.$pdxml.0.month}{$record.$pdxml.0.month|escape}/{/if}{if $record.$pdxml.0.day}{$record.$pdxml.0.day|escape}/{/if}{if $record.$pdxml.0.year}{$record.$pdxml.0.year|escape}){/if}{elseif $record.PublicationDate}{$record.PublicationDate.0|escape}{/if}
          </div>

          <div class="resultItemLine3">
            {$record.identifier}
          </div>

          <div class="resultItemLine4">
            {if $record.pdf}
            <a href="{$record.pdf}" class="fulltext">{translate text='Get full text'}</a>
            {/if}
          </div>

          <span class="iconlabel {$record.ContentType.0|getSummonFormatClass|escape}">{translate text=$record.ContentType.0}</span>

          <a href="{$record.openUrl|escape}" target="_blank">{translate text='Get full text'}</a>

        </div>
      </div>

      {* TODO: make "save record" feature work:
      <div class="yui-u">
        <div id="saveLink{$record.id.0}">
          <a href="{$url}/Record/Save?id={$record.id.0}"
             onClick="getLightbox('Record', 'Save', 'Summon', '{$record.id.0}', null); return false;" class="fav tool">{translate text='Add to favorites'}</a>
          <ul id="lists{$record.id.0}"></ul>
          <script language="JavaScript" type="text/javascript">
            getSaveStatuses('{$record.id.0}');
          </script>
        </div>
      </div>
       *}
    </div>

    <span class="Z3988" title="{$record.openUrl|escape}"></span>

  </div>

{/foreach}
</form>

{* TODO: implement save statuses for Summon
{if $user}
<script type="text/javascript">
  doGetSaveStatuses();
</script>
{/if}
 *}