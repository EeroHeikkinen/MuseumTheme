<!-- START of: Record/bx.tpl -->

{literal}
<script type="text/javascript">
  //<![CDATA[
  var openurl_resolver = "{/literal}{$openUrlBase}{literal}"; 

    var url = path + "/AJAX/JSON_bXRecommendations?method=getbXRecommendations"+
        "&id="+"{/literal}{$id|escape}{literal}&source={/literal}{$module|escape}{literal}";
    
    var jqxhr = $.getJSON(url, function(response) {
        if (response.status == 'OK') {
            if (response.data.length > 0) {
                $('#bXRecommendations').removeClass("hide");
            }
            var list = $('#bXRecommendations ul');
            for (var i = 0; i < response.data.length; i++) {
                item = response.data[i];
                var span = $('<span/>');
                if (item.openurl) {
                    var a = $('<a/>');
                    a.attr('href', openurl_resolver + '?' + item.openurl); 
                    a.text(item.atitle);
                    span.append(a);
                } else {
                    span.text(item.atitle);
                }
                var listItem = $('<li/>');
                listItem.append(span);
                if (item.date) {
                    listItem.append(' (' + item.date + ')');
                }
                list.append(listItem);
            }
        }
    })
    .error(function() {
        $('#bXRecommendations').removeClass("hide").text("Request for bX recommendations failed.");
    });              
  //]]>
</script>
{/literal}

<div id="bXRecommendations" class="bXRecommendations sidegroup hide">
  <h4>{translate text="bX Recommendations"}</h4>
  <ul class="similar">
    <li> </li>
  </ul>
</div>

<!-- END of: Record/bx.tpl -->
