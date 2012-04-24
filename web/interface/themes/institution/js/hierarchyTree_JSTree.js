var hierarchyID;
var baseTreeSearchFullURL;
$(document).ready(function() {

    hierarchyID = $("#hierarchyTree").find(".hiddenHierarchyId")[0].value;
    var recordID = $("#hierarchyTree").find(".hiddenRecordId")[0].value;
    var scroller = vufindString.lightboxMode ? '#modalDialog' : '#hierarchyTree';
    var context = $("#hierarchyTree").find(".hiddenContext")[0].value;

    if (!vufindString.fullHierarchy) {
        // Set Up Partial Hierarchy View Toggle
        $('#hierarchyTree').parent().prepend('<a href="#" id="toggleTree" class="closed">' + vufindString.showTree + '</a>');
        $('#toggleTree').click(function(e) {
            e.preventDefault();
            $(this).toggleClass("open");
            $(this).hasClass("open") ? scroll(scroller, "show") : scroll(scroller, "hide");
            $("#hierarchyTree").jstree("toggle_dots");
        });
    }

    $("#hierarchyTree")
    .bind("loaded.jstree", function (event, data) {
        var idList = $('#hierarchyTree .JSTreeID');
        $(idList).each(function() {
            var id = $.trim($(this).text());
            $(this).before('<input type="hidden" class="jsTreeID '+context+ '" value="'+id+'" />');
            $(this).remove();
        });

        $(".Collection").each(function() {
            var id = $(this).attr("value");
            $(this).next("a").click(function(e){
                e.preventDefault();
                $("#hierarchyTree a").removeClass("jstree-clicked");
                $(this).addClass("jstree-clicked");
				//open this node
                $(this).parent().removeClass("jstree-closed").addClass("jstree-open");
                getRecord(id);
                return false;
            });
        });

        $("#hierarchyTree a").click(function(e) {
            e.preventDefault();
            if (context == "Record") {
                window.location = $(this).attr("href");
            }
            if ($('#toggleTree').length > 0 && !$('#toggleTree').hasClass("open")) {
                hideFullHierarchy($(this).parent());
            }
        });

        var jsTreeNode = $(".jsTreeID:input[value='"+recordID+"']").parent();
        // Open Nodes to Current Path
        jsTreeNode.parents("li").removeClass("jstree-closed").addClass("jstree-open");
        // Initially Open Current node too
        jsTreeNode.removeClass("jstree-closed").addClass("jstree-open");
        // Add clicked class
        $("> a", jsTreeNode).addClass("jstree-clicked");
        // Add highlight class to parents
        jsTreeNode.parents("li").children("a").addClass("jstree-highlight");

        if (!vufindString.fullHierarchy) {
            // Initial hide of nodes outside current path
            hideFullHierarchy(jsTreeNode);
            $("#hierarchyTree").jstree("toggle_dots");
        }
        // Scroll to the current record
        $(scroller).delay(300).animate({
            scrollTop: jsTreeNode.offset().top - $(scroller).offset().top + $(scroller).scrollTop()
        });
    })
    .jstree({
        "xml_data" : {
            "ajax" : {
                "url" : path + '/AJAX/AJAX_HierarchyTree?' + $.param({method:'getHierarchyTree', 'hierarchyID': hierarchyID, 'id': recordID, 'context': context, mode: "Tree"}),
                success: function(data) {
                    // Necessary as data is a string
                    var dataAsXML = parseXml(data);
                    if(dataAsXML) {
                        var error = $(dataAsXML).find("error");
                        if (error.length > 0) {
                            showTreeError($(error).text());
                            return false;
                        } else {
                            return data;
                        }
                    } else {
                        showTreeError("Unable to Parse XML");
                    }
                },
                failure: function() {
                    showTreeError("Unable to Load Tree");
                }
            },
            "xsl" : "nest"
        },
        "plugins" : [ "themes", "xml_data", "ui" ],
        "themes" : {
            "url": path + '/interface/themes/institution/js/jsTree/themes/apple/style.css'
        }
    }).bind("open_node.jstree close_node.jstree", function (e, data) {
        $(data.args[0]).find("li").show();
    });

    $('#treeSearch').show();
    $('#treeSearchText').bind('keypress', function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code == 13) {
            //Enter keycode
            //call the search code
            doTreeSearch();
        }

    });
});

// Parse XML (Feature not avilable in JQuery 1.4)
if (typeof window.DOMParser != "undefined") {
    parseXml = function(xmlStr) {
        return ( new window.DOMParser() ).parseFromString(xmlStr, "text/xml");
    };
} else if (typeof window.ActiveXObject != "undefined" &&
       new window.ActiveXObject("Microsoft.XMLDOM")) {
    parseXml = function(xmlStr) {
        var xmlDoc = new window.ActiveXObject("Microsoft.XMLDOM");
        xmlDoc.async = "false";
        xmlDoc.loadXML(xmlStr);
        return xmlDoc;
    };
} else {
    parseXml = function(xmlStr) {
        return false;
    };
}

function showTreeError(msg) {
    $("#hierarchyTreeHolder").html('<p class="error">' + msg + '</p>');
}

function getRecord(recordID) {
    $.ajax({
      url: path + '/AJAX/AJAX_HierarchyTree?' + $.param({method:'getRecord', id: recordID}),
      dataType: 'html',
      success: function(response) {
        if (response) {
            $('#hierarchyRecord').html(response);
            // Remove the old path highlighting
            $('#hierarchyTree a').removeClass("jstree-highlight");
            // Add Current path highlighting
            var jsTreeNode = $(":input[value='"+recordID+"']").parent();
            jsTreeNode.children("a").addClass("jstree-highlight");
            jsTreeNode.parents("li").children("a").addClass("jstree-highlight");
        }
      }
    });
}

function scroll(scroller, mode) {
    // Get the currently cicked item
    var jsTreeNode = $(".jstree-clicked").parent('li');
    // Toggle display of closed nodes
    $('#hierarchyTree li.jstree-closed').toggle();
    if (mode == "show") {
        $('#hierarchyTree li').show();
        $(scroller).animate({
            scrollTop: -$(scroller).scrollTop()
        });
        $('#toggleTree').html(vufindString.hideTree);
    } else {
        hideFullHierarchy(jsTreeNode);
        $(scroller).animate({
            scrollTop: $(jsTreeNode).offset().top - $(scroller).offset().top + $(scroller).scrollTop()
        });
        $('#toggleTree').html(vufindString.showTree);
    }
}

function hideFullHierarchy(jsTreeNode) {
    // Hide all nodes
    $('#hierarchyTree li').hide();
    // Show the nodes on the current path
    $(jsTreeNode).show().parents().show();
    // Show the nodes below the current path
    $(jsTreeNode).find("li").show();
}

function changeNoResultLabel(display){
    if (display){
        $("#treeSearchNoResults").show();
    }
    else {
        $("#treeSearchNoResults").hide();
    }
}

function changeLimitReachedLabel(display){
    if (display){
        $("#treeSearchLimitReached").show();
    }
    else {
        $("#treeSearchLimitReached").hide();
    }
}

function doTreeSearch(){
    var keyword = $("#treeSearchText").val();
    if (keyword == ""){
        changeNoResultLabel(true);
        return;
    }
    var searchType = $("#treeSearchType").val();

    $("#treeSearchLoadingImg").show();
    $.getJSON(path + '/AJAX/AJAX_HierarchyTree?' + $.param({method:'searchTree', 'lookfor': keyword, 'hierarchyID': hierarchyID, 'type': searchType}), function(results) {
    	
    	if(results["limitReached"] == true){
    		if(typeof(baseTreeSearchFullURL) == "undefined" || baseTreeSearchFullURL == null){
    			baseTreeSearchFullURL = $("#fullSearchLink").attr("href");
    		}
    		$("#fullSearchLink").attr("href", baseTreeSearchFullURL + "?lookfor="+ keyword + "&filter[]=hierarchy_top_id:\"" + hierarchyID  + "\"");
    		changeLimitReachedLabel(true);
    	}
    	else {
    		changeLimitReachedLabel(false);
    	}
    	
        if (results["results"].length >= 1){
            $("#hierarchyTree .jstree-search").removeClass("jstree-search");
            $("#hierarchyTree").jstree("close_all", hierarchyID);
            changeNoResultLabel(false);
        }
        else{
            $("#hierarchyTree .jstree-search").removeClass("jstree-search");
            changeNoResultLabel(true);
        }
        
        $.each(results["results"], function(key, val){

        	
            var jsTreeNode = $(".jsTreeID:input[value="+val+"]").parent();
            if (jsTreeNode.hasClass("jstree-closed")) {
            	jsTreeNode.removeClass("jstree-closed").addClass("jstree-open");
            }
            jsTreeNode.show().children('a:first').addClass("jstree-search");
            var parents = $(jsTreeNode).parents();
            parents.each(function() {
            	if ($(this).hasClass("jstree-closed")) {
                	$(this).removeClass("jstree-closed").addClass("jstree-open");
                }
            	$(this).show();
            });            
        });
        if (results["results"].length == 1){
            $("#hierarchyTree .jstree-clicked").removeClass("jstree-clicked");
            //only do this for collection pages
            if($(".Collection").length != 0){
            	getRecord(results["results"][0]);
        	}
        }
        $("#treeSearchLoadingImg").hide();
    });
}
//Code for the search button
$(function () {
    $("#treeSearch input").click(function () {
        switch(this.id) {
            case "search":
                doTreeSearch();
                break;
            default:
                break;
        }
    });
});
