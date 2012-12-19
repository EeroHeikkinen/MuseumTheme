<!-- START of: Search/rsi.tpl -->

{if $rsi}
{js filename="rsi.js"}
{literal}
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
{/literal}
  checkRSI("{$module}", {if $openUrlAutoCheck}true{else}false{/if},
    {literal}{{/literal}
      "fullText": "{translate text="Full text available"}",
      "peerReviewedFullText": "{translate text="Peer-reviewed full text available"}",
      "noFullText": "{translate text="No full text"}", 
      "maybeFullText": "{translate text="Check full text availability"}", 
      "moreInformation": '<span class="separator"/><span class="more">{translate text="More"}</span>',
      "peerReviewed": "{translate text="Peer-reviewed"}",
    {literal}
  });
});
//]]>
</script>
{/literal}
{/if}

<!-- END of: Search/rsi.tpl -->
