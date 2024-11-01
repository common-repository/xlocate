$ = jQuery;
var map;
var markers = [];
var geocoder = new google.maps.Geocoder;
var infowindow = new google.maps.InfoWindow;

//Meta Values
var xloc_default_latitude = 27.7090319;
if( typeof settings_data.default_latitude != 'undefined' ) {
    xloc_default_latitude = parseFloat(settings_data.default_latitude);
}
var xloc_default_longitude = 85.2911132;
if( typeof settings_data.default_longitude != 'undefined' ) {
    xloc_default_longitude = parseFloat(settings_data.default_longitude);
}
var xloc_default_zoom_level = 15;
if( typeof settings_data.default_zoom_level != 'undefined' ) {
    xloc_default_zoom_level = parseInt(settings_data.default_zoom_level);
}

// Update Latitude Longitude on Map Marker events
function xLocDraggedListener(lat, lng) {
    //Save Latitude and Longitude
    var latLng = {lat: lat, lng: lng};
    jQuery('#lat').val(lat);
    jQuery('#lng').val(lng);
    geocoder.geocode({'location': latLng}, function (results, status) {
        if (status === 'OK') {
            if (results[0]) {
                // Note: Reverse geocoding is not an exact science. The geocoder will attempt to find the closest addressable location within a certain tolerance.
                var formatted_address = results[0].formatted_address;

                /*Show on Search Filed As Well As to be Saved Address Field*/
                jQuery('#formatted_address').val(formatted_address);
                jQuery('#search-address').val(formatted_address);

            } else {
                window.alert('No results found');
            }
        } else {
            window.alert('Geocoder failed due to: ' + status);
        }
    });
}

// Add Marker to Map
function addMarker(myLatLng, map) {
    var marker = new google.maps.Marker({
        draggable: true,
        position: myLatLng,
        map: map,
    });
    markers.push(marker);
    google.maps.event.addListener(marker, 'dragend', function (event) {
        var lat = this.getPosition().lat();
        var lng = this.getPosition().lng();
        xLocDraggedListener(lat, lng);
    });
}

// Sets the map on all markers in the array.
function setMapOnAll(map) {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
    setMapOnAll(null);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
    clearMarkers();
    markers = [];
}

//Initialize map
function initMap() {

    if( $('#map-canvas').length === 0 ){
        return false;
    }
    /*Prepopulate with initial values if already exists*/
    var myLat = $('#lat').val();
    var myLng = $('#lng').val();
    /*console.log( myLat + myLng )*/
    if (myLat == '' && myLng == '') {
        myLat = xloc_default_latitude;
        myLng = xloc_default_longitude;
    }
    else {
        myLat = parseFloat(myLat);
        myLng = parseFloat(myLng);
    }
    var myLatLng = {lat: myLat, lng: myLng};
    //get default location
    if (map == undefined) {
        map = new google.maps.Map(document.getElementById('map-canvas'), {
            zoom: xloc_default_zoom_level,
            center: myLatLng
        });

        // add marker to map on click
        map.addListener('click', function (event) {
            deleteMarkers();
            addMarker(event.latLng, map);
            xLocDraggedListener(event.latLng.lat(), event.latLng.lng());
        });

        //Geo Complete Search Results
        $("#search-address").geocomplete({
            details: "#xlocate-map-wrapper",
            detailsAttribute: "data-geo"
        }).bind("geocode:result", function (event, result) {
            xLocDraggedListener(result.geometry.location.lat(), result.geometry.location.lng());
            deleteMarkers();
            addMarker(result.geometry.location, map);
            map.panTo(result.geometry.location);
        });
        ;

    } else {
        deleteMarkers();
    }

    addMarker(myLatLng, map);
    map.panTo(myLatLng);
}//init map

//Find Current Location
function xLocGeoFindMe() {
    var output = document.getElementById("out");

    if (!navigator.geolocation) {
        output.innerHTML = "<p>Geolocation is not supported by your browser</p>";
        return;
    }

    function success(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        output.innerHTML = '<p>Latitude is ' + latitude + '° <br>Longitude is ' + longitude + '°</p>';
        $('#lat').val(latitude);
        $('#lng').val(longitude);
        initMap();
        //xLocDraggedListener(latitude, longitude);
    }

    function error() {
        output.innerHTML = "Unable to retrieve your location";
    }

    output.innerHTML = "<p>Locating…</p>";

    navigator.geolocation.getCurrentPosition(success, error);
}

jQuery(function ($) {
    //init map with correct lat long
    initMap();

    $('#find-location').on('click', function (e) {
        e.preventDefault();
        xLocGeoFindMe();
    });
});