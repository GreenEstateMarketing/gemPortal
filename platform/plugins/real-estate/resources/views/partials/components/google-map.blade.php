<style>
    #map-container {
        position: relative;
        height: 230px;

    }

    #map {
        position: relative;
        height: inherit;

        width: inherit;
    }

</style>
<script type="text/javascript">

    "use strict";
    var map;
    var marker;
    var lat;
    var lng;
    var myLatlng;
    var geocoder;
    var infowindow;
    var global_arr=[];var counter=0;var di=0;var map;var bermudaTriangle=[];var count_shapes=0;var random;

    navigator.geolocation.getCurrentPosition(function (position) {
        let coords = position.coords;
        lat = coords.latitude;
        lng = coords.longitude;

    });

    $(document).ready(function () {

        function setpVal(pos) {
            let coords = pos.coords;
            $("#timestamp").text(new Date(pos.timestamp));
            lat=coords.latitude;
            lng=coords.longitude;

        }
        ///ajax to draw
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
            var iconBase = '/themes/real-scout/images/generic.png';

            var icon = {
                url: iconBase, // url
                scaledSize: new google.maps.Size(32,37), // scaled size
                origin: new google.maps.Point(0,0), // origin
                anchor: new google.maps.Point(0, 0) // anchor
            };;

            marker = new google.maps.Marker({
                map: map,
                position: myLatlng,
                draggable: true,
                icon:icon
            });

            geocoder.geocode({'latLng': myLatlng}, function (results, status) {

                $('#latitude,#longitude').show();
                $('#location').val(results[0].formatted_address);
                $('#latitude').val(marker.getPosition().lat());
                $('#longitude').val(marker.getPosition().lng());
                infowindow.setContent(results[0].formatted_address);
                infowindow.open(map, marker);

                $('#latitude').trigger('change');
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
                var iconBase = '/themes/real-scout/images/generic.png';

                var icon = {
                    url: iconBase, // url
                    scaledSize: new google.maps.Size(32,37), // scaled size
                    origin: new google.maps.Point(0,0), // origin
                    anchor: new google.maps.Point(0, 0) // anchor
                };
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }

                    marker.setPosition(place.geometry.location);
                    var agent_area_edit= $("#agent_area").val();

                    if(agent_area_edit!="") {
                        var isWithInPolygon = google.maps.geometry.poly.containsLocation(
                            place.geometry.location,
                            bermudaTriangle[count_shapes]
                        );
                        if (isWithInPolygon) {

                            $(":submit").prop('disabled', false);
                            $("#messageText").addClass("d-none");
                            geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {

                                $('#location').val(results[0].formatted_address);
                                $('#latitude').val(marker.getPosition().lat());
                                $('#longitude').val(marker.getPosition().lng());
                                infowindow.setContent(results[0].formatted_address);
                                infowindow.open(map, marker);
                                $('#latitude').trigger('change');

                            });

                        } else {
                            marker.setMap(null);
                            //  $(":submit").prop('disabled',true);
                            $("#messageText").removeClass("d-none");
                            $("#messageText").text("Please Select Location Inside the polygon");
                            marker = new google.maps.Marker({
                                map: map,
                                position: random,
                                draggable: true,
                                icon: icon
                            });

                        }
                    }

                    geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {

                        $('#location').val(results[0].formatted_address);
                        $('#latitude').val(marker.getPosition().lat());
                        $('#longitude').val(marker.getPosition().lng());
                        infowindow.setContent(results[0].formatted_address);
                        infowindow.open(map, marker);
                        $('#latitude').trigger('change');

                    });
                   // alert("location chnagef");
                 //   var inputString=place.geometry.location;
                  //infowindow.setContent($('#location').val());
                    //infowindow.open(map, marker);
                    //$('#latitude').val(marker.lat());
                    ///$('#longitude').val(marker.lng());
                    ///$('#latitude').trigger('change');

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
            });

            google.maps.event.addListener(marker, 'dragend', function (event) {

                var coordinate = marker.getPosition();
                var agent_area_edit= $("#agent_area").val();

                if(agent_area_edit!="") {
                    // var polygon = new google.maps.Polygon([], "#000000", 1, 1, "#336699", 0.3);
                    var isWithInPolygon = google.maps.geometry.poly.containsLocation(
                        event.latLng,
                        bermudaTriangle[count_shapes]
                    );
                    if (isWithInPolygon) {
                        $(":submit").prop('disabled', false);
                        $("#messageText").addClass("d-none");
                    } else {
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
                    }
                }
                geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {

                    $('#location').val(results[0].formatted_address);
                    $('#latitude').val(marker.getPosition().lat());
                    $('#longitude').val(marker.getPosition().lng());
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(map, marker);
                    $('#latitude').trigger('change');

                });
            });
            // click on map and set you marker to that position
            google.maps.event.addListener(map, 'click', function(event) {
                console.log(event);
                marker.setPosition(event.latLng);
                var coordinate = marker.getPosition();
                var agent_area_edit= $("#agent_area").val();

                if(agent_area_edit!="") {
                    var isWithInPolygon = google.maps.geometry.poly.containsLocation(
                        event.latLng,
                        bermudaTriangle[count_shapes]
                    );
                    if (isWithInPolygon) {
                        //alert("here");
                        $(":submit").prop('disabled', false);
                        $("#messageText").addClass("d-none");
                    } else {
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
                    }
                }
               // alert(bermudaTriangle[count_shapes].getBounds().contains(marker.getPosition()));
                //alert(isWithinPolygon);
                geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {

                    $('#location').val(results[0].formatted_address);
                    $('#latitude').val(marker.getPosition().lat());
                    $('#longitude').val(marker.getPosition().lng());
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(map, marker);
                    $('#latitude').trigger('change');

                });

            });
        }
        initMap();
        //ajax to draw a polygon
        function drawPolygonArea() {
            var agent_area_edit= $("#agent_area").val();
            var latlngbounds = new google.maps.LatLngBounds();
            if(agent_area_edit!="") {
                var dataObj = JSON.parse(agent_area_edit);
                var arrlen = dataObj.length;
                console.log(dataObj.coordinates[0]);
                var list_data = [];
                var one = 0;
                var shapes=0;
                var objAr = dataObj.coordinates;
                var type=dataObj.type;

                $.each(dataObj.coordinates[0], function(index,data){

                    if (type =="Polygon") {
                        var latlng = new google.maps.LatLng(data[1], data[0]);
                        random=latlng;
                        latlngbounds.extend(latlng);
                        list_data[one] = {

                            lat: data[1],
                            lng: data[0],

                        };
                        one++;
                    } else {
                        var many = 0;


                        $.each(data, function(key,data1){
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
                        global_arr[counter]=list_data;
                        counter++;
                        bermudaTriangle[count_shapes] = new google.maps.Polygon({
                            paths: list_data,
                            strokeColor: "#FF0000",
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: "#FF0000",
                            fillOpacity: 0.35,
                            clickable:true
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
                if (type =="Polygon") {
                    //    $("input[name='agent_area']").val(JSON.stringify(list_data, null, 1));
                    global_arr[counter]=list_data;
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
        var agent_area_edit= $("#agent_area").val();

        if(agent_area_edit!="") {
            drawPolygonArea();
            marker.setMap(null);
            var iconBase = '/themes/real-scout/images/generic.png';

            var icon = {
                url: iconBase, // url
                scaledSize: new google.maps.Size(32, 37), // scaled size
                origin: new google.maps.Point(0, 0), // origin
                anchor: new google.maps.Point(0, 0) // anchor
            };
            marker = new google.maps.Marker({
                map: map,
                position: random,
                draggable: true,
                icon: icon
            });
        }
    });


</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ setting('google_map_api_key') }}&libraries=places,geometry"></script>
