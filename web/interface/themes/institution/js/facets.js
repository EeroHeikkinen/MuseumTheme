function enableDynatree(tree, url)
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
      var query = node.tree.options.query;
      
      $.getJSON(path + "/AJAX/JSON_Facets?" + query,
        {
          method: "getFacets",
          facetName: node.data.facet,
          facetPrefix: node.data.filter,
          facetLevel: level        	
        },
        function(response, textStatus) {
          if (response.status == "OK") {
            var list = response.data;
            res = [];
            for (var i = 0, l = list.length; i < l; i++) {
              var e = list[i];
              res.push({title: e.value + '<span class="facetCount"> (' + e.count + ')</span>', href: e.url, url: e.url, icon: false, 
            	facet: node.data.facet, level: level, filter: e.untranslated, unselectable: true, isLazy: e.children ? true : false});
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
  });
}
