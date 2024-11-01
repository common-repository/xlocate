$ = jQuery;
var map;
/*Markers stores response for markers reutrned via ajax call*/
var markers = [];
/*Is reference to all map markers on the map*/
var map_markers = [];

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

var infoWindowContent = [];
var geocoder = new google.maps.Geocoder;
var infoWindow = new google.maps.InfoWindow();
var bounds;
var loadMoreButton = '#xlocate-load-more';
var inputAddress = "#search-address";
var markerCluster;
var searchForm = $('#search-form');

function setMapMarkers() {
    // Display multiple markers on a map
    var marker, i;
    // Loop through our array of markers & place each one on the map
    for (i = 0; i < markers.length; i++) {
        var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
        bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
            map: map
            // icon: 'http://202.166.207.19/c/digamber/realtor/wp-content/plugins/xlocate-location/assets/public/images/you.png'
            // title: markers[i][0]
        });

        // Allow each marker to have an info window
        google.maps.event.addListener(marker, 'click', (function (marker, i) {
            return function () {
                infoWindow.setContent(infoWindowContent[i][0]);
                infoWindow.open(map, marker);
            };
        })(marker, i));

        map_markers.push(marker);
    }
}

// Sets the map on all markers in the array.
function setMapOnAll(map) {
    for (var i = 0; i < map_markers.length; i++) {
        map_markers[i].setMap(map);
    }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
    setMapOnAll(null);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
    clearMarkers();
    map_markers = [];
}

//Find Current Location
function realEstateGeoFindMe() {
    var output = document.getElementById("out");

    document.getElementById("search-address").value = "Loading.. Please Wait..";

    if (!navigator.geolocation) {
        output.innerHTML = "<p>Geolocation is not supported by your browser</p>";
        return;
    }

    function success(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        /*Reverse Geodcode and Update AutoComplete*/
        reverseGeoCode(latitude, longitude);
        $(searchForm).submit();
        //  output.innerHTML = 'latitude: '+latitude+'longitude: '+longitude;
    }

    function error() {
        output.innerHTML = "Unable to retrieve your location";
    }

    //output.innerHTML = "<p>Locating…</p>";
    navigator.geolocation.getCurrentPosition(success, error);
}

// Get Latitude and Longitude via reverse Geo Code
function reverseGeoCode(lat, lng) {
    jQuery('#lat').val(lat);
    jQuery('#lng').val(lng);
    var latLng = new google.maps.LatLng(lat, lng);
    //var latLng = {lat: lat, lng: lng };
    geocoder.geocode({'location': latLng}, function (results, status) {
        if (status === 'OK') {
            if (results[0]) {
                // Note: Reverse geocoding is not an exact science. The geocoder will attempt to find the closest addressable location within a certain tolerance.
                var formatted_address = results[0].formatted_address;
                /*Show on Search Filed As Well As to be Saved Address Field*/
                jQuery('#search-address').val(formatted_address);
            } else {
                window.alert('No results found');
            }
        } else {
            window.alert('Geocoder failed due to: ' + status);
        }
    });
}

function clearPagination() {
    $(loadMoreButton).data('paged', 1);
    $(loadMoreButton).data('maxpages', 1);
}

function initialize_empty_map() {
    if( $('#map_canvas').length > 0 ) {
        myLat = xloc_default_latitude;
        myLng = xloc_default_longitude;
        var latLng = {lat: myLat, lng: myLng};
        //var latLng = new google.maps.LatLng(lat, lng);
        var mapOptions = {
            mapTypeId: 'roadmap',
            zoom: xloc_default_zoom_level,
            center: latLng
        };

        if (map === undefined) {
            // Display a map on the page
            map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
        }

        google.maps.event.addDomListener(window, "resize", function () {
            var center = map.getCenter();
            google.maps.event.trigger(map, "resize");
            map.setCenter(center);
        });
    }
}

// Main Map Initialize Function
function initialize() {
    bounds = new google.maps.LatLngBounds();

    //var latLng = new google.maps.LatLng(lat, lng);
    // console.log(latLng);
    var mapOptions = {
        mapTypeId: 'roadmap',
        zoom: xloc_default_zoom_level,
        // center: latLng
    };

    if (map === undefined) {
        // Display a map on the page
        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    }

    //add map to markers
    deleteMarkers(); //Added by R2-D2
    setMapMarkers();
    markerCluster = new MarkerClusterer(
        map,
        map_markers,
        {
            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
        }
    );

    // Automatically center the map fitting all markers on the screen
    map.fitBounds(bounds);

    google.maps.event.addDomListener(window, "resize", function () {
        var center = map.getCenter();
        google.maps.event.trigger(map, "resize");
        map.setCenter(center);
    });
}

// Ajax Search Properties Function
function searchProperties(data, paged) {
    var lat = $('#lat').val();
    var lng = $('#lng').val();
    ajaxUrl = rem.ajax_url + '?action=get_xlocate_map';
    if (paged) {
        ajaxUrl = rem.ajax_url + '?action=get_xlocate_map&paged=' + paged;
    } else {
        clearPagination();
    }
    if (lat !== '' && lng !== '') {
        // do ajax call to get map
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: data,
            beforeSend: function () {
                $('#xlocate-loader').show();
            },
            success: function (response) {
                $('#xlocate-loader').hide();

                if (response.mapData.markers instanceof Array) {
                    //Re-initialize Marker to 0

                    //Check if the result is paginated or not if not then dont satisfy me here
                    if (!response.paginated) {
                        markers = []; //Added by R2-D2
                    }

                    //markers = response.mapData.markers;
                    response.mapData.markers.forEach(function (element) {
                        markers.push(element);
                    });
                }

                //console.log(markers);
                //console.log(map_markers);

                if (response.mapData.infowindowcontent instanceof Array) {
                    response.mapData.infowindowcontent.forEach(function (element) {
                        infoWindowContent.push(element);
                    });
                }

                if (response.hide_map == 1) {
                    deleteMarkers();
                    if( typeof markerCluster !== "undefined" ) {
                        markerCluster.clearMarkers();
                    } else {
                        initialize_empty_map();
                    }
                }

                if (response.paginated) {
                    $('#xlocate-estate-listing').append(response.listing);
                    deleteMarkers();
                    setMapMarkers();
                    markerCluster.clearMarkers();
                    markerCluster.addMarkers(map_markers);

                    map.fitBounds(bounds);
                    if (response.maxpages > 1 && response.maxpages !== $(loadMoreButton).data('maxpages')) {
                        $(loadMoreButton).data('maxpages', response.maxpages);
                        $(loadMoreButton).show();
                    }
                } else {
                    if (response.mapData.markers) {
                        initialize(lat, lng);
                        // console.log(markers);
                        // console.log(map_markers);
                        // need to work on push state, comment out for now
                        history.pushState('', '', rem.page_url + '?lat=' + lat + '&lng=' + lng);
                        if (response.maxpages > 1 && response.maxpages !== $(loadMoreButton).data('maxpages')) {
                            $(loadMoreButton).data('maxpages', response.maxpages);
                            $(loadMoreButton).show();
                        }
                    } else {
                        deleteMarkers();
                    }
                    $('#xlocate-estate-listing').html(response.listing).css('overflow-y', 'scroll');
                }

            },
            error: function (MLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

    }
}

jQuery(function ($) {
    /*On Form Submit*/
    $(searchForm).on('submit', function (e) {
        e.preventDefault();
        var data = $(this).serializeArray();
        // console.log(data);
        searchProperties(data);
    });

    /*On Page Load Check if Lat Lng value is predefined*/
    var lat = $('#lat').val();
    var lng = $('#lng').val();
    if (lat !== '' && lng !== '') {
        reverseGeoCode(lat, lng);
        $(searchForm).submit();
    } else {
        //Load empty Map at first
        initialize_empty_map();
    }

    /*Search Autocomplete and Search*/
    // noinspection JSUnusedLocalSymbols
    $(inputAddress).geocomplete({
        details: "#search-form"
        //detailsAttribute: "data-geo"
    }).bind("geocode:result", function (event, result) {
        $(searchForm).submit();
    });

    /*Geo Location*/
    $('#find-location, #xlocate-frontend-find-location').on('click', function (e) {
        e.preventDefault();
        realEstateGeoFindMe();
    });

    $('#map-view').on('click', function (e) {
        e.preventDefault();
        $('#map_wrapper').show();
        $('#xlocate-estate-listing').hide();
        $(searchForm).submit();
    });

    $('#list-view').on('click', function (e) {
        e.preventDefault();
        $('#xlocate-estate-listing').show();
        $('#map_wrapper').hide();
        $(searchForm).submit();
    });

    $(inputAddress).on('click', function () {
        $(this).select();
    });

    $(loadMoreButton).on('click', function (e) {
        e.preventDefault();
        /*I need to now trigger the current query again with posts_per_page parameter set*/
        /* Would it be easier to have a posts_per_page parameter passed ? */
        var data = $(searchForm).serialize();
        var that = $(this);
        var paged = $(this).data('paged') + 1;
        var maxPages = $(that).data('maxpages');
        if (paged === maxPages) {
            $(this).hide();
        } else {
            $(this).data('paged', paged);
        }
        searchProperties(data, paged);
    });

});