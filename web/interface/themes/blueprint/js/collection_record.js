$(document).ready(function() {
	showMoreInfoToggle();
	$("#moreInfoToggle").click(function(e) {
		e.preventDefault();
		toggleCollectionInfo();
	});
    var collectionID = ($(".collectionID").length == 1) 
        ? $(".collectionID").attr('id').substr('record'.length) : false;
    if (collectionID) {    
	    showMapTab(collectionID);
    }
});

function showMoreInfoToggle() {
	toggleCollectionInfo();
	$("#moreInfoToggle").show();
}

function toggleCollectionInfo() {
	$("#collectionInfo").toggle();
}

function showMapTab(collectionID) {
	$.ajax({
        dataType: 'json',
        url: path + '/AJAX/JSON_GoogleMap?method=getMapData&filter[]=hierarchy_parent_id%3A' + collectionID,
        success: function(response) {
            if(response.status == 'OK') {
                if(response.data.length != 0) {
                	$('#collectionMapTab').removeClass("offscreen");
                }  
            }
        }
    });
}


