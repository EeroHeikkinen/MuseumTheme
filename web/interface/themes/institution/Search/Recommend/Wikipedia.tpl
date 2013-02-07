{if $info}
  <div class="authorbio">
    <h2>{$info.name|escape}</h2>

    {if $info.image}
      <img src="{$info.image}" alt="{$info.altimage|escape}" width="150px" class="alignleft recordcover"/>
    {/if}
    
    {$info.description|truncate_html:4500:"...":false}

    <div class="providerLink"><a class="wikipedia" href="http://{$wiki_lang}.wikipedia.org/wiki/{$info.name|escape:"url"}" target="_blank">{translate text='wiki_link'}</a></div>

    <div class="clear"></div>  
  </div>
{/if}
