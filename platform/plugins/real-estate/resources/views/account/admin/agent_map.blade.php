<style>
    #map-container {
        position: relative;
        height: 400px;
    }

    #map {
        position: relative;
        height: 100%;
        width: 100%;
    }

    .map-control {
        background-color: #f3a54a;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        margin-right: 10px;
        font-size: 20px;
        color: #ffffff;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 999;
    }

    .map-control:hover {
        background-color: #e67e22;
        box-shadow: 0 4px 10px rgba(230, 126, 34, 0.4);
    }

    /* Floating redo button style (extra specific for visibility) */
    #floating-redo {
        position: absolute;
        bottom: 15px;
        right: 60px;
        z-index: 9999;
        background-color: #3498db !important;
        display: none;
    }
</style>

@if (setting('google_map_api_key'))
    <label class="text-capitalize control-label">Mark areas for agent</label>
    <div id="map-container">
        <div id="map"></div>
        <!-- Floating Redo Button -->
{{--        <button id="floating-redo" type="button" class="map-control" title="Undo last point">⤺</button>--}}
    </div>
@endif

<script async
src="https://maps.googleapis.com/maps/api/js?key={{ setting('google_map_api_key') }}&libraries=places,geometry,drawing&v=3.64&callback=initMap">
</script>

<script>
    let map, selectedShape;
    let shapesArray = [];
    let currentPolygon = null;
    let currentPath = null;
    let drawingListener = null;

    function convertArray(array) {
        if (array.type === "Polygon" && array.coordinates) {
            return [array.coordinates[0].map(coord => ({ lat: coord[1], lng: coord[0] }))];
        } else if (array.type === "MultiPolygon" && array.coordinates) {
            return array.coordinates.map(polygon => polygon[0].map(coord => ({ lat: coord[1], lng: coord[0] })));
        } else if (Array.isArray(array)) {
            const isNestedTwice = Array.isArray(array[0][0][0]);
            return isNestedTwice
                ? array.map(polygon => polygon.map(coord => ({ lat: coord[1], lng: coord[0] })))
                : [array.map(coord => ({ lat: coord[1], lng: coord[0] }))];
        } else {
            return [];
        }
    }

    function setSelection(shape) {
        clearSelection();
        selectedShape = shape;
        selectedShape.setEditable(true);
    }

    function clearSelection() {
        if (selectedShape) {
            selectedShape.setEditable(false);
            selectedShape = null;
        }
    }

    function addCustomControls() {
        const controls = [
            { text: "+", action: () => map.setZoom(map.getZoom() + 1) },
            { text: "-", action: () => map.setZoom(map.getZoom() - 1) },
            { text: "🗑", action: removeSelectedPolygon }
        ];

        controls.forEach(ctrl => {
            const button = document.createElement("button");
            button.textContent = ctrl.text;
            button.classList.add("map-control");
            button.type = "button";
            if (ctrl.text === "🗑") {
                button.style.backgroundColor = "#e74c3c";
            }

            map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(button);
            button.addEventListener("click", ctrl.action);
        });
    }

    function removeSelectedPolygon() {
        if (selectedShape) {
            selectedShape.setMap(null);
            shapesArray = shapesArray.filter(shape => shape !== selectedShape);
            selectedShape = null;
            updateCoordArray();
        } else {
            alert("Please select a polygon to remove by clicking on it.");
        }
    }

    function updateCoordArray() {
        const coordArray = shapesArray.map(shape => {
            const path = shape.getPath();
            return Array.from({ length: path.getLength() }, (_, i) => ({
                lat: path.getAt(i).lat(),
                lng: path.getAt(i).lng(),
            }));
        });

        $('input[name="agent_area"]').val(JSON.stringify(coordArray, null, 1));
        $('input[name="agent_area_edit"]').val(JSON.stringify(coordArray, null, 1));
    }

    function drawExistingPolygons(data) {
        const polygons = convertArray(data);
        const bounds = new google.maps.LatLngBounds();

        polygons.forEach(path => {
            const polygon = new google.maps.Polygon({
                paths: path,
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: "#FF0000",
                fillOpacity: 0.35,
                editable: false,
                clickable: true
            });
            polygon.setMap(map);
            shapesArray.push(polygon);

            google.maps.event.addListener(polygon, "click", () => setSelection(polygon));
            path.forEach(coord => bounds.extend(coord));
        });

        if (!bounds.isEmpty()) {
            map.fitBounds(bounds);
        }

        updateCoordArray();
    }

    // --- Updated redo button functions ---

    function createRedoButton() {
        const redoBtn = document.getElementById("floating-redo");
        if (redoBtn) {
            redoBtn.style.display = "block";
            redoBtn.onclick = () => {
                if (currentPath && currentPath.getLength() > 0) {
                    currentPath.pop();
                }
            };
        }
    }

    function removeRedoButton() {
        const redoBtn = document.getElementById("floating-redo");
        if (redoBtn) {
            redoBtn.style.display = "none";
            redoBtn.onclick = null;
        }
    }

    function initDrawingManager() {
        const drawingManager = new google.maps.drawing.DrawingManager({
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: [google.maps.drawing.OverlayType.POLYGON],
            },
            polygonOptions: {
                fillColor: "#ffff00",
                fillOpacity: 1,
                strokeWeight: 5,
                clickable: true,
                editable: true,
                zIndex: 1,
            },
        });
        drawingManager.setMap(map);

        google.maps.event.addListener(drawingManager, "overlaycomplete", (event) => {
            if (event.type === google.maps.drawing.OverlayType.POLYGON) {
                if (drawingListener) {
                    google.maps.event.removeListener(drawingListener);
                    drawingListener = null;
                }
                // removeRedoButton();

                const shape = event.overlay;
                shape.type = event.type;
                google.maps.event.addListener(shape, "click", () => setSelection(shape));
                shapesArray.push(shape);
                setSelection(shape);
                updateCoordArray();
            }
        });

        google.maps.event.addListener(drawingManager, "drawingmode_changed", (mode) => {
            const currentMode = drawingManager.getDrawingMode();
            // createRedoButton();
            if (currentMode === google.maps.drawing.OverlayType.POLYGON) {
                if (drawingListener) {
                    google.maps.event.removeListener(drawingListener);
                    drawingListener = null;
                }

                currentPolygon = new google.maps.Polygon({
                    strokeColor: "#0000FF",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#0000FF",
                    fillOpacity: 0.35,
                    editable: true,
                    map: map,
                });

                currentPath = currentPolygon.getPath();

                drawingListener = google.maps.event.addListener(map, "click", function (e) {
                    currentPath.push(e.latLng);
                });

                google.maps.event.addListener(currentPolygon, "dblclick", function (e) {
                    google.maps.event.removeListener(drawingListener);
                    drawingListener = null;
                    // removeRedoButton();
                    shapesArray.push(currentPolygon);
                    google.maps.event.addListener(currentPolygon, "click", () => setSelection(currentPolygon));
                    setSelection(currentPolygon);
                    updateCoordArray();
                    currentPolygon = null;
                    currentPath = null;
                });
            } else {
                if (drawingListener) {
                    google.maps.event.removeListener(drawingListener);
                    drawingListener = null;
                }
                // removeRedoButton();
                if (currentPolygon) {
                    currentPolygon.setMap(null);
                    currentPolygon = null;
                    currentPath = null;
                }
            }
        });
    }

    function initMap() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const currentLocation = { lat: position.coords.latitude, lng: position.coords.longitude };
                    map = new google.maps.Map(document.getElementById("map"), {
                        center: currentLocation,
                        zoom: 8,
                    });

                    addCustomControls();
                    initDrawingManager();

                    const apCoordsVal = $('[name="agent_area_edit"]').val();
                    if (apCoordsVal) {
                        const data = JSON.parse(apCoordsVal);
                        drawExistingPolygons(data);
                    }
                },
                (error) => {
                    if (error.code === error.PERMISSION_DENIED) {
                        $('#map-container').html('<p class="center alert alert-danger">Location access is required to display the map. Please enable location services in your browser settings.</p>');
                    }
                    alert("Error: Geolocation failed.");
                }
            );
        } else {
            alert("Error: Your browser doesn't support geolocation.");
        }
    }

    window.initMap = initMap;
</script>
