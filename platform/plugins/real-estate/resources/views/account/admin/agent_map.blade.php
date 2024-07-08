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
    function initMap() {
        // Get the user's current location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };

                    const map = new google.maps.Map(document.getElementById("map"), {
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
                                center: { lat: center.lat(), lng: center.lng() },
                                radius: radius,
                            };
                        } else if (event.type === google.maps.drawing.OverlayType.POLYGON) {
                            const path = event.overlay.getPath();
                            const coordinates = [];
                            for (let i = 0; i < path.getLength(); i++) {
                                const latLng = path.getAt(i);
                                coordinates.push({ lat: latLng.lat(), lng: latLng.lng() });
                            }
                            shapeData = {
                                type: "polygon",
                                coordinates: coordinates,
                            };
                        }
                        const shapeBlob = new Blob([JSON.stringify(shapeData)], { type: "application/json" });

                        if (shapeData) {
                            let coords = shapeData.coordinates;
                            coordArray.push(coords);
                            let inJson = JSON.stringify(coordArray, null, 1)
                            console.log('JSON CORRDS: ', inJson);
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
            browserHasGeolocation
                ? "Error: The Geolocation service failed."
                : "Error: Your browser doesn't support geolocation."
        );
    }

    window.initMap = initMap;
</script>