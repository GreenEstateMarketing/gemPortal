<script src="https://maps.googleapis.com/maps/api/js?key={{ setting('google_map_api_key') }}&libraries=places,geometry"></script>

<script type="text/javascript">
    "use strict";
    var map;
    var marker;
    var lat;
    var lng;
    var myLatlng;
    var geocoder;
    var infowindow;
    var global_arr = [];
    var counter = 0;
    var di = 0;
    var bermudaTriangle = [];
    var count_shapes = 0;
    var random;
    var currentPolygon;
    var polygonDrawn = false; // <-- Track when polygon is drawn

    navigator.geolocation.getCurrentPosition(
        function (position) {
            let coords = position.coords;
            lat = coords.latitude;
            lng = coords.longitude;
        },
        function (error) {
            if (error.code == error.PERMISSION_DENIED) {
                document.getElementById('map-container').innerHTML =
                    '<p class="center alert alert-danger">Location access is required to display the map. Please enable location services in your browser settings.</p>';
            }
        }
    );

    $(document).ready(function () {

        function setpVal(pos) {
            let coords = pos.coords;
            $("#timestamp").text(new Date(pos.timestamp));
            lat = coords.latitude;
            lng = coords.longitude;
        }

        function initMap() {
            var editlat = $('#latitude').val();
            var editLng = $('#longitude').val();

            if (editlat > 0 && editLng > 0) {
                lat = editlat;
                lng = editLng;
            } else {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(setpVal);
                }
            }

            myLatlng = new google.maps.LatLng(lat, lng);
            geocoder = new google.maps.Geocoder();
            infowindow = new google.maps.InfoWindow();
            var mapOptions = {
                zoom: 18,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("map"), mapOptions);

            // Custom zoom controls
            const zoomInControl = document.createElement("button");
            zoomInControl.textContent = "+";
            zoomInControl.classList.add("zoom-control", "btn", "btn-primary");
            zoomInControl.type = "button";
            map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(zoomInControl);
            zoomInControl.addEventListener("click", () => {
                map.setZoom(map.getZoom() + 1);
            });

            const zoomOutControl = document.createElement("button");
            zoomOutControl.textContent = "-";
            zoomOutControl.classList.add("zoom-control", "btn", "btn-primary", "mr-1");
            zoomOutControl.type = "button";
            map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(zoomOutControl);
            zoomOutControl.addEventListener("click", () => {
                map.setZoom(map.getZoom() - 1);
            });

            var iconBase = '/themes/real-scout/images/generic-3.png';
            var icon = {
                url: iconBase,
                scaledSize: new google.maps.Size(32, 37),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(0, 0)
            };

            marker = new google.maps.Marker({
                map: map,
                position: myLatlng,
                draggable: true,
                icon: icon
            });

            attachDragEndListener(marker);

            geocoder.geocode({'latLng': myLatlng}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK && results[0]) {
                    $('#latitude,#longitude').show();
                    $('#location').val(results[0].formatted_address);
                    $('#latitude').val(marker.getPosition().lat());
                    $('#longitude').val(marker.getPosition().lng());
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(map, marker);
                    $('#latitude').trigger('change');
                }
            });

            const input = document.getElementById("location");
            const searchBox = new google.maps.places.SearchBox(input);

            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });

            let markers = [];
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();
                if (places.length == 0) return;

                markers.forEach((m) => m.setMap(null));
                markers = [];
                const bounds = new google.maps.LatLngBounds();

                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    const location = place.geometry.location;

                    var agent_area_edit = $("#agent_area").val();
                    if (agent_area_edit != "" && bermudaTriangle.length > 0) {
                        var isWithInPolygon = false;
                        for (var i = 0; i < bermudaTriangle.length; i++) {
                            if (google.maps.geometry.poly.containsLocation(location, bermudaTriangle[i])) {
                                isWithInPolygon = true;
                                break;
                            }
                        }

                        if (isWithInPolygon) {
                            $(":submit").prop('disabled', false);
                            $("#messageText").addClass("d-none");
                            if (marker) marker.setMap(null);
                            marker = new google.maps.Marker({
                                map: map,
                                position: location,
                                draggable: true,
                                icon: icon
                            });
                            attachDragEndListener(marker);

                            geocoder.geocode({'latLng': location}, function (results, status) {
                                if (status === google.maps.GeocoderStatus.OK && results[0]) {
                                    $('#location').val(results[0].formatted_address);
                                    $('#latitude').val(marker.getPosition().lat());
                                    $('#longitude').val(marker.getPosition().lng());
                                    infowindow.setContent(results[0].formatted_address);
                                    infowindow.open(map, marker);
                                    $('#latitude').trigger('change');
                                }
                            });
                        } else {
                            handleMarkerOutsidePolygon();
                        }
                    }

                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(location);
                    }
                });

                map.fitBounds(bounds);
            });

            // Only enable map clicks after polygon is drawn
            map.addListener('click', function (event) {
                console.log('clicked the map', polygonDrawn, bermudaTriangle.length, event.latLng.lat(), event.latLng.lng());
                if (!polygonDrawn || bermudaTriangle.length === 0) {
                    $("#messageText").removeClass("d-none");
                    $("#messageText").text("Please select an area first.");
                    return;
                } else {
                    $("#messageText").addClass("d-none");
                    $("#messageText").text("");
                }

                var isWithInPolygon = false;
                for (var i = 0; i < bermudaTriangle.length; i++) {
                    if (google.maps.geometry.poly.containsLocation(event.latLng, bermudaTriangle[i])) {
                        isWithInPolygon = true;
                        break;
                    }
                }
                if (isWithInPolygon) {
                    $(":submit").prop('disabled', false);
                    $("#messageText").addClass("d-none");
                    if (marker) marker.setMap(null);
                    marker = new google.maps.Marker({
                        map: map,
                        position: event.latLng,
                        draggable: true,
                        icon: icon
                    });
                    attachDragEndListener(marker);
                    $('#latitude').val(event.latLng.lat());
                    $('#longitude').val(event.latLng.lng());
                    $('#latitude').trigger('change');
                    geocoder.geocode({'latLng': event.latLng}, function (results, status) {
                        if (status === google.maps.GeocoderStatus.OK && results[0]) {
                            $('#location').val(results[0].formatted_address);
                            infowindow.setContent(results[0].formatted_address);
                            infowindow.open(map, marker);
                        }
                    });
                } else {
                    handleMarkerOutsidePolygon();
                }
            });
        }

        function attachDragEndListener(marker) {
            google.maps.event.addListener(marker, 'dragend', function (event) {
                var coordinate = marker.getPosition();
                var agent_area_edit = $("#agent_area").val();
                var isWithInPolygon = false;
                if (agent_area_edit != "" && bermudaTriangle.length > 0) {
                    for (var i = 0; i < bermudaTriangle.length; i++) {
                        isWithInPolygon = google.maps.geometry.poly.containsLocation(
                            event.latLng,
                            bermudaTriangle[i]
                        );
                        if (isWithInPolygon) break;
                    }
                    if (isWithInPolygon) {
                        $(":submit").prop('disabled', false);
                        $("#messageText").addClass("d-none");
                    } else {
                        handleMarkerOutsidePolygon();
                    }
                }
                geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK && results[0]) {
                        $('#location').val(results[0].formatted_address);
                        $('#latitude').val(marker.getPosition().lat());
                        $('#longitude').val(marker.getPosition().lng());
                        infowindow.setContent(results[0].formatted_address);
                        infowindow.open(map, marker);
                        $('#latitude').trigger('change');
                    }
                });
            });
        }

        function handleMarkerOutsidePolygon() {
            $(":submit").prop('disabled', true);
            $("#messageText").removeClass("d-none");
            $("#messageText").text("Please Select Location Inside the polygon");

            if (marker) marker.setMap(null);

            // Set fallback to first point in first polygon, or map center
            var fallback = (bermudaTriangle.length > 0 && bermudaTriangle[0].getPath().getLength() > 0)
                ? bermudaTriangle[0].getPath().getAt(0)
                : map.getCenter();

            var iconBase = '/themes/real-scout/images/generic-3.png';
            var icon = {
                url: iconBase,
                scaledSize: new google.maps.Size(32, 37),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(0, 0)
            };

            marker = new google.maps.Marker({
                map: map,
                position: fallback,
                draggable: true,
                icon: icon
            });

            attachDragEndListener(marker);
        }

        initMap();

        function drawPolygonArea() {
            // Reset polygons if already drawn
            bermudaTriangle.forEach(poly => poly.setMap(null));
            bermudaTriangle = [];
            global_arr = [];
            counter = 0;
            count_shapes = 0;
            random = null;
            polygonDrawn = false;

            var agent_area_edit = $("#agent_area").val();
            var latlngbounds = new google.maps.LatLngBounds();
            if (agent_area_edit != "") {
                var dataObj = JSON.parse(agent_area_edit);
                var type = dataObj.type;

                // For MultiPolygon or Polygon
                if (type === "Polygon") {
                    var list_data = [];
                    $.each(dataObj.coordinates[0], function (index, data) {
                        // Assuming GeoJSON [lng, lat] format
                        var latlng = new google.maps.LatLng(data[1], data[0]);
                        list_data.push({lat: data[1], lng: data[0]});
                        latlngbounds.extend(latlng);
                    });
                    random = new google.maps.LatLng(list_data[0].lat, list_data[0].lng);

                    var polygon = new google.maps.Polygon({
                        paths: list_data,
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#FF0000",
                        fillOpacity: 0.35,
                        clickable: false,
                        zIndex: 1 // help with event propagation
                    });
                    polygon.setMap(map);
                    bermudaTriangle.push(polygon);
                } else {
                    // Handle MultiPolygon if needed
                    $.each(dataObj.coordinates, function (polygonIndex, polyCoords) {
                        var list_data = [];
                        $.each(polyCoords[0], function (index, data) {
                            var latlng = new google.maps.LatLng(data[1], data[0]);
                            list_data.push({lat: data[1], lng: data[0]});
                            latlngbounds.extend(latlng);
                        });
                        if (!random) random = new google.maps.LatLng(list_data[0].lat, list_data[0].lng);
                        var polygon = new google.maps.Polygon({
                            paths: list_data,
                            strokeColor: "#FF0000",
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: "#FF0000",
                            fillOpacity: 0.35,
                            clickable: false,
                            zIndex: 1
                        });
                        polygon.setMap(map);
                        bermudaTriangle.push(polygon);
                    });
                }
                map.fitBounds(latlngbounds);
                polygonDrawn = true;
            }
        }

        // Listen to area selection dropdown change
        $('#agent_area').on('change', function () {
            drawPolygonArea();

            // Move marker to a valid spot
            if (marker) marker.setMap(null);
            var iconBase = '/themes/real-scout/images/generic-3.png';
            var icon = {
                url: iconBase,
                scaledSize: new google.maps.Size(32, 37),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(0, 0)
            };
            marker = new google.maps.Marker({
                map: map,
                position: random || map.getCenter(),
                draggable: true,
                icon: icon
            });
            attachDragEndListener(marker);
            $("#messageText").addClass("d-none");
            $(":submit").prop('disabled', false);
        });

        // Initial polygon drawing if value already present
        var agent_area_edit = $("#agent_area").val();
        if (agent_area_edit != "") {
            drawPolygonArea();

            // Move marker to a valid spot
            if (marker) marker.setMap(null);
            var iconBase = '/themes/real-scout/images/generic-3.png';
            var icon = {
                url: iconBase,
                scaledSize: new google.maps.Size(32, 37),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(0, 0)
            };
            marker = new google.maps.Marker({
                map: map,
                position: random || map.getCenter(),
                draggable: true,
                icon: icon
            });
            attachDragEndListener(marker);
            polygonDrawn = true;
            $("#messageText").addClass("d-none");
            $(":submit").prop('disabled', false);
        }

        // Fallback: draw a circle for city/area if polygon not set
        function drawPolygonAreaForSelectedArea(address) {
            const geocoder = new google.maps.Geocoder();

            geocoder.geocode({address: address}, function (results, status) {
                if (status === 'OK') {
                    const location = results[0].geometry.location;
                    const bounds = results[0].geometry.viewport;

                    // Center the map on the area
                    map.setCenter(location);

                    // Fit map to the bounding box
                    map.fitBounds(bounds);

                    // Get the northeast and southwest corners of the bounding box
                    const ne = bounds.getNorthEast();  // North-East corner
                    const sw = bounds.getSouthWest();  // South-West corner

                    // Draw the rectangle as a polygon
                    const polygonCoords = [
                        {lat: ne.lat(), lng: ne.lng()},
                        {lat: ne.lat(), lng: sw.lng()},
                        {lat: sw.lat(), lng: sw.lng()},
                        {lat: sw.lat(), lng: ne.lng()},
                        {lat: ne.lat(), lng: ne.lng()} // Close the polygon
                    ];

                    // Remove previous
                    bermudaTriangle.forEach(poly => poly.setMap(null));
                    bermudaTriangle = [];
                    polygonDrawn = false;

                    var rectanglePolygon = new google.maps.Polygon({
                        paths: polygonCoords,
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#FF0000",
                        fillOpacity: 0.35,
                        clickable: false,
                        zIndex: 1
                    });
                    rectanglePolygon.setMap(map);
                    bermudaTriangle.push(rectanglePolygon);
                    polygonDrawn = true;

                    // Optionally: reset marker to center
                    if (marker) marker.setMap(null);
                    var iconBase = '/themes/real-scout/images/generic-3.png';
                    var icon = {
                        url: iconBase,
                        scaledSize: new google.maps.Size(32, 37),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(0, 0)
                    };
                    marker = new google.maps.Marker({
                        map: map,
                        position: location,
                        draggable: true,
                        icon: icon
                    });
                    attachDragEndListener(marker);
                } else {
                    bermudaTriangle.forEach(poly => poly.setMap(null));
                    bermudaTriangle = [];
                    polygonDrawn = false;
                    if (currentPolygon) currentPolygon.setMap(null);
                    console.error('Geocode failed: ' + status);
                }
            });
        }


        if (agent_area_edit === "") {
            let alreadySaved = $('#city_area_id').val();
            if (alreadySaved !== 0) {
                var cityAreaValue = $('#city_area_id').find('option:selected').text();
                var cityValue = $('#city_id').find('option:selected').text();
                let address = cityAreaValue + ' ' + cityValue
                drawPolygonAreaForSelectedArea(address);
            }
            $('#city_area_id').on('change', function () {
                var cityAreaValue = $('#city_area_id').find('option:selected').text();
                var cityValue = $('#city_id').find('option:selected').text();
                let address = cityAreaValue + ' ' + cityValue
                drawPolygonAreaForSelectedArea(address);
            });
        }

    });
</script>
