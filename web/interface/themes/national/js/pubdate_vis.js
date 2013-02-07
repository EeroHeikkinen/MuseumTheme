function loadVis(facetFields, searchParams, baseURL, zooming, collection, collectionAction) {

    // get current year so we can set that as a limit when drawing the graph
    var d = new Date();
    var currentYear = d.getFullYear();
    
    // options for the graph, TODO: make configurable
    var options = {
        series: {
            bars: {
                show: true,
                fill: true,
                lineWidth:0,
                fillColor: "#aaaaaa",
                shadow:0
            }
        },
        colors: ["#00a3b5"],
        legend: { noColumns: 2 },
        xaxis: { 
            max: currentYear, 
            tickDecimals: 0, 
            font :{
                size: 13,
                family: "'helvetica neue', helvetica,arial,sans-serif",
                color:'#000',
                weight:'bold'
            }                   
        },
        yaxis: { min: 0, ticks: [] },
        selection: {mode: "x", color:'#00a3b5;'},
        grid: { 
            backgroundColor: null, 
            borderWidth:0,
            axisMargin:0,
            margin:0
        }
    };

    var url = baseURL + '/AJAX/JSON_Vis?method=getVisData&facetFields=' + encodeURIComponent(facetFields) + '&' + searchParams;
    if (typeof collection != 'undefined'){
    	url+= '&collection=' + collection + '&collectionAction='+ collectionAction;
    }
    // AJAX call
    $.getJSON(url, function (data) {
        if (data.status == 'OK') {
            $.each(data['data'], function(key, val) {
            	//check if there is data to display, if there isn't hide the box
            	if(val['data'].length == 0){
            		$("#datevis" + key + "xWrapper").hide();
            		return;
            	}
            	
                // plot graph
                var placeholder = $("#datevis" + key + "x");

                //set up the hasFilter variable
                var hasFilter = true;

                //set the has filter
                if (val['min'] == 0 && val['max']== 0) {
                    hasFilter = false;
                }

                //check if the min and max value have been set otherwise set them to the ends of the graph
                if (val['min'] == 0) {
                    val['min'] = val['data'][0][0] - 5;
                }
                if (val['max']== 0) {
                    val['max'] =  parseInt(val['data'][val['data'].length - 1][0], 10) + 5;
                }

                if (zooming) {
                    //check the first and last elements of the data array against min and max value (+padding)
                    //if the element exists leave it, otherwise create a new marker with a minus one value
                    if (val['data'][val['data'].length - 1][0] != parseInt(val['max'], 10) + 5) {
                        val['data'].push([parseInt(val['max'], 10) + 5, -1]);
                    }
                    if (val['data'][0][0] != val['min'] - 5) {
                        val['data'].push([val['min'] - 5, -1]);
                    }
                    //check for values outside the selected range and remove them by setting them to null
                    for (i=0; i<val['data'].length; i++) {
                        if (val['data'][i][0] < val['min'] -5 || val['data'][i][0] > parseInt(val['max'], 10) + 5) {
                            //remove this
                            val['data'].splice(i,1);
                            i--;
                        }
                    }

                } else {
                    //no zooming means that we need to specifically set the margins
                    //do the last one first to avoid getting the new last element
                    val['data'].push([parseInt(val['data'][val['data'].length - 1][0], 10) + 5, -1]);
                    //now get the first element
                    val['data'].push([val['data'][0][0] - 5, -1]);
                }


                var plot = $.plot(placeholder, [val], options);
                if (hasFilter) {
                    // mark pre-selected area
                    plot.setSelection({ x1: val['min'] , x2: val['max']});
                }
                // selection handler
                placeholder.bind("plotselected", function (event, ranges) {
                    from = Math.floor(ranges.xaxis.from);
                    to = Math.ceil(ranges.xaxis.to);
                    location.href=val['removalURL'] + '&daterange[]=' + key + '&' + key + 'to=' + PadDigits(to,4) + '&' + key + 'from=' + PadDigits(from,4);
                });

                if (hasFilter) {
                    var newdiv = document.createElement('div');
                    var text = document.getElementById("clearButtonText").innerHTML;
                    newdiv.setAttribute('id', 'clearButton' + key);
                    newdiv.innerHTML = '<a href="' + val['removalURL'] + '">x</a>';
                    newdiv.className += "dateVisClear";
                    placeholder.append(newdiv);
                }
            });
        }
    });
}

function PadDigits(n, totalDigits) 
{ 
	var neg = false
	if (n < 0) {
		neg = true;
		n = n.toString().substr(1); 
	} else {
		n = n.toString();
	}
	while (n.length < totalDigits) {
		n = '0' + n;
	}
    return (neg ? '-' : '') + n; 
}