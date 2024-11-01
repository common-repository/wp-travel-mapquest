'use strict';

(function ($) {
  var mapQuest = wp_travel_drag_drop_uploader.mapquest;
  $(document).ready(function () {
    var initMapQuest = function initMapQuest() {
      var lat = mapQuest.latlng.lat;
      var lng = mapQuest.latlng.lng;
      var Center = [0, 0];
      var apikey = mapQuest.apiKey;

      if (lat != '' && lng != '') {
        Center = [lat, lng];
      }

      var ps = placeSearch({
        key: apikey,
        container: document.querySelector('#mq-search-input'),
        useDeviceLocation: true,
        collection: ['poi', 'airport', 'address', 'adminArea']
      });
      L.mapquest.key = apikey;
      var map = L.mapquest.map('mapQuest', {
        center: Center,
        layers: L.mapquest.tileLayer('map'),
        zoom: mapQuest.zoomLevel
      });
      map.setView({
        lat: Center[0],
        lng: Center[1]
      });
      var marker = L.marker({
        lat: Center[0],
        lng: Center[1]
      });
      marker.addTo(map);
      $("#wp-travel-mq-lat, #wp-travel-mq-lng").on('change', function () {
        var latlng = {
          lat: $('#wp-travel-mq-lat').val(),
          lng: $('#wp-travel-mq-lng').val()
        };
        map.setView({
          lat: latlng.lat,
          lng: latlng.lng
        });
        var marker = L.marker(latlng);
        marker.addTo(map);
      });
      L.mapquest.control().addTo(map);
      var markers = [];
      ps.on('change', function (e) {
        markers.forEach(function (marker, markerIndex) {
          if (markerIndex === e.resultIndex) {
            markers = [marker];
            marker.setOpacity(1);
            map.setView(e.result.latlng, 11);
            $('#wp-travel-mq-lat').val(e.result.latlng.lat);
            $('#wp-travel-mq-lng').val(e.result.latlng.lng);
          } else {
            removeMarker(marker);
          }
        });
      });
      ps.on('results', function (e) {
        markers.forEach(removeMarker);
        markers = [];

        if (e.results.length === 0) {
          map.setView(new L.LatLng(0, 0), 2);
          return;
        }

        e.results.forEach(addMarker);
        findBestZoom();
      });
      ps.on('cursorchanged', function (e) {
        markers.forEach(function (marker, markerIndex) {
          if (markerIndex === e.resultIndex) {
            marker.setOpacity(1);
            marker.setZIndexOffset(1000);
          } else {
            marker.setZIndexOffset(0);
            marker.setOpacity(0.5);
          }
        });
      });
      ps.on('clear', function () {
        console.log('cleared');
        map.setView(new L.LatLng(0, 0), 2);
        markers.forEach(removeMarker);
      });
      ps.on('error', function (e) {
        console.log(e);
      });

      function addMarker(result) {
        var marker = L.marker(result.latlng, {
          opacity: .4
        });
        marker.addTo(map);
        markers.push(marker);
      }

      function removeMarker(marker) {
        map.removeLayer(marker);
      }

      function findBestZoom() {
        var featureGroup = L.featureGroup(markers);
        map.fitBounds(featureGroup.getBounds().pad(0.5), {
          animate: false
        });
      }
    };

    document.getElementById('wp-travel-tab-content-locations').addEventListener('click', initMapQuest);
  });
})(jQuery);
//# sourceMappingURL=admin-script.js.map
