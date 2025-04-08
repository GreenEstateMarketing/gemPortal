<style>
    /* Your existing styles */
</style>
<section class="sales-team">
    <div class="container">
        <div class="row">
            <ul class="vertical">
                <li data-keyword="school" style="cursor: pointer"  class="border-li nearyby active d-flex"><a > <i class="far fa-school"></i>  School</a> <span class="count-res"> </span></li>
                <li data-keyword="hospital" style="cursor: pointer"  class="border-li nearyby d-flex"><a > <i class="far fa-hospital"></i> Hospital</a> <span class="count-res"> </span></li>
                <li data-keyword="restaurant" style="cursor: pointer"  class="border-li nearyby d-flex"><a > <i class="far fa-utensils-alt"></i>Restaurant</a> <span class="count-res"> </span></li>
                <li data-keyword="park" style="cursor: pointer"  class="border-li nearyby d-flex"><a > <i class="fas fa-parking"></i> Park</a> <span class="count-res"> </span></li>
            </ul>

            <div id="map-container" style="position: relative;height:530px; width:100%;">
                <center><div><i class="fas fa-spinner fa-pulse loader-spin d-none"></i></div></center>
                <div id="map" style="position: relative;width:inherit;height:inherit"></div>
            </div>
        </div>
    </div>
</section>

<script>
    let pos;
    let map;
    let bounds;
    let infoWindow;
    let currentInfoWindow;
    let service;
    let infoPane;
    let latt;
    let long;
    var gmarkers = [];

    function initMapNeighbourhood() {
        latt = parseFloat($("#latitude").val());
        long = parseFloat($("#longitude").val());
        bounds = new google.maps.LatLngBounds(null);
        infoWindow = new google.maps.InfoWindow;
        currentInfoWindow = infoWindow;
        infoPane = document.getElementById('panel');

        pos = { lat: latt, lng: long };
        console.log(pos);
        map = new google.maps.Map(document.getElementById('map'), {
            center: pos,
            zoom: 8,
            minZoom: 8
        });
        bounds.extend(pos);

        infoWindow.setPosition(pos);

        property_name = $(".property_name").text();

        var iconBase = '/themes/real-scout/images/generic.png';
        var icon = {
            url: iconBase,
            scaledSize: new google.maps.Size(32, 37),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(0, 0)
        };
        let marker = new google.maps.Marker({
            position: pos,
            map: map,
            title: property_name,
            icon: icon
        });

        getNearbyPlaces(pos);
    }

    function handleLocationError(browserHasGeolocation, infoWindow) {
        pos = { lat: 33.856, lng: 73.215 };
        map = new google.maps.Map(document.getElementById('map'), {
            center: pos,
            zoom: 15
        });

        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
            'Geolocation permissions denied. Using default location.' :
            'Error: Your browser doesn\'t support geolocation.');
        infoWindow.open(map);
        currentInfoWindow = infoWindow;

        getNearbyPlaces(pos);
    }

    async function getNearbyPlaces(position, keyword = 'school') {
        $(".loader-spin").removeClass("d-none");

        const request = {
            location: position,
            radius: 300,
            keyword: keyword,
        };

        const service = new google.maps.places.PlacesService(map);
        service.nearbySearch(request, (results, status) => {
            nearbyCallback(results, status);
        });
    }

    function nearbyCallback(results, status) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
            const count = results.length;
            $('.vertical li.active').find('.count-res').text('(' + count + ')');
            const keyword = $('.vertical li.active').attr('data-keyword');
            createMarkers(results, keyword);
        } else {
            $(".loader-spin").addClass("d-none");
            if (gmarkers.length) {
                for (i = 0; i < gmarkers.length; i++) {
                    gmarkers[i].setMap(null);
                }
            }
        }
    }

    function createMarkers(places, keyword) {
        if (gmarkers.length) {
            for (i = 0; i < gmarkers.length; i++) {
                gmarkers[i].setMap(null);
            }
        }

        const iconBase = '/themes/real-scout/images/' + keyword + '.png';
        const icon = {
            url: iconBase,
            scaledSize: new google.maps.Size(32, 37),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(0, 0)
        };

        places.forEach(place => {
            let marker = new google.maps.Marker({
                position: place.geometry.location,
                map: map,
                title: place.name,
                icon: icon
            });

            const distanceInMeters = google.maps.geometry.spherical.computeDistanceBetween(
                new google.maps.LatLng({ lat: latt, lng: long }),
                marker.getPosition()
            );

            gmarkers.push(marker);

            google.maps.event.addListener(marker, 'click', async () => {
                const placeFetcher = new google.maps.places.PlacesService(map);
                placeFetcher.getDetails({ placeId: place.place_id }, (placeResult, status) => {
                    if (status == google.maps.places.PlacesServiceStatus.OK) {
                        showDetails(placeResult, marker, status, distanceInMeters);
                    }
                });
            });

            bounds.extend(place.geometry.location);
        });

        setTimeout(function () {
            $(".loader-spin").addClass("d-none");
            map.fitBounds(bounds);
        }, 1000);
    }

    function showDetails(placeResult, marker, status, distanceInMeters) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
            let rating = "None";
            let img = "";

            if (placeResult.rating) rating = placeResult.rating;
            if (placeResult.photos && placeResult.photos.length > 0) {
                const src = placeResult.photos[0].getUrl();
                img = '<div class="thumb img-fluid img-size pt-2"><img src="' + src + '" style="width: 240px;"></div>';
            }

            const contentString = '<div class="infowindow-neighbour-wrap">' +
                img +
                '<div class="title-info pt-2"><b>' + placeResult.name + '</b></div>' +
                '<div class="location-info pt-2"><b>Rating: ' + rating + '</b></div>' +
                '<div class="price-info pt-2"><b>' + placeResult.formatted_address + '</b></div>' +
                '<div class="price-info pt-2"><b>Distance From Property: ' + distanceInMeters.toFixed(2) + ' m</b></div>' +
                '</div>';

            let placeInfowindow = new google.maps.InfoWindow();
            placeInfowindow.setContent(contentString);
            placeInfowindow.open(marker.map, marker);
            currentInfoWindow.close();
            currentInfoWindow = placeInfowindow;
        } else {
            console.log('showDetails failed: ' + status);
        }
    }

    $(document).ready(function () {
        $(".nearyby").click(function () {
            var keyword = $(this).attr("data-keyword");
            getNearbyPlaces(pos, keyword);
            map.fitBounds(bounds);
        });

        $(".vertical li").click(function () {
            $(".vertical li").removeClass("active");
            $(this).addClass('active');
        });

        $(".showGrid").click(function () {
            if ($(this).attr('data-tab-id') == 2) {
                $(".mapouter").addClass('d-none');
                var keyword = 'school';
                getNearbyPlaces(pos, keyword);
                map.fitBounds(bounds);
            } else {
                $(".mapouter").removeClass('d-none');
            }
        });
    });
</script>

<script async
    src="https://maps.googleapis.com/maps/api/js?key={{ setting('google_map_api_key') }}&loading=async&libraries=drawing&callback=initMapNeighbourhood">
</script>
