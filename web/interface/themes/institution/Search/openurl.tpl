{if $openUrlEmbed}{assign var="openUrlId" value=$openUrlCounter->increment()}{/if}

{literal}
<script type="text/javascript">
$(document).ready(function() {
    // set the spinner going
    $({/literal}".openUrlLink"{literal}).html('<center><img src="' + path + '/images/loading.gif" /></center>');
        
    var url = "http://{/literal}{$rsi.url}{literal}"+path+"/AJAX/JSON?method=getFullTextAvailability"+
        "&issn="+"{/literal} {$rsi.issn} {literal}"+
        "&isbn="+"{/literal} {$rsi.isbn} {literal}"+
        "&year="+"{/literal} {$rsi.year} {literal}"+ 
        "&volume="+"{/literal} {$rsi.volume} {literal}"+ 
        "&issue="+"{/literal} {$rsi.issue} {literal}"+
        "&institution="+"{/literal}{$rsi.institution}{literal}" ;

        var jqxhr = $.getJSON(url, function(response){
			if (response.status == 'OK') {
            	// Nothing to do. Leave the open URL link as it is.
				$(".openUrlLabel").html("{/literal}{translate text='Get full text'}{literal}");
                $(".openUrlSeparator").html("<br>");
			}
			else {
                $({/literal}".openurl_id\\:{$openUrlId}"{literal}).hide();                    
			}
        });
                
}); 
</script>
{/literal}

<a href="{$openUrlBase|escape}?{$openUrl|escape}" 
{if $openUrlEmbed} 
class="fulltext openUrlEmbed openurl_id:{$openUrlId}"
{elseif $openUrlWindow} 
class="fulltext openUrlWindow window_settings:{$openUrlWindow|escape}"
{/if}
>
  {* put the openUrl here in a span (COinS almost) so we can retrieve it later *}
  <span title="{$openUrl|escape}" class="openUrl"></span>
  {if $openUrlGraphic}
    <img src="{$openUrlGraphic|escape}" class="openUrlLink" alt="{translate text='Get full text'}" style="{if $openUrlGraphicWidth}width:{$openUrlGraphicWidth|escape}px;{/if}{if $openUrlGraphicHeight}height:{$openUrlGraphicHeight|escape}px;{/if}" />
  {else}
    <span class="openUrlLabel"></span>
  {/if}
</a>
{if $openUrlEmbed}
  <div id="openUrlEmbed{$openUrlId}" class="resolver hide">{translate text='Loading...'}</div>
{/if}
