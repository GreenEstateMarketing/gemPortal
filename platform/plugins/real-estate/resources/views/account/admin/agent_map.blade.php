<style>
    #map-container {
        position: relative;
        height: 400px;

    }

    #map {
        position: relative;
        height: inherit;

        width: inherit;
    }

</style>
@if (setting('google_map_api_key'))

    <label class="text-capitalize control-label">mark areas for agent</label>
    <div id="floating-panel">
        <input id="remove-line" type="button" value="Remove" />

    </div>
    <div id="map-container">
        <div id="map"></div>
    </div>


    <!-- <div id="infowindow-content">
         <img src="" width="16" height="16" id="place-icon">
         <span id="place-name" class="title"></span><br>
         <span id="place-address"></span>
     </div>-->
    <br>
    <br>

@endif
<script type="text/javascript">
    var global_arr = [];
    var counter = 0;
    var di = 0;
    var map;
    var bermudaTriangle = [];
    var count_shapes = 0;
    var infowindow;
    var random;


    // globals
    var drawingManager;
    var selectedShape;

    function clearSelection() {
        if (selectedShape) {
            selectedShape.setEditable(false);
            selectedShape = null;
        }
    }

    function setSelection(shape) {
        clearSelection();
        selectedShape = shape;
        // shape.setEditable(true);
        // selectColor(shape.get('fillColor') || shape.get('strokeColor'));
    }

    function deleteSelectedShape() {
        if (selectedShape) {
            selectedShape.setMap(null);


            if (counter > -1) {

                global_arr.splice(counter - 1, 1);
                $("input[name='agent_area']").val(JSON.stringify(global_arr, null, 1));
            } else {
                $("input[name='agent_area']").val("");
            }


            counter--;
        }
    }

    function initMaps() {
        navigator.geolocation.getCurrentPosition(function(position) {

            let coords = position.coords;
            lat = coords.latitude;
            lng = coords.longitude;
            initMap(lat, lng);
        });
    }

    function initMap(lat, lng) {

        map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: lat,
                lng: lng
            },
            zoom: 8
        });

        drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: google.maps.drawing.OverlayType.polygon,
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: [ /*'marker', 'circle', 'polyline',*/ 'polygon' /*, 'rectangle'*/ ]
            },
            markerOptions: {
                icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'
            },
            circleOptions: {
                fillColor: '#ffff00',
                fillOpacity: 1,
                strokeWeight: 5,
                clickable: true,
                editable: true,
                zIndex: 1
            }
        });
        new google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
        new google.maps.event.addListener(map, 'click', clearSelection);
        /*// Configure the click listener.
        map.addListener("click", (mapsMouseEvent) => {
            // Close the current InfoWindow.
            infoWindow.close();

            // Create a new InfoWindow.
            infoWindow = new google.maps.InfoWindow({
                position: mapsMouseEvent.latLng,
            });
            infoWindow.setContent(
                JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2)
            );
            infoWindow.open(map);
        });*/
        google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
            // var path = polygon.getPath();
            /*
                var encodeString =
                    google.maps.geometry.encoding.encodePath(path);
                    console.log(encodeString);*/
            drawingManager.setDrawingMode(null);

            // Add an event listener that selects the newly-drawn shape when the user
            // mouses down on it.
            var newShape = polygon;
            //  newShape.type = e.type;
            google.maps.event.addListener(newShape, 'click', function() {
                setSelection(newShape);
            });
            setSelection(newShape);
            if (di == 4) {
                drawingManager.setOptions({
                    drawingControl: false,
                    drawingMode: null
                });
            }
            di++;
            const coords = polygon.getPath().getArray().map(coord => {
                return {
                    lat: coord.lat(),
                    lng: coord.lng()
                }
            });
            //console.log(polygon.getGeometry());
            var area = google.maps.geometry.spherical.computeArea(polygon.getPath());
            console.log(coords);
            //console.log(JSON.stringify(coords, null, 1));
            var data_string = JSON.stringify(coords, null, 1);
            // alert(counter);
            global_arr[counter] = coords;
            console.log(global_arr);
            $("input[name='agent_area']").val(JSON.stringify(global_arr, null, 1));
            counter++;
            var currentInfoWindow = null;
            //ajax to check agent_area there
            $.ajax({
                type: 'get',
                url: "/admin/real-estate/accounts/getAgentAreaList",
                /* processData: false,
                 contentType: false,*/
                dataType: 'json',
                data: {
                    agent_area: $("input[name='agent_area']").val()
                },
                async: false,
                success: function(dataa) {
                    if (dataa != "") {
                        $.each(dataa, function(index, dataaa) {


                            var dataObj = JSON.parse(dataaa.agent_area);
                            var arrlen = dataObj.length;
                            console.log(dataObj.coordinates[0]);
                            var list_data = [];
                            var one = 0;
                            var shapes = 0;
                            var objAr = dataObj.coordinates;
                            var type = dataObj.type;

                            $.each(dataObj.coordinates[0], function(index, data) {

                                if (type == "Polygon") {
                                    var latlng = new google.maps.LatLng(data[0],
                                        data[1]);
                                    random = latlng;
                                    latlngbounds.extend(latlng);
                                    list_data[one] = {

                                        lat: data[0],
                                        lng: data[1],

                                    };
                                    one++;
                                } else {
                                    var many = 0;


                                    $.each(data, function(key, data1) {
                                        console.log("text2");
                                        console.log(data1);

                                        var latlng = new google.maps.LatLng(
                                            data1[0], data1[1]);
                                        random = latlng;
                                        latlngbounds.extend(latlng);
                                        list_data[many] = {

                                            lat: data1[0],
                                            lng: data1[1],

                                        };
                                        many++;
                                        //

                                    });
                                    console.log(list_data);
                                    global_arr[counter] = list_data;

                                }


                            });

                            var iconBase = '/themes/real-scout/images/generic.png';

                            var icon = {
                                url: iconBase, // url
                                scaledSize: new google.maps.Size(32, 37), // scaled size
                                origin: new google.maps.Point(0, 0), // origin
                                anchor: new google.maps.Point(0, 0) // anchor
                            };
                            var marker = new google.maps.Marker({
                                map: map,
                                position: random,
                                draggable: false,
                                title: 'Agent: ' + dataaa.first_name + ' ' + dataaa
                                    .last_name,
                                icon: icon
                            });
                            var contentString =
                                '<div class="infowindow-wrap"><div class="thumb img-fluid img-size text-center pt-2"><a href="#"><img src="' +
                                dataaa.avatar_url.encoded +
                                '"   style="width:100px;"></a></div><div class="title-info pt-2"><b>Name</b>: ' +
                                dataaa.first_name + ' ' + dataaa.last_name +
                                '</div><div class="location-info pt-2"><b>Email: </b>' +
                                dataaa.email +
                                '</div><div class="price-info pt-2"><b>Phone: </b>' + dataaa
                                .phone + '</div></div>';

                            marker['infowindow'] = new google.maps.InfoWindow({
                                content: contentString
                            });
                            /*var infowindow = new google.maps.InfoWindow({
                                content:contentString
                            });*/

                            // Attach it to the marker we've just added
                            google.maps.event.addListener(marker, 'click', function() {
                                if (currentInfoWindow != null) {
                                    currentInfoWindow.close();
                                }
                                this['infowindow'].open(map, this);
                                currentInfoWindow = this['infowindow'];
                                infowindow.open(map, marker);
                            });

                        });
                    }
                }
            });

        });

        drawingManager.setMap(map);
        var agent_area_edit = $("input[name='agent_area_edit']").val();
        var latlngbounds = new google.maps.LatLngBounds();
        if (agent_area_edit != "") {
            dataObj = JSON.parse(agent_area_edit);
            var arrlen = dataObj.length;
            console.log(dataObj.coordinates[0]);
            var list_data = [];
            var one = 0;
            var shapes = 0;
            var objAr = dataObj.coordinates;
            var type = dataObj.type;

            $.each(dataObj.coordinates[0], function(index, data) {

                if (type == "Polygon") {
                    var latlng = new google.maps.LatLng(data[1], data[0]);
                    latlngbounds.extend(latlng);
                    list_data[one] = {

                        lat: data[1],
                        lng: data[0],

                    };
                    one++;
                } else {
                    var many = 0;


                    $.each(data, function(key, data1) {
                        console.log("text2");
                        console.log(data1);

                        var latlng = new google.maps.LatLng(data1[1], data1[0]);
                        latlngbounds.extend(latlng);
                        list_data[many] = {

                            lat: data1[1],
                            lng: data1[0],

                        };
                        many++;
                        //

                    });
                    console.log(list_data);
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
                    count_shapes++;
                    shapes++;
                }

                shapes++;
            });

            /* var data = {
                 "type": "FeatureCollection",
                 "features": [


                     {
                         "type": "Feature",
                         "geometry": {
                             "type": "Polygon",
                             "coordinates": [[[33.707474357929, 73.00773860012], [33.70633193462, 73.088762770042], [33.667480502458, 73.085329542503], [33.667480502458, 73.006365309104], [33.707474357929, 73.00773860012]]]
                         },
                         /!*   "properties": {
                             "prop0": "value0",
                             "prop1": {"this": "that"}
                         }*!/
                     }
                 ]
             };*/

            /* const triangleCoords = [
                 {lat: 33.730830754238, lng: 71.960238315625},
                 {lat: 33.217646119918, lng: 72.048128940625},
                 {lat: 33.222241556302, lng: 72.4765957375},
                 {lat: 33.730830754238, lng: 71.960238315625},
             ];*/
            // Construct the polygon.
            if (type == "Polygon") {
                //    $("input[name='agent_area']").val(JSON.stringify(list_data, null, 1));
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
            //console.log(data);
        }

    }
    $("#remove-line").click(function() {

        var agent_area_edit = $("input[name='agent_area_edit']").val();

        if (agent_area_edit != "") {


            $.each(bermudaTriangle, function(key, value) {
                value.setMap(null);
            });
            //  bermudaTriangle.setMap(null);
            $("input[name='agent_area_edit']").val('');
        } else {
            deleteSelectedShape();
        }

    });
</script>
<script
src="https://maps.googleapis.com/maps/api/js?key={{ setting('google_map_api_key') }}&libraries=places&libraries=geometry,drawing&callback=initMaps">
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
    navigator.geolocation.getCurrentPosition(function(position) {

        let coords = position.coords;
        lat = coords.latitude;
        lng = coords.longitude;

    });

    $(document).ready(function() {

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

            marker = new google.maps.Marker({
                map: map,
                position: myLatlng,
                draggable: true
            });

            geocoder.geocode({
                'latLng': myLatlng
            }, function(results, status) {

                $('#latitude,#longitude').show();
                $('#location').val(results[0].formatted_address);
                $('#latitude').val(marker.getPosition().lat());
                $('#longitude').val(marker.getPosition().lng());
                infowindow.setContent(results[0].formatted_address);
                infowindow.open(map, marker);


            });

            const input = document.getElementById("location");
            const searchBox = new google.maps.places.SearchBox(input);
            console.log(searchBox.getPlaces());
            // Bias the SearchBox results towards current map's viewport.
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
                console.log(searchBox.getPlaces());
            });
            let markers = [];
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }
                // Clear out the old markers.
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                // For each place, get the icon, name and location.
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    const icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25),
                    };
                    // Create a marker for each place.
                    /*markers.push(
                        new google.maps.Marker({
                            map,
                            icon,
                            title: place.name,
                            position: place.geometry.location,
                        })
                    );*/

                    marker.setPosition(place.geometry.location);
                    var inputString = place.geometry.location;
                    infowindow.setContent($('#location').val());
                    infowindow.open(map, marker);
                    $('#latitude').val(place.geometry.location.lat());
                    $('#longitude').val(place.geometry.location.lng());

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
            });

            google.maps.event.addListener(marker, 'dragend', function() {

                geocoder.geocode({
                    'latLng': marker.getPosition()
                }, function(results, status) {

                    $('#location').val(results[0].formatted_address);
                    $('#latitude').val(marker.getPosition().lat());
                    $('#longitude').val(marker.getPosition().lng());
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(map, marker);


                });
            });
            // click on map and set you marker to that position
            google.maps.event.addListener(map, 'click', function(event) {
                marker.setPosition(event.latLng);
                $('#latitude').val(marker.getPosition().lat());
                $('#longitude').val(marker.getPosition().lng());
            });
        }
        initMap();
    });
</script>
