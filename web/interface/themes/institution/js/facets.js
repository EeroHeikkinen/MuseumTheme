function enableDynatree(tree, facet, url)
{
  var query = url.split('?')[1];
  $(tree).dynatree({
    isLazy: true,
    query: query,
    onActivate: function(node) {
      if (node.data.url)
        window.location.href = node.data.url;
	},
	onLazyRead: function(node) {
      var level = node.data.level + 1;
      getFacetList(node, node.tree.options.query, node.data.facet, level, node.data.filter);
    }
  });
  var node = $(tree).dynatree("getRoot");
  $('#facet_' + facet).find('.facet_loading').show();
  getFacetList(node, query, facet, 0, '');
}

function getFacetList(node, query, facet, level, prefix)
{
  $.getJSON(path + "/AJAX/JSON_Facets?" + query,
    {
      method: "getFacets",
      facetName: facet,
      facetLevel: level,
      facetPrefix: prefix
    },
    function(response, textStatus) {
      $('#facet_' + facet).find('.facet_loading').hide();
      if (response.status == "OK") {
        var list = response.data;
        res = [];
        for (var i = 0, l = list.length; i < l; i++) {
          var e = list[i];
          res.push({title: e.value + '<span class="facetCount"> (' + e.count + ')</span>', href: e.url, url: e.url, icon: false, 
        	facet: facet, level: level, filter: e.untranslated, unselectable: true, isLazy: e.children ? true : false});
        }
        node.setLazyNodeStatus(DTNodeStatus_Ok);
        node.addChild(res);
      } else {
        node.setLazyNodeStatus(DTNodeStatus_Error, {
          tooltip: response.faultDetails,
          info: response.faultString
        });
      }
    }
  );
}
