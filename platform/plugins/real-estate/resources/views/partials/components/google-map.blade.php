<script src="https://maps.googleapis.com/maps/api/js?key={{ setting('google_map_api_key') }}&libraries=places,geometry">
</script>

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

            geocoder.geocode({
                'latLng': myLatlng
            }, function (results, status) {
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
                if (places.length == 0) {
                    return;
                }
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    marker.setPosition(place.geometry.location);
                    attachDragEndListener(marker);

                    var agent_area_edit = $("#agent_area").val();
                    if (agent_area_edit != "") {
                        var isWithInPolygon = google.maps.geometry.poly.containsLocation(
                            place.geometry.location,
                            bermudaTriangle[count_shapes]
                        );
                        if (isWithInPolygon) {
                            $(":submit").prop('disabled', false);
                            $("#messageText").addClass("d-none");
                            geocoder.geocode({
                                'latLng': marker.getPosition()
                            }, function (results, status) {
                                if (status === google.maps.GeocoderStatus.OK && results[
                                    0]) {
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
                        bounds.extend(place.geometry.location);
                    }
                });
            });

            google.maps.event.addListener(map, 'click', function (event) {
                marker.setPosition(event.latLng);
                attachDragEndListener(marker);
                var agent_area_edit = $("#agent_area").val();
                if (agent_area_edit != "") {
                    var isWithInPolygon = google.maps.geometry.poly.containsLocation(
                        event.latLng,
                        bermudaTriangle[count_shapes]
                    );
                    if (isWithInPolygon) {
                        $(":submit").prop('disabled', false);
                        $("#messageText").addClass("d-none");
                    } else {
                        handleMarkerOutsidePolygon();
                    }
                }
                geocoder.geocode({
                    'latLng': marker.getPosition()
                }, function (results, status) {
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

        function attachDragEndListener(marker) {
            google.maps.event.addListener(marker, 'dragend', function (event) {
                var coordinate = marker.getPosition();
                var agent_area_edit = $("#agent_area").val();
                if (agent_area_edit != "") {
                    var isWithInPolygon = false;
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
                geocoder.geocode({
                    'latLng': marker.getPosition()
                }, function (results, status) {
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
            var iconBase = '/themes/real-scout/images/generic-3.png';
            var icon = {
                url: iconBase,
                scaledSize: new google.maps.Size(32, 37),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(0, 0)
            };
            marker.setMap(null);
            $(":submit").prop('disabled', true);
            $("#messageText").removeClass("d-none");
            $("#messageText").text("Please Select Location Inside the polygon");
            marker = new google.maps.Marker({
                map: map,
                position: random,
                draggable: true,
                icon: icon
            });
            attachDragEndListener(marker);
        }

        initMap();

        function drawPolygonArea() {
            var agent_area_edit = $("#agent_area").val();
            var latlngbounds = new google.maps.LatLngBounds();
            if (agent_area_edit != "") {
                var dataObj = JSON.parse(agent_area_edit);
                var list_data = [];
                var one = 0;
                var shapes = 0;
                var objAr = dataObj.coordinates;
                var type = dataObj.type;

                $.each(dataObj.coordinates[0], function (index, data) {
                    if (type == "Polygon") {
                        var latlng = new google.maps.LatLng(data[1], data[0]);
                        random = latlng;
                        latlngbounds.extend(latlng);
                        list_data[one] = {
                            lat: data[1],
                            lng: data[0],
                        };
                        one++;
                    } else {
                        var many = 0;
                        $.each(data, function (key, data1) {
                            var latlng = new google.maps.LatLng(data1[1], data1[0]);
                            latlngbounds.extend(latlng);
                            list_data[many] = {
                                lat: data1[1],
                                lng: data1[0],
                            };
                            many++;
                        });
                        global_arr[counter] = list_data;
                        counter++;
                        bermudaTriangle[count_shapes] = new google.maps.Polygon({
                            paths: list_data,
                            strokeColor: "#FF0000",
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: "#FF0000",
                            fillOpacity: 0.35,
                            clickable: true
                        });
                        bermudaTriangle[count_shapes].setMap(map);
                        count_shapes++;
                        shapes++;
                    }
                    shapes++;
                });

                if (type == "Polygon") {
                    global_arr[counter] = list_data;
                    counter++;
                    bermudaTriangle[count_shapes] = new google.maps.Polygon({
                        paths: list_data,
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#FF0000",
                        fillOpacity: 0.35,
                    });
                    bermudaTriangle[count_shapes].setMap(map);
                }

                map.fitBounds(latlngbounds);
            }
        }

        var agent_area_edit = $("#agent_area").val();
        if (agent_area_edit != "") {
            drawPolygonArea();
            marker.setMap(null);
            var iconBase = '/themes/real-scout/images/generic-3.png';
            var icon = {
                url: iconBase,
                scaledSize: new google.maps.Size(32, 37),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(0, 0)
            };
            marker = new google.maps.Marker({
                map: map,
                position: random,
                draggable: true,
                icon: icon
            });
            attachDragEndListener(marker);
        }

        function drawPolygonAreaForSelectedArea(address) {
            const geocoder = new google.maps.Geocoder();

            geocoder.geocode({ address: address }, function (results, status) {
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

                    // Draw the polygon using these bounds
                    const polygonCoords = [
                        { lat: ne.lat(), lng: ne.lng() },
                        { lat: ne.lat(), lng: sw.lng() },
                        { lat: sw.lat(), lng: sw.lng() },
                        { lat: sw.lat(), lng: ne.lng() }
                    ];

                    const radius = google.maps.geometry.spherical.computeDistanceBetween(ne, sw) / 3; // Radius in meters

                    if (currentPolygon) {
                        currentPolygon.setMap(null);
                    }

                    currentPolygon = new google.maps.Circle({
                        center: location,
                        radius: radius,  // Radius in meters
                        strokeColor: '#f57070',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#f57070',
                        fillOpacity: 0.35
                    });

                    currentPolygon.setMap(map);
                } else {
                    currentPolygon.setMap(null);
                    console.error('Geocode failed: ' + status);
                }
            });
        }

        if (agent_area_edit === "") {
            let alreadySaved = $('#city_area_id').val();
            
            if (alreadySaved !== 0) {
                var cityAreaValue = $('#city_area_id').find('option:selected').text();;
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