<style>

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
                <Center> <div><i class="fas fa-spinner fa-pulse loader-spin d-none"></i></div></Center>
                <div id="map" style="position: relative;width:inherit;height:inherit"></div>
            </div>
            <!--
                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d13278.991209367314!2d73.05233364999998!3d33.6895939!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2s!4v1629950970010!5m2!1sen!2s" width="65%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            -->


        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</section>
<!--<div id="panel"></div>


<div class="d-flex mt-5">
    <div class="list pr-5">
        <ul>
            <li data-keyword="school" style="cursor: pointer" class="nearyby">School</li>
            <li data-keyword="health" style="cursor: pointer" class="nearyby">Health</li>
            <li data-keyword="hospital" style="cursor: pointer" class="nearyby">Hospital</li>
            <li data-keyword="parks" style="cursor: pointer" class="nearyby">Parks</li>

        </ul>
    </div>

</div>-->
<script>
    /* Note: This example requires that you consent to location sharing when
     * prompted by your browser. If you see the error "Geolocation permission
     * denied.", it means you probably did not give permission for the browser * to locate you. */
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
        latt=parseFloat($("#latitude").val());
        long=parseFloat($("#longitude").val());
        // Initialize variables
        bounds = new google.maps.LatLngBounds(null);
        infoWindow = new google.maps.InfoWindow;
        currentInfoWindow = infoWindow;
        /* TODO: Step 4A3: Add a generic sidebar */
        infoPane = document.getElementById('panel');

        // Try HTML5 geolocation


        pos = {
            lat:latt,
            lng:long
        };
        console.log(pos);
        map = new google.maps.Map(document.getElementById('map'), {
            center: pos,
            zoom: 8,
           minZoom: 8
        });
        bounds.extend(pos);

        infoWindow.setPosition(pos);

        property_name= $(".property_name").text();

        var iconBase = '/themes/real-scout/images/generic.png';

        var icon = {
            url: iconBase, // url
            scaledSize: new google.maps.Size(32,37), // scaled size
            origin: new google.maps.Point(0,0), // origin
            anchor: new google.maps.Point(0, 0) // anchor
        };;
        let marker = new google.maps.Marker({
            position: pos,
            map: map,
            title:property_name,
            icon: icon
        });
        //infoWindow.setContent(property_name);
        // infoWindow.open(map);
        //  map.setCenter(pos);

        // Call Places Nearby Search on user's location
        getNearbyPlaces(pos);


    }

    // Handle a geolocation error
    function handleLocationError(browserHasGeolocation, infoWindow) {
        // Set default location to Sydney, Australia
        pos = { lat: 33.856, lng: 73.215 };
        map = new google.maps.Map(document.getElementById('map'), {
            center: pos,
            zoom: 15
        });

        // Display an InfoWindow at the map center
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
            'Geolocation permissions denied. Using default location.' :
            'Error: Your browser doesn\'t support geolocation.');
        infoWindow.open(map);
        currentInfoWindow = infoWindow;

        // Call Places Nearby Search on the default location
        getNearbyPlaces(pos);
    }

    // Perform a Places Nearby Search Request
    function getNearbyPlaces(position,keyword='school') {

        $(".loader-spin").removeClass("d-none");
        let request = {
            location: position,
            radius: 300,
            keyword: [keyword]
        };

        service = new google.maps.places.PlacesService(map);
        service.nearbySearch(request, nearbyCallback);
    }

    // Handle the results (up to 20) of the Nearby Search
    function nearbyCallback(results, status) {

        if (status == google.maps.places.PlacesServiceStatus.OK) {
            count= results.length;
            $('.vertical li.active').find('.count-res').text('('+count+')');
            keyword=( $('.vertical li.active').attr('data-keyword'));
            // alert($('.vertical li.active').find('.count-res').text(count));
            createMarkers(results,keyword);
        }
        else
        {

            $(".loader-spin").addClass("d-none");
            if(gmarkers.length) {
                for (i = 0; i < gmarkers.length; i++) {
                    gmarkers[i].setMap(null);
                }
            }
        }
    }

    // Set markers at the location of each place result
    function createMarkers(places,keyword) {
        if(gmarkers.length) {
            for (i = 0; i < gmarkers.length; i++) {
                gmarkers[i].setMap(null);
            }
        }
        var iconBase = '/themes/real-scout/images/'+keyword+'.png';

        var icon = {
            url: iconBase, // url
            scaledSize: new google.maps.Size(32,37), // scaled size
            origin: new google.maps.Point(0,0), // origin
            anchor: new google.maps.Point(0, 0) // anchor
        };
        //    alert(iconBase);

        places.forEach(place => {

            let marker = new google.maps.Marker({
                position: place.geometry.location,
                map: map,
                title: place.name,
                icon:icon
            });
            var distanceInMeters = google.maps.geometry.spherical.computeDistanceBetween(
                new google.maps.LatLng({
                    lat: latt,
                    lng: long
                }),
                marker.getPosition()
            );
            //console.log(distanceInMeters);
          //  alert(distanceInMeters);
            gmarkers.push(marker);
            /* TODO: Step 4B: Add click listeners to the markers */
            // Add click listener to each marker
            google.maps.event.addListener(marker, 'click', () => {
                let request = {
                    placeId: place.place_id,
                    fields: ['name', 'formatted_address', 'geometry', 'rating',
                        'website', 'photos']
                };

                /* Only fetch the details of a place when the user clicks on a marker.
                 * If we fetch the details for all place results as soon as we get
                 * the search response, we will hit API rate limits. */
                service.getDetails(request, (placeResult, status) => {

                    showDetails(placeResult, marker, status,distanceInMeters)

                });
            });

            // Adjust the map bounds to include the location of this marker
            bounds.extend(place.geometry.location);

        });
        /* Once all the markers have been placed, adjust the bounds of the map to
         * show all the markers within the visible area. */

        setTimeout(function(){  $(".loader-spin").addClass("d-none"); map.fitBounds(bounds);},1000);




    }

    /* TODO: Step 4C: Show place details in an info window */
    // Builds an InfoWindow to display details above the marker
    function showDetails(placeResult, marker, status,distanceInMeters) {
        let rating = "None";
        let firstPhoto="";
        let img="";

        if (status == google.maps.places.PlacesServiceStatus.OK) {

            let placeInfowindow = new google.maps.InfoWindow();
            let rating = "None";
            if (placeResult.rating) rating = placeResult.rating;
            if (placeResult.photos) {
                let firstPhoto = placeResult.photos[0];
                let  src= firstPhoto.getUrl();
                img='<div class="thumb img-fluid img-size pt-2"><img src="'+src+'" style="width: 240px;"></div>';

            }
            /*

            <div class="thumb img-fluid img-size pt-2"><img src="'+im+'" style="width: 240px;"></div>
              <div class="room-info mg-left pt-3" ><b class="pr-2 bed-no">'+data.number_bedroom+'</b><i class="fa fa-bed fa-2x bed-icon" aria-hidden="true"></i><b class="pr-2 bath-no">'+data.number_bathroom+'</b><i class="fa fa-bath fa-2x bath-icon" aria-hidden="true"></i><i class="fa fa-building  square-icon" aria-hidden="true"></i><b class="pr-2 square-no">'+data.square_text+'</b></div>
            */

            var contentString= '<div class="infowindow-neighbour-wrap">'+img+'<div class="title-info pt-2"><b>'+placeResult.name+'</b></div><div class="location-info pt-2"><b>Rating:'+rating+'</b></div><div class="price-info pt-2"><b>'+placeResult.formatted_address+'</b></div><div class="price-info pt-2"><b>Distance From Property: '+distanceInMeters.toFixed(2)+' m</b></div></div>';

            placeInfowindow.setContent(contentString);
            placeInfowindow.open(marker.map, marker);
            currentInfoWindow.close();
            currentInfoWindow = placeInfowindow;
            //showPanel(placeResult);
        } else {
            console.log('showDetails failed: ' + status);
        }
    }

    /* TODO: Step 4D: Load place details in a sidebar */
    // Displays place details in a sidebar
    function showPanel(placeResult) {
        // If infoPane is already open, close it
        if (infoPane.classList.contains("open")) {
            infoPane.classList.remove("open");
        }

        // Clear the previous details
        while (infoPane.lastChild) {
            infoPane.removeChild(infoPane.lastChild);
        }

        /* TODO: Step 4E: Display a Place Photo with the Place Details */
        // Add the primary photo, if there is one
        if (placeResult.photos) {
            let firstPhoto = placeResult.photos[0];
            let photo = document.createElement('img');
            photo.classList.add('hero');
            photo.src = firstPhoto.getUrl();
            infoPane.appendChild(photo);
        }

        // Add place details with text formatting
        let name = document.createElement('h1');
        name.classList.add('place');
        name.textContent = placeResult.name;
        infoPane.appendChild(name);
        if (placeResult.rating) {
            let rating = document.createElement('p');
            rating.classList.add('details');
            rating.textContent = `Rating: ${placeResult.rating} \u272e`;
            infoPane.appendChild(rating);
        }
        let address = document.createElement('p');
        address.classList.add('details');
        address.textContent = placeResult.formatted_address;
        infoPane.appendChild(address);
        if (placeResult.website) {
            let websitePara = document.createElement('p');
            let websiteLink = document.createElement('a');
            let websiteUrl = document.createTextNode(placeResult.website);
            websiteLink.appendChild(websiteUrl);
            websiteLink.title = placeResult.website;
            websiteLink.href = placeResult.website;
            websitePara.appendChild(websiteLink);
            infoPane.appendChild(websitePara);
        }

        // Open the infoPane
        infoPane.classList.add("open");
    }
    $(document).ready(function() {
        $(".nearyby").click(function () {

            var keyword=$(this).attr("data-keyword");
            getNearbyPlaces(pos,keyword);
            map.fitBounds(bounds);
        });


        $(".vertical li").click(function () {

            $(".vertical li").removeClass("active");
            $(this).addClass('active');

        });
        $(".showGrid").click(function () {

            if($(this).attr('data-tab-id')==2) //neighbourbhood
            {
// pos = { lat: 33.856, lng: 73.215 };
                //  getNearbyPlaces(pos,'school');
                $(".mapouter").addClass('d-none');
                var keyword='school';
                getNearbyPlaces(pos,keyword);
                map.fitBounds(bounds);
            }
            else{
                $(".mapouter").removeClass('d-none');

            }
        });

    });

</script>


