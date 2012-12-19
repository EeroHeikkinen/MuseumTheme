<div>
  {if !empty($electronic)}
    <div class="openurls">
      <span class="fulltextAvailable">{translate text="Full text available"}:</span>
      <ul>
        {foreach from=$electronic item=link}
          <li>
            {if $link.href}
              <a class="availableLoc" href="{$link.href|escape}" title="{$link.service_type|escape}">{$link.title|escape}</a> {$link.coverage|escape}
            {else}
              {$link.title|escape} {$link.coverage|escape}
            {/if}
          </li>
        {/foreach}
      </ul>
    </div>
  {/if}
  {if !empty($print)}
    <div class="openurls">
      {translate text="Holdings"}
      <ul>
        {foreach from=$print item=link}
          <li>
            {if $link.href}
              <a href="{$link.href|escape}" title="{$link.service_type|escape}">{$link.title|escape}</a> {$link.coverage|escape}
            {else}
              {$link.title|escape} {$link.coverage|escape}
            {/if}
          </li>
        {/foreach}
      </ul>
    </div>
  {/if}
  <div class="openurls">
    <a class="openurl_more" href="{$path}/AJAX/SFXMenu.php?action=SFXMenu&openurl={$openUrl|escape:"url"}">{translate text="More options"} <span class="more_img"><img src="{path filename="images/down.png"}" width="11" height="6"/></span><span class="less_img hide"><img src="{path filename="images/up.png"}" width="11" height="6"/></span></a>
    <a class="openurl_more_full hide" href="{$openUrlBase|escape}?{$openUrl|escape}" target="_blank">{translate text="Open in a New Window"}</a>
    {if !empty($services)}
      <ul>
        {foreach from=$services item=link}
          {if $link.href}
            <li>
              <a href="{$link.href|escape}" title="{$link.service_type|escape}">{$link.title|escape}</a>
            </li>
          {/if}
        {/foreach}
      </ul>
    {/if}
  </div>
</div>
