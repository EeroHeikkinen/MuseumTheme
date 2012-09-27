<!-- START of: Record/view-googlemap.tpl -->

{js filename="jquery-1.8.0.min.js"}

{literal}
<script type="text/javascript">

  function initialize() {
    var markersData = {/literal}{$map_marker}{literal};
    var latlng = new google.maps.LatLng(0, 0);
    var myOptions = {
      zoom: 1,
      center: latlng,
      mapTypeControl: true,
      mapTypeControlOptions: {
          style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
        },
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"),
      myOptions);

    for (var i = 0; i < markersData.length; i++){
      var disTitle = markersData[i].title;
      var iconTitle = disTitle;
      if (disTitle.length > 25) {
          iconTitle = disTitle.substring(0,25) + "...";
      }
      var latLng = new google.maps.LatLng(markersData[i].lat , markersData[i].lon)
      var marker = new google.maps.Marker({
        position: latLng,
        map: map,
        title: disTitle
      });
    }
  }

  $(document).ready(function() {
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = 'https://maps.googleapis.com/maps/api/js?sensor=false&' +
        'callback=initialize';
    document.body.appendChild(script);
  });
</script>
{/literal}
<div id="wrap" style="width: 674px; height: 479px">
  <div id="map_canvas" style="width: 100%; height: 100%"></div>
</div>

<!-- END of: Record/view-googlemap.tpl -->
