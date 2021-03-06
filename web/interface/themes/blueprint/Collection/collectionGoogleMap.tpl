<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.5&sensor=false&language={$userLang}"></script>
<script type="text/javascript" src="https://google-maps-utility-library-v3.googlecode.com/svn/tags/markerclustererplus/2.0.3/src/markerclusterer_packed.js"></script>
<!--[if lte IE 6]><link rel="stylesheet" href="{$url}/interface/themes/blueprint/css/ie6.css" type="text/css" media="screen, projection"><![endif]-->
{literal}
<script type="text/javascript">
/**
 * Overriding clusterer default function for determining the label text and style
 * for a cluster icon.
 *
 * @param {Array.<google.maps.Marker>} markers The array of represented by the cluster.
 * @param {number} numStyles The number of marker styles available.
 * @return {ClusterIconInfo} The information resource for the cluster.
 * @constant
 * @ignore
 */
MarkerClusterer.CALCULATOR = function (markers, numStyles) {
  var index = 0;
  var count = markers.length.toString();
  var dispText = 0;
  for (calcMarker in markers){
    dispText = dispText + parseInt(markers[calcMarker].getTitle());
  }
  var dv = count;
  while (dv !== 0) {
    dv = parseInt(dv / 10, 10);
    index++;
  }

  index = Math.min(index, numStyles);
  return {
    text: dispText.toString(),
    index: index
  };
};

/**
 * Overriding clusterer adding the icon to the DOM.
 */
ClusterIcon.prototype.onAdd = function () {
  var cClusterIcon = this;

  this.div_ = document.createElement("div");
  this.div_.className = "clusterDiv";
  if (this.visible_) {
    this.show();
  }

  this.getPanes().overlayMouseTarget.appendChild(this.div_);

  google.maps.event.addDomListener(this.div_, "click", function () {
    var mc = cClusterIcon.cluster_.getMarkerClusterer();
    google.maps.event.trigger(mc, "click", cClusterIcon.cluster_);
    google.maps.event.trigger(mc, "clusterclick", cClusterIcon.cluster_); // deprecated name

    // The default click handler follows. Disable it by setting
    // the zoomOnClick property to false.
    var mz = mc.getMaxZoom();
    if (mc.getZoomOnClick()) {
      // Zoom into the cluster.
      mc.getMap().fitBounds(cClusterIcon.cluster_.getBounds());
      // Don't zoom beyond the max zoom level
      if (mz && (mc.getMap().getZoom() > mz)) {
        mc.getMap().setZoom(mz + 1);
      }
    }
  });

  google.maps.event.addDomListener(this.div_, "mouseover", function () {
    var mc = cClusterIcon.cluster_.getMarkerClusterer();
    google.maps.event.trigger(mc, "mouseover", cClusterIcon.cluster_);
  });

  google.maps.event.addDomListener(this.div_, "mouseout", function () {
    var mc = cClusterIcon.cluster_.getMarkerClusterer();
    google.maps.event.trigger(mc, "mouseout", cClusterIcon.cluster_);
  });
};

/**
 * Overriding the image path for ssl
 *
 * The default root name for the marker cluster images.
 *
 * @type {string}
 * @constant
 */
MarkerClusterer.IMAGE_PATH = "https://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclustererplus/images/m";

var markers;
var mc;
var markersData;
var latlng;
var myOptions;
var map;
var infowindow = new google.maps.InfoWindow({maxWidth: 480, minWidth: 480});
  function initialize() {
    //alert('go: ' + '{/literal}{$url}{literal}' + '/AJAX/JSON_Map?method=getMapData&' + '{/literal}{$searchParams}{literal}');
    $.getJSON('{/literal}{$url}{literal}' + '/AJAX/JSON_GoogleMap?method=getMapData&' + '{/literal}{$searchParams}{literal}', function(data){
      markersData = data['data'];
      latlng = new google.maps.LatLng(0, 0);
      myOptions = {
        zoom: 1,
        center: latlng,
        mapTypeControl: true,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
          },
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      map = new google.maps.Map(document.getElementById("map_canvas"),
          myOptions);
      //mc = new MarkerClusterer(map);
      showMarkers();
      var checkbx = document.getElementById("useCluster");
      var wrap = document.getElementById("mapWrap");
      wrap.style.display = "block";
      checkbx.style.display = "block";
    });
  }
  function showMarkers(){
    deleteOverlays();
    if(mc != null) {
      mc.clearMarkers();
    }
    markers = [];

    for (var i = 0; i<markersData.length; i++){
      var disTitle = markersData[i].title;
      var iconSize = "0.5";
      if (disTitle>99){
          iconSize = "0.75";
      }
      var markerImg = "https://chart.googleapis.com/chart?chst=d_map_spin&chld="+iconSize+"|0|F44847|10|_|" +  disTitle;
      var labelXoffset = 1 + disTitle.length * 4;
      var latLng = new google.maps.LatLng(markersData[i].lat , markersData[i].lon)
      var marker = new google.maps.Marker({//MarkerWithLabel
        loc_facet: markersData[i].location_facet,
        position: latLng,
        map: map,
        title: disTitle,
        icon: markerImg
      });
      google.maps.event.addListener(marker, 'click', function() {
        infowindow.close();
        //infowindow.setContent(this.html);
        //infowindow.open(map, this);
        load_content(this);
      });
      markers.push(marker);
    }
    if (document.getElementById("usegmm").checked) {
      mc = new MarkerClusterer(map, markers);
    } else {
      for (var i = 0; i < markers.length; i++) {
        map.addOverlay(markers[i]);
      }
    }
  }
  function load_content(marker){
    var xmlhttp;
    if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safarihttp://www.google.ie/search?hl=en&cp=10&gs_id=2i&xhr=t&q=php+cast+string+to+int&pq=php+int+to+string&gs_sm=&gs_upl=&bav=on.2,or.r_gc.r_pw.&biw=1876&bih=1020&um=1&ie=UTF-8&tbm=isch&source=og&sa=N&tab=wi
      xmlhttp=new XMLHttpRequest();
    }
    else{// code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    var ajaxUrl = '{/literal}{$url}{literal}/AJAX/CollectionGoogleMapInfo?limit=5&filter[]=long_lat%3A"' + marker.loc_facet + '"&filter[]=in_collection%3A"{/literal}{$collectionName}{literal}"&collection={/literal}{$collectionID}{literal}';
    xmlhttp.open("GET", ajaxUrl, false);
    xmlhttp.send();

    infowindow.setContent(xmlhttp.responseText);
    infowindow.open(map, marker);
  }
  function deleteOverlays() {
      if (markers) {
        for (i in markers) {
          markers[i].setMap(null);
        }
        markers.length = 0;
      }
  }
  function refreshMap() {
    showMarkers();
  }

  google.maps.event.addDomListener(window, 'load', initialize);

</script>
{/literal}
  {if $topRecommendations}
    {foreach from=$topRecommendations item="recommendations"}
      {include file=$recommendations}
    {/foreach}
  {/if}
<div id="mapWrap" style="width: 682px; height: 479px">
  <div id="map_canvas" style="width: 100%; height: 100%"></div>
  <div class="mapClusterToggle" id="useCluster" style="display:none;" >
    <input type="checkbox" id="usegmm" checked="true" onclick="refreshMap();" style="vertical-align:middle;"></input><label for="usegmm" style="padding-left:2px;">Cluster</label>
  </div>
</div>
