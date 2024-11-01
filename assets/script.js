"use strict";

(function ($) {
  $.extend($.fn, {
    wptravelMapquestMap: function wptravelMapquestMap(options) {
      if (this.length > 0) {
        // Get Selector name.
        var mapSelector = this[0].id;
        var selectorPrefix = '#';

        if (!mapSelector) {
          mapSelector = this[0].className;
          selectorPrefix = '.';
        }

        var fullSelector = mapSelector; // End of getting selector name.

        var mapquest = wp_travel.mapquest;
        var lat = options && options.lat ? options.lat : mapquest.latlng.lat;
        var lng = options && options.lng ? options.lng : mapquest.latlng.lng;
        var apiKey = mapquest.apiKey;
        window.addEventListener('load', function () {
          if (lng == '' || lat == '' || apiKey == '') {
            return;
          }

          var Center = [lat, lng];
          L.mapquest.key = mapquest.apiKey;
          var map = L.mapquest.map(fullSelector, {
            center: Center,
            layers: L.mapquest.tileLayer('map'),
            zoom: mapquest.zoomLevel
          });
          var marker = L.marker({
            "lat": Center[0],
            "lng": Center[1]
          });
          marker.addTo(map);
          map.addControl(L.mapquest.control());
        });
      }
    }
  });
})(jQuery);
//# sourceMappingURL=script.js.map
