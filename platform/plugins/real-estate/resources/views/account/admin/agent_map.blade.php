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
@endif

<script async
    src="https://maps.googleapis.com/maps/api/js?key={{ setting('google_map_api_key') }}&loading=async&libraries=drawing&callback=initMap">
</script>

<script>
    let coordArray = []
    var map;
    var selectedShape;
    let shapesArray = []

    function convertArray(array) {
        // Check the nesting level of the array
        const isNestedTwice = Array.isArray(array[0][0][0]);

        if (isNestedTwice) {
            console.log('one')
            let retArr = array.map(outerArray =>
                outerArray.map(innerArray =>
                    innerArray.map(coord => ({
                        lat: coord[1],
                        lng: coord[0]
                    }))
                )
            );
            return retArr[0]
        } else {
            return array.map(innerArray =>
                innerArray.map(coord => ({
                    lat: coord[1],
                    lng: coord[0]
                }))
            );
        }
    }

    $(document).ready(function() {
        let apCoordsVal = $('[name="agent_area_edit"]').val()
        if (apCoordsVal) {
            let apCoords = JSON.parse($('[name="agent_area_edit"]').val());

            convertedArray = convertArray(apCoords.coordinates)

            console.log(convertedArray)

            if (convertedArray.length > 0) {
                coordArray = convertedArray
            }
        }
    })

    function clearSelection() {
        if (selectedShape) {
            selectedShape.setEditable(false);
            selectedShape = null;
        }
    }

    function setSelection(shape) {
        clearSelection();
        selectedShape = shape;
    }

    function initMap() {
        // Get the user's current location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };

                    map = new google.maps.Map(document.getElementById("map"), {
                        center: currentLocation,
                        zoom: 8,
                    });

                    const drawingManager = new google.maps.drawing.DrawingManager({
                        drawingMode: google.maps.drawing.OverlayType.MARKER,
                        drawingControl: true,
                        drawingControlOptions: {
                            position: google.maps.ControlPosition.TOP_CENTER,
                            drawingModes: [
                                google.maps.drawing.OverlayType.POLYGON,
                            ],
                        },
                        markerOptions: {
                            icon: "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
                        },
                        circleOptions: {
                            fillColor: "#ffff00",
                            fillOpacity: 1,
                            strokeWeight: 5,
                            clickable: false,
                            editable: true,
                            zIndex: 1,
                        },
                        polygonOptions: {
                            fillColor: "#ffff00",
                            fillOpacity: 1,
                            strokeWeight: 5,
                            clickable: false,
                            editable: true,
                            zIndex: 1,
                        },
                    });

                    drawingManager.setMap(map);

                    // Event listener for capturing shape data
                    google.maps.event.addListener(drawingManager, "overlaycomplete", (event) => {
                        let shapeData;
                        if (event.type === google.maps.drawing.OverlayType.CIRCLE) {
                            const radius = event.overlay.getRadius();
                            const center = event.overlay.getCenter();
                            shapeData = {
                                type: "circle",
                                center: {
                                    lat: center.lat(),
                                    lng: center.lng()
                                },
                                radius: radius,
                            };
                        } else if (event.type === google.maps.drawing.OverlayType.POLYGON) {
                            const path = event.overlay.getPath();
                            const coordinates = [];
                            for (let i = 0; i < path.getLength(); i++) {
                                const latLng = path.getAt(i);
                                coordinates.push({
                                    lat: latLng.lat(),
                                    lng: latLng.lng()
                                });
                            }
                            shapeData = {
                                type: "polygon",
                                coordinates: coordinates,
                            };
                        }

                        const shape = event.overlay;
                        shape.type = event.type;
                        google.maps.event.addListener(shape, "click", () => {
                            setSelection(shape);
                        });
                        setSelection(shape);
                        shapesArray.push(shape)

                        const shapeBlob = new Blob([JSON.stringify(shapeData)], {
                            type: "application/json"
                        });

                        if (shapeData) {
                            let coords = shapeData.coordinates;
                            coordArray.push(coords);
                            let inJson = JSON.stringify(coordArray, null, 1)
                            console.log(inJson);
                            $('input[name="agent_area"]').val(inJson);
                        }
                    });
                },
                () => {
                    handleLocationError(true, map.getCenter());
                }
            );
        } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, map.getCenter());
        }
    }

    function handleLocationError(browserHasGeolocation, pos) {
        alert(
            browserHasGeolocation ?
            "Error: The Geolocation service failed." :
            "Error: Your browser doesn't support geolocation."
        );
    }

    window.initMap = initMap;

    $(document).ready(function() {
        let global_arr = []
        let counter = 0
        let bermudaTriangle = []
        let count_shapes = 0

        function drawPolygonArea() {
            var agent_area_edit = $('[name="agent_area_edit"]').val();
            var latlngbounds = new google.maps.LatLngBounds();
            if (agent_area_edit != "") {
                var dataObj = JSON.parse(agent_area_edit);
                var arrlen = dataObj.length;
                var list_data = [];
                var one = 0;
                var shapes = 0;
                var objAr = dataObj.coordinates;
                var type = dataObj.type;

                $.each(dataObj.coordinates[0], function(index, data) {

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

                        $.each(data, function(key, data1) {
                            var latlng = new google.maps.LatLng(data1[1], data1[0]);
                            latlngbounds.extend(latlng);
                            list_data[many] = {

                                lat: data1[1],
                                lng: data1[0],

                            };
                            many++;
                            //

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

        drawPolygonArea();

        function deleteSelectedShape() {
            let lastShape = shapesArray.pop()
            lastShape.setMap(null)

            if (counter > -1) {
                global_arr.splice(counter - 1, 1);
                coordArray.splice(counter - 1, 1);
                $("input[name='agent_area']").val(JSON.stringify(global_arr, null, 1));
            } else {
                $("input[name='agent_area']").val("");
            }

            counter--;
        }

        $("#remove-line").click(function() {
            var agent_area_edit = $("input[name='agent_area_edit']").val();

            if (agent_area_edit != "") {
                $.each(bermudaTriangle, function(key, value) {
                    value.setMap(null);
                });
                coordArray = []
                if (shapesArray.length > 0) {
                    shapesArray.forEach(shape => shape.setMap(null))
                }

                $("input[name='agent_area_edit']").val('');
            } else {
                deleteSelectedShape();
            }

        });
    })
</script>
