$(document).ready(function() {
  $("#selectionMap").geomap({
    center: [27, 66], 
    scroll: "off",
    shift: "dragBox",
    zoom: 4,
    zoomMin: 1,
    zoomMax: 17,
    shape: function(e, geo) {
      var coordinates = '';
      if ($("#selectionMap").geomap("option", "mode") == 'drawPolygon') {
        for (var i = 0; i < geo.coordinates[0].length; i++) {
          if (coordinates) {
            coordinates += ',';
          }
          var c = geo.coordinates[0][i];
          coordinates += c[0] + ' ' + c[1];  
        }
        coordinates = 'POLYGON((' + coordinates + '))';
      } else {
        if (geo.type == 'Point') {
          coordinates = geo.coordinates[0] + ' ' + geo.coordinates[1];  
        } else {
          var c1 = geo.coordinates[0][0];
          var c2 = geo.coordinates[0][2];
          coordinates = c1[0] + ' ' + c1[1] + ' ' + c2[0] + ' ' + c2[1];
        }
      }
      $("#coordinates").attr("value", coordinates);
      $("#selectionMap").geomap("empty").geomap("append", geo);
      $("#mapPan").click();
    },
    bboxchange: function(e, geo) {
      $("#zoomPath").slider("option", "value", $("#selectionMap").geomap("option", "zoom")); 
    }
  });
  $("#selectionMapTools input").click(function() {
    $("#selectionMap").geomap("option", "mode", $(this).val());
    $("#selectionMapHelp").children().hide();
    switch ($(this).val()) {
      case 'pan': $("#selectionMapHelpPan").show(); break;
      case 'drawPolygon': $("#selectionMapHelpPolygon").show(); break;
      case 'dragBox': $("#selectionMapHelpRectangle").show(); break;
    }
  });
  $("#zoomControlPlus").click(function() {
    $("#selectionMap").geomap("zoom", 1);
    $("#zoomPath").slider("option", "value", $("#selectionMap").geomap("option", "zoom")); 
  });
  $("#zoomControlMinus").click(function() {
    $("#selectionMap").geomap("zoom", -1);
    $("#zoomPath").slider("option", "value", $("#selectionMap").geomap("option", "zoom")); 
  });
  $("#zoomSlider").bind('dblclick', function(e) {
    e.preventDefault();
  });
  
  var sliderElement = $("#zoomPath");
  sliderElement.slider({
    orientation: "vertical",
    min: parseInt($("#selectionMap").geomap("option", "zoomMin")),
    max: parseInt($("#selectionMap").geomap("option", "zoomMax")),
    value: $("#selectionMap").geomap("option", "zoom"),
    stop: function() {
      $("#selectionMap").geomap("option", "zoom", parseInt(sliderElement.slider("option", "value")));
    }
  });
});
