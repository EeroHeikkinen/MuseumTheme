{if $openUrlEmbed}{assign var="openUrlId" value=$openUrlCounter->increment()}{/if}

{literal}
<script type="text/javascript">
$(document).ready(function() {
	// if we are not using RSI return
	var rsi = "{/literal}{$rsi}{literal}";
	if (!rsi) { return; }
	
	// set the spinner going
    $({/literal}".rsi\\:{$openUrlId}"{literal}).html('<center><img src="' + path + '/images/loading.gif" /></center>');
    $({/literal}".openurl_id\\:{$openUrlId}"{literal}).hide();

    var url = path + "/AJAX/JSON?method=getFullTextAvailability"+
        "&issn="+"{/literal}{$rsi.issn}{literal}"+
        "&isbn="+"{/literal}{$rsi.isbn}{literal}"+
        "&year="+"{/literal}{$rsi.year}{literal}"+ 
        "&volume="+"{/literal}{$rsi.volume}{literal}"+ 
        "&issue="+"{/literal}{$rsi.issue}{literal}"+
        "&institute="+"{/literal}{$rsi.institute}{literal}";

        var jqxhr = $.getJSON(url, function(response){
			if (response.status == 'OK') {
            	// Nothing to do. Leave the open URL link as it is.
			    $({/literal}".openurl_id\\:{$openUrlId}"{literal}).show();				
			}
			else {
                $(".openUrlSeparator").hide();
			}

			$({/literal}".rsi\\:{$openUrlId}"{literal}).hide();
			
        })
        .error(function() {
			$({/literal}".rsi\\:{$openUrlId}"{literal}).hide();
            alert("RSI query for full text encountered an error.");
        });              
}); 
</script>
{/literal}


<span class="rsi:{$openUrlId}"></span>

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
    <img src="{$openUrlGraphic|escape}" alt="{translate text='Get full text'}" style="{if $openUrlGraphicWidth}width:{$openUrlGraphicWidth|escape}px;{/if}{if $openUrlGraphicHeight}height:{$openUrlGraphicHeight|escape}px;{/if}" />
  {else}
    {translate text='Get full text'}
  {/if}
</a>
{if $openUrlEmbed}
  <div id="openUrlEmbed{$openUrlId}" class="resolver hide">{translate text='Loading...'}</div>
{/if}
