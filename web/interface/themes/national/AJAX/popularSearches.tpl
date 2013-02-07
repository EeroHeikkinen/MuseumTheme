<ul>
{foreach from=$searchPhrases item=phrase name=phrases}
    <li><span>{counter}</span><span><a href="{$url}/Search/Results?lookfor={$phrase|escape url}">{$phrase|truncate:20:" ..":true}</a></span></li>
    {if $smarty.foreach.phrases.iteration == 5}</ul><ul>{/if}
{/foreach}
</ul>