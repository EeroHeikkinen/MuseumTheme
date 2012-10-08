$(document).ready(function(){
    // create the slider for the publish date facet
	$("#publishDateSlider").jslider({ from: 0, to: 2020, 
									 heterogeneity: ['50/1800', '75/1910'], 
        							 scale: [0, '|', 900, '|', '1800', '|', 1910, '|', 2020], 
        							 limits: false, step: 1, 
        							 dimension: '', 
        							 format: {locale: 'fi'},
        							 callback: function(){
        							 updateFields();
        						   }
        });
        $('#publishDatefrom, #publishDateto').change(function(){
            updateSlider();
        });

        $("form").submit(function() {
        	$('#publishDateSlider').attr("disabled", "disabled");
  	        return true; // ensure form still submits
   	    });

        
});

function updateFields() {
	var values = $("#publishDateSlider").jslider("value");	
	var limits = values.split(";");
    $('#publishDatefrom').val(limits[0]);
    $('#publishDateto').val(limits[1]);
}

function updateSlider() {
    var from = parseInt($('#publishDatefrom').val());
    var to = parseInt($('#publishDateto').val());
    var min = 0;
    if (!from || from < min) {
        from = min;
    }

    if (to && from > to) {
        to = from;
    }
    
    // update the slider with the new min/max/values
    $("#publishDateSlider").jslider("value", from, to);	
}