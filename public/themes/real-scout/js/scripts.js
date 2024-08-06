(function($) {
    $(document).ready(function() {
        "use strict";


        // HOVER TOGGLE
        $('.side-navigation .menu ul li a').on('click', function(e) {
            $(this).parent().children('.side-navigation .menu ul li ul').slideToggle(300);
            return true;
        });
        $("#share_url").attr("href","https://api.whatsapp.com/send?text="+window.location.href);
        $("#fb_link").attr("href","https://www.facebook.com/sharer/sharer.php?u="+window.location.href);
        $("#twitter_link").attr("href","https://twitter.com/share?url="+window.location.href);

        // CONTACT FORM INPUT LABEL
        function checkForInput(element) {
            const $label = $(element).siblings('span');
            if ($(element).val().length > 0) {
                $label.addClass('label-up');
            } else {
                $label.removeClass('label-up');
            }
        }

        $('input, textarea').each(function(e) {
            checkForInput(this);
        });

        $('input, textarea').on('change keyup', function(e) {
            checkForInput(this);
        });



        // TOOLTIP
        $('[data-toggle="tooltip"]').tooltip()



        // PARALLAX
        $.stellar({
            horizontalScrolling: false,
            verticalOffset: 0,
            responsive:true
        });



        // DROPDOWN
        $('.dropdown-toggle').dropdown()



        // HAMBURGER
        $('.hamburger').on('click', function(e) {
            $(this).toggleClass('open');
            $('body').toggleClass('overflow');
            $('.side-navigation').toggleClass('active');
        });



        // DATA BACKGROUND IMAGE
        var pageSection = $("*");
        pageSection.each(function(indx){
            if ($(this).attr("data-background")){
                $(this).css("background-image", "url(" + $(this).data("background") + ")");
            }
        });



        // PAGE TRANSITION
        $('body a').on('click', function(e) {
            if (typeof $( this ).data('fancybox', 'filter') == 'undefined') {
                e.preventDefault();
                var url = this.getAttribute("href");
                if( url.indexOf('#') != -1 ) {
                    var hash = url.substring(url.indexOf('#'));


                    if( $('body ' + hash ).length != 0 ){
                        $('.transition-overlay').removeClass("active");
                        $(".hamburger").toggleClass("open");
                        $(".navigation-menu").removeClass("active");


                        $('html, body').animate({
                            scrollTop: $(hash).offset().top
                        }, 1300);

                    }
                }
                else {
                    $('.transition-overlay').toggleClass("active");
                    setTimeout(function(){
                        window.location = url;
                    },1300);

                }
            }
        });






    });
    $(document).ready(function(){

        /*$('#search_data').tokenfield({
            autocomplete :{
                source: function(request, response)
                {
                    $(".spinner-icon").css('display','block !important');
                    jQuery.get('get-search-area', {
                        query : request.term,
                        city_id:$('input[name="city_id"]').val(),
                        location:$('input[name="location"]').val(),
                        type:$('#txttypesearch').val()
                    }, function(data){
                        data = JSON.parse(data);
                         $(".spinner-icon").css('display','none');
                        response(data);
                    });
                },
                delay: 100
            }
        });*/


    });


    // COUNTER
    $(document).scroll(function(){
        $('.odometer').each( function () {
            var parent_section_postion = $(this).closest('section').position();
            var parent_section_top = parent_section_postion.top;
            if ($(document).scrollTop() > parent_section_top - 300) {
                if ($(this).data('status') == 'yes') {
                    $(this).html( $(this).data('count') );
                    $(this).data('status', 'no')
                }
            }
        });
    });




    /* GALLERY SLIDER */
    var swiper = new Swiper('.gallery-container', {
        slidesPerView: 'auto',
        spaceBetween: 0,
        loop: true,
        loadOnTransitionStart: true,
        autoplay: {
            delay: 0,
            disableOnInteraction: true,
        },
        pagination: {
            el: '.gallery-pagination',
            clickable: true,
        },
    });




    // SLIDER
    /*var swiper = new Swiper('.slider-container', {
        touchRatio: 0,
        loop: false,
        speed: 600,
        autoplay: {
            delay: 500,
            disableOnInteraction: false,
          },
          pagination: {
            el: '.pagination',
            type: 'fraction',
          },
          navigation: {
            nextEl: '.button-next',
            prevEl: '.button-prev',
          },
    }); */



    // MASONRY
    $(window).load(function(){
        $('.gallery').isotope({
            itemSelector: '.gallery li',
            percentPosition: true
        });
    });



    // ISOTOPE FILTER
    var $container = $('.gallery');
    $container.isotope({
        filter: '*',
        animationOptions: {
            duration: 750,
            easing: 'linear',
            queue: false
        }
    });

    $('.gallery-filter li a').click(function(){
        $('.gallery-filter li a.current').removeClass('current');
        $(this).addClass('current');

        var selector = $(this).attr('data-filter');
        $container.isotope({
            filter: selector,
            animationOptions: {
                duration: 750,
                easing: 'linear',
                queue: false
            }
        });
        return false;
    });

    // WOW ANIMATION
    wow = new WOW(
        {
            boxClass:     'wow',      // default
            animateClass: 'animated', // default
            offset:       100,          // default
            mobile:       true,       // default
            live:         true        // default
        }
    )
    wow.init();

    // PRELOADER
    $(window).load(function(){
        $("body").addClass("page-loaded");
    });

    $(window).scroll(function(){
        var sticky = $('.sticky-top'),
            scroll = $(window).scrollTop();

        if (scroll >= 2){
            sticky.addClass('fixed');
            $('.navbar .container .upper-side .phone-email').hide();
            $('.navbar .container .upper-side .hamburger').hide();
            $('.navbar .container .upper-side #profile_link').hide();
            $('#profile_link_sticky').show();
            $('#menu').hide();
            $('#menu-sticky').show();
            $('.login-up').hide();

            if($('#login_check').val()==1)
            {
                $('.sticky-login').addClass('d-none');
            }
            else
            {

                $('.sticky-login').removeClass('d-none');

            }
        }
        else {
            sticky.removeClass('fixed');
            $('.navbar .container .upper-side .phone-email').show();
            $('.navbar .container .upper-side .hamburger').show();
            $('.navbar .container .upper-side #profile_link').show();
            $('#profile_link_sticky').hide();
            $('#menu').show();
            $('#menu-sticky').hide();
            $('.login-up').show();
            $('.sticky-login').addClass('d-none');
        }
    });

    // Home page Search Geocode Location //
    if(document.location.pathname === "/") {


        //var latlng;
        var map;
        var marker;
        var lat;
        var lng;
        var latlng;
        var geocoder;
        var infowindow;
//latlng = new google.maps.LatLng(40.730885, -73.997383); // New York, US
//latlng = new google.maps.LatLng(37.990849233935194, 23.738339349999933); // Athens, GR
//latlng = new google.maps.LatLng(48.8567, 2.3508); // Paris, FR
//latlng = new google.maps.LatLng(47.98247572667902, -102.49018710000001); // New Town, US
//latlng = new google.maps.LatLng(35.44448406385493, 50.99001635390618); // Parand, Tehran, IR
//latlng = new google.maps.LatLng(34.66431108560504, 50.89113940078118); // Saveh, Markazi, IR

        navigator.geolocation.getCurrentPosition(function (position) {

            let coords = position.coords;
            lat = coords.latitude;
            lng = coords.longitude;
            $('#latitude').val(lat);
            $('#longitude').val(lng);
            latlng = new google.maps.LatLng(lat, lng);
            new google.maps.Geocoder().geocode({'latLng': latlng}, function (results, status) {

                console.log(result, status);
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        var country = null, countryCode = null, city = null, cityAlt = null;
                        var c, lc, component;
                        for (var r = 0, rl = results.length; r < rl; r += 1) {
                            var result = results[r];

                            if (!city && result.types[0] === 'locality') {
                                for (c = 0, lc = result.address_components.length; c < lc; c += 1) {
                                    component = result.address_components[c];

                                    if (component.types[0] === 'locality') {
                                        city = component.long_name;
                                        break;
                                    }
                                }
                            } else if (!city && !cityAlt && result.types[0] === 'administrative_area_level_1') {
                                for (c = 0, lc = result.address_components.length; c < lc; c += 1) {
                                    component = result.address_components[c];

                                    if (component.types[0] === 'administrative_area_level_1') {
                                        cityAlt = component.long_name;
                                        break;
                                    }
                                }
                            } else if (!country && result.types[0] === 'country') {
                                country = result.address_components[0].long_name;
                                countryCode = result.address_components[0].short_name;
                            }

                            if (city && country) {
                                break;
                            }
                        }

                        console.log("City: " + city + ", City2: " + cityAlt + ", Country: " + country + ", Country Code: " + countryCode);
                        var url = window.location.href;
                        if (url.indexOf("location") > -1) {
                            if ($('.select-city-state').val() == "")
                                $('.select-city-state').val(city);
                        }
                        window.homepagecheck = function () {
                            var check = false;
                            if (document.location.pathname === "/") {
                                check = true;
                            }
                            return check;
                        }
                        if (window.homepagecheck()) {
                            $('.select-city-state').val(city);
                        }


                    }
                } else {
                    document.getElementById('error').innerHTML = "Error Status: " + status;
                }
            });
        });
    }
    ///////////////////////////for search locatio on map /////////




    $(window).load(function () {
        $(".nearyby:first").trigger("click");

        function initMap() {

            var editlat = $('#latitude').val();
            var editLng = $('#longitude').val();
            myLatlng = new google.maps.LatLng(editlat, editLng);
            geocoder = new google.maps.Geocoder();
            infowindow = new google.maps.InfoWindow();
            var mapOptions = {
                zoom: 18,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("map"), mapOptions);

            marker = new google.maps.Marker({
                map: map,
                position: myLatlng,
                draggable: true
            });

            geocoder.geocode({'latLng': myLatlng}, function (results, status) {

                $('#latitude,#longitude').show();
                // $('#location_map').val(results[0].formatted_address);
                $('#latitude').val(marker.getPosition().lat());
                $('#longitude').val(marker.getPosition().lng());
                console.log(results[0]);
                infowindow.setContent(results[0].formatted_address);
                infowindow.open(map, marker);


            });

            //const input = document.getElementById("location_map");
            //const searchBox = new google.maps.places.SearchBox(input);
            // console.log(searchBox.getPlaces());
            // Bias the SearchBox results towards current map's viewport.
            /*map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
                console.log(searchBox.getPlaces());
            });*/
            //  let markers = [];
            /*searchBox.addListener("places_changed", () => {
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


                   // marker.setPosition(place.geometry.location);
                    var inputString=place.geometry.location;
                    infowindow.setContent($('#location_map').val());
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
            });*/

            google.maps.event.addListener(marker, 'dragend', function () {

                geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {
                    //   $('#location_map').val(results[0].formatted_address);
                    $('#latitude').val(marker.getPosition().lat());
                    $('#longitude').val(marker.getPosition().lng());
                    infowindow.setContent(results[0].formatted_address);
                    console.log(results[0]);
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
        if(document.location.pathname === "/") {
            initMap();
            $(".category-parent-active").click(function () {
                $(".p-category").removeClass("category-parent-active").addClass('category-parent-inactive');
                $(this).addClass('category-parent-active');
                var p_id = $(this).attr("data-id");
                $(".pcateory_data").html($(".p" + p_id).html());
                $("#projectdropdownMenuLink").siblings(".category_id").val(p_id);
                $("#propertydropdownMenuLink").siblings(".category_id").val(p_id);
                $("#projectdropdownMenuLink").siblings(".category_id_text").html($(this).text());
                $("#propertydropdownMenuLink").siblings(".category_id_text").html($(this).text());
            });
            $(".p-category").click(function () {
                var p_category = $(this).text();
                if (p_category == "PLOTS" || p_category == "Plots" || p_category == "plot" || p_category == "PLOT") {
                    $(".bedrooms").addClass('d-none');
                    $(".bathrooms").addClass('d-none');
                    $(".floors").addClass('d-none');
                    $(".home-price-dp").addClass('d-none');
                    $(".plot-price-dp").removeClass('d-none');
                    $(".commerical-floors").addClass('d-none');
                    $(".home-floors").addClass('d-none');
                } else if (p_category == "COMMERCIALS" || p_category == "Commercials" || p_category == "COMMERCIAL" || p_category == "Commercials") {
                    $(".bedrooms").addClass('d-none');
                    $(".bathrooms").addClass('d-none');
                    $(".home-price-dp").addClass('d-none');
                    $(".plot-price-dp").removeClass('d-none');
                    $(".commerical-floors").removeClass('d-none');
                    $(".home-floors").addClass('d-none');
                } else //HOME
                {
                    $(".bedrooms").removeClass('d-none');
                    $(".bathrooms").removeClass('d-none');
                    $(".floors").removeClass('d-none');
                    $(".home-price-dp").removeClass('d-none');
                    $(".plot-price-dp").addClass('d-none');
                    $(".commerical-floors").addClass('d-none');
                    $(".home-floors").addClass('d-none');

                }
                $(".p-category").removeClass("category-parent-active").addClass('category-parent-inactive');
                $(this).addClass('category-parent-active');
                var p_id = $(this).attr("data-id");
                $(".pcateory_data").html($(".p" + p_id).html());
                $("#projectdropdownMenuLink").siblings(".category_id").val(p_id);
                $("#propertydropdownMenuLink").siblings(".category_id").val(p_id);
                $("#projectdropdownMenuLink").siblings(".category_id_text").html($(this).text());
                $("#propertydropdownMenuLink").siblings(".category_id_text").html($(this).text());

            });

            $(".category-parent-active").trigger("click");
        }

    });
    //////////////home page popup for change price unit & change area units
    $("#changeCurrency").click(function () {
        $("#curreny_modal").modal("show");
    });

    $("#changeAreaUnit").click(function () {
        $("#area_modal").modal("show");
    });
    $("#search_map").click(function () {
        $("#search_map_modal").modal("show");
    });
    $("#projectdropdownMenuLink").click(function () {
        $(".project-category-search-dropdown").css("display","block");
    });
    $("#propertydropdownMenuLink").click(function () {
        $(".property-category-search-dropdown").css("display","block");
    });
    $("body").on("click",function(e){
        var container = $(".project-category-search-dropdown");
        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0)
        {
            container.hide();
        }
        var containerp = $(".property-category-search-dropdown");
        // if the target of the click isn't the container nor a descendant of the container
        if (!containerp.is(e.target) && containerp.has(e.target).length === 0)
        {
            containerp.hide();
        }
    });
    $( ".pcateory_data" ).on( "click",".category-li-item", function( event ) {
        event.preventDefault();
        var sub_cat=$(this).text();
        var parent_name=$(this).attr('parent-name');
        if(parent_name=="PLOTS" || parent_name == "PLOTS" || parent_name == "Plots" || parent_name == "plot" || parent_name == "PLOT" || parent_name=="COMMMERCIALS" || parent_name == "COMMERCIALS" || parent_name == "Commercials" || parent_name == "COMMERCIAL" || parent_name == "Commercials")
        {

            $(".bedrooms").addClass('d-none');
            $(".bathrooms").addClass('d-none');
        }
        else
        {
            $(".bedrooms").removeClass('d-none');
            $(".bathrooms").removeClass('d-none');

        }
        $(".category-li-item").removeClass("category-li-item-active");
        $(this).addClass('category-li-item-active');
        var id= $(this).attr("data-id");
        $("#projectdropdownMenuLink").siblings(".category_id").val(id);
        $("#propertydropdownMenuLink").siblings(".category_id").val(id);
        $("#projectdropdownMenuLink").siblings(".category_id_text").html($(this).text());
        $("#propertydropdownMenuLink").siblings(".category_id_text").html($(this).text());
        $(".project-category-search-dropdown").css('display','none');
        $(".property-category-search-dropdown").css('display','none');

    });
    $( "#hometypesearch" ).on( "click","a", function( event ) {
        event.preventDefault();
        type=$("#txttypesearch").val();
        $(this).parent().parent().find(".p-category").first().addClass("category-parent-active");
        $(this).parent().parent().find(".p-category").first().trigger("click");
    });
////
// ////////
    /////////////////price min max box ////////////
    $('.price-dropdown, .currency, #min-max-price-range, a').on('click',function () {
        $('.property-category-search-dropdown').css('display','none');
    });
    /*var priceLabelObj;
    $('.price-label').focus(function (event) {
        priceLabelObj=$(this);
       // $('.price-range').addClass('hide');
        //$('#'+$(this).data('dropdownId')).removeClass('hide');
    });*/

    $(".price-range li").click(function(e){
        // alert("here");
        e.stopPropagation();
        console.log('this is clicked');
        if($(this).parent().hasClass("price-min-ul")) {
            $('[data-dropdown-id="price-min"]').val($(this).attr('data-value'));
            $('[data-dropdown-id="price-min"]').change();
            $(".price-min-ul li").removeClass("category-li-item-active");
            $(this).addClass('category-li-item-active');
            $('.min_price_text').html($(this).attr('data-value'));
            $('#min-max-price-range').show();
            console.log('this is if');
        }
        else
        {
            $(".price-max-ul li").removeClass("category-li-item-active");
            $(this).addClass('category-li-item-active');
            $('[data-dropdown-id="price-max"]').val($(this).attr('data-value'));

            $('[data-dropdown-id="price-max"]').change();
            $('.max_price_text').html($(this).attr('data-value'));
            $('#min-max-price-range').show();
            console.log('this is else');
        }
        /* $("#input_max_price")[0].dispatchEvent(new Event('change'));
         $("#input_min_price")[0].dispatchEvent(new Event('change'));*/
        var el = document.getElementById('input_min_price');
        el.dispatchEvent(new Event('input'));

        var el = document.getElementById('input_max_price');
        el.dispatchEvent(new Event('input'));


    });

    $('#input_min_price').on('change',function (){
        $('.min_price_text').html($(this).val());
    });
    $('#input_max_price').on('change',function (){
        $('.max_price_text').html($(this).val());
    });

    //for max and min area units
    $(".units-range li").click(function(e){
        e.stopPropagation();
        console.log('units range item is clicked');
        if($(this).parent().hasClass("unit-min-ul")) {

            $('[data-dropdown-id="unit-min"]').val($(this).attr('data-value'));

            $('[data-dropdown-id="unit-min"]').change();
            $(".unit-min-ul li").removeClass("category-li-item-active");
            $(this).addClass('category-li-item-active');
            $('.min_unit_text').html($(this).attr('data-value'));
            $('#min-max-unit-range').show();
            console.log('units range min item is clicked');
        }
        else
        {
            $(".unit-max-ul li").removeClass("category-li-item-active");
            $(this).addClass('category-li-item-active');
            $('[data-dropdown-id="unit-max"]').val($(this).attr('data-value'));

            $('[data-dropdown-id="unit-max"]').change();
            $('.max_unit_text').html($(this).attr('data-value'));
            $('#min-max-unit-range').show();
            console.log('units range max item is clicked this one');
        }
        /* $("#input_max_price")[0].dispatchEvent(new Event('change'));
         $("#input_min_price")[0].dispatchEvent(new Event('change'));*/
        var el = document.getElementById('input_min_unit');
        el.dispatchEvent(new Event('input'));

        var el = document.getElementById('input_max_unit');``
        el.dispatchEvent(new Event('input'));


    });

    $('#input_min_unit').on('change',function (){
        $('.min_unit_text').html($(this).val());
    });
    $('#input_max_unit').on('change',function (){
        $('.max_unit_text').html($(this).val());
    });

    ////////////property page search-map///////////////

    $(document).ready(function() {
        $(".porperties_list").css("display","none");
        /*  map = new L.Map(
             'property_search_map',{scrollWheelZoom: false});
         map.doubleClickZoom.disable();
         L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
             attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
         }).addTo(map);

         var houston = new L.LatLng(33.74928730,72.78111600);
         map.setView(houston,10);

         var drawnItems = new L.FeatureGroup();
         map.addLayer(drawnItems);

         var drawControl = new L.Control.Draw({
             draw: {
                 circle: false,
                 marker: false,
                 polyline: false
             },
             edit: {
                 featureGroup: drawnItems
             }
         });

         map.addControl(drawControl);

         var markers = [];
         var newParam="";
         var polygon = null;
         var url=window.location.href;
         var separator ="?";
         map.on('draw:created', function (e) {

             // remove markers and polygon from the last run
             $.each (markers, function (i) { map.removeLayer(markers[i]) });
             if (polygon != null) map.removeLayer (polygon);
             var i=0;
             $("#maplatlng").empty();
             var latLngs = $.map(e.layer.getLatLngs(), function(o) {
                 i++;

                 $("#maplatlng").append("<input type='hidden' id='latLngs-"+i+"' name='latLngs-"+i+"' value='"+o.lat+","+o.lng +"' > ");
                 console.log("<input type='text' id='latLngs-"+i+"' name='latLngs-"+i+"' value='"+o.lat+","+o.lng +"'  >");
                 newParam+=separator+"latLngs-"+i+"="+o.lat + "," + o.lng;
                 separator="&";
                 return { name: "points[]", value: o.lat + "," + o.lng };


             });
             var i=0;
             $.ajax({
                 url: '/
                 ?type=mapsearch',
                 data: latLngs,
                  beforeSend: function(xhr) {
                      xhr.setRequestHeader("Authorization", "Basic " + btoa("simplyrets:simplyrets"))
                  },

                 success: function(data) {

                     $.each (data.data, function (idx) {
                             var markerLocation = new L.LatLng(data.data[i].latitude, data.data[i].longitude);
                             $("#maplatitude").val(data.data[i].latitude);
                             $("#maplongitude").val(data.data[i].longitude);
                             var marker = new L.Marker(markerLocation);
                             map.addLayer(marker);
                             markers.push(marker);
                             i++;

                     });

                 },
                 complete:function (data) {
                     setTimeout(
                         function()
                         {
                            // newUrl=url.split('?')[0].replace(newParam,"");
                            // newUrl+=newParam;
                             //window.location.href =newUrl;
                             //do something special
                         }, 2000);
                     }
             });

             polygon = e.layer;
             map.addLayer(e.layer);
         });
         var url = window.location.href;
         var vars = [];
         var arguments;
         var check;

         if(url.indexOf("?") > -1) {
             arguments = url.split('?')[1].split('&');
             var q = document.URL.split('?')[1];
             check = url.split('?')[1];


             if (check.indexOf("latLngs-1") > -1) {
                 arguments = q.split('&');

                 for (var i = 0; i < arguments.length; i++) {
                     hash = arguments[i].split('=');
                     latlng = hash[1].split(',');
                     str1 = latlng[0].replace(/^"(.*)"$/, '$1');
                     str2 = latlng[1].replace(/^"(.*)"$/, '$1');
                     vars.push([parseFloat(str1), parseFloat(str2)]);

                 }
                 console.log(vars);
                 var latlngs = vars;
                 // var latlngs =[[33.55055114384406, 71.97698020725511],[33.9672812214173,71.97698020725511],[33.9672812214173,73.68260764866136],[33.55055114384406,73.68260764866136]];
                 var polygon = L.polygon(latlngs, {color: 'red', weight: 2}).addTo(map);
                 // zoom the map to the polygon
                 map.fitBounds(polygon.getBounds());

             }
         }
         ///setting markers og latlng
         j=0;

             $("input[name^='map_latitude']").each(function () {
                 var markerLocation = new L.LatLng($(this).val(), $(this).siblings().val());
                 $("#maplatitude").val($(this).val());
                 $("#maplongitude").val($(this).siblings().val());
                 var marker = new L.Marker(markerLocation);
                 map.addLayer(marker);
                 markers.push(marker);

                 j++;

             });



 */
    });
    $(".btn-reset-price").click(function (e) {
        e.stopPropagation();
        // $("input[name='max_price']").val('');
        // $("input[name='min_price']").val('');
        $('input[data-dropdown-id="price-min"]').val('');
        $('input[data-dropdown-id="price-max"]').val('');
        $('input[data-dropdown-id="price-min"]').val('');
        $('input[data-dropdown-id="price-max"]').val('');
        $('input[data-dropdown-id="price-max"]').attr("placeholder","Max");
        $('input[data-dropdown-id="price-min"]').attr("placeholder","Min");
        $('.min_price_text').html("0");
        $('.max_price_text').html("Any");
        $(".price-min-ul li").removeClass("category-li-item-active");
        $(".price-max-ul li").removeClass("category-li-item-active");
    });
    $(".btn-reset-unit").click(function (e) {
        e.stopPropagation();
        $('[data-dropdown-id="unit-max"]').val("");
        $('[data-dropdown-id="unit-min"]').val("");
        $('[data-dropdown-id="unit-max"]').attr("placeholder","Max");
        $('[data-dropdown-id="unit-min"]').attr("placeholder","Min");
        $('.min_unit_text').html("0");
        $('.max_unit_text').html("Any");
        $(".unit-min-ul li").removeClass("category-li-item-active");
        $(".unit-max-ul li").removeClass("category-li-item-active");
    });
    /*$('#area_units-val').on('change',function(e) {
        e.stopPropagation();
        $("input[name='max_unit']").val('');
        $("input[name='min_unit']").val('');
        $('[data-dropdown-id="unit-max"]').val("");
        $('[data-dropdown-id="unit-min"]').val("");
        $('[data-dropdown-id="unit-max"]').attr("placeholder","Max");
        $('[data-dropdown-id="unit-min"]').attr("placeholder","Min");
        $('.min_unit_text').html("0");
        $('.max_unit_text').html("Any");
        $(".unit-min-ul li").removeClass("category-li-item-active");
        $(".unit-max-ul li").removeClass("category-li-item-active");
    });*/
    $('.clickBtn').click(function(){
        var value= $(this).attr("data-attr");
        if(value=="map")
        {

            $(".clickBtn").removeClass("active");
            $(this).addClass("active");
            $(".porperties_list").css("display","none");
            $("#map-container").css("visibility","visible");
            $("#map-container").css("height","100%");
            $(".properties_side_list").css("display","block");

        }
        if(value=="list")
        {
            $(".clickBtn").removeClass("active");
            $(this).addClass("active");
            $(".porperties_list").css("display","block");
            $("#map-container").css("visibility","hidden");
            $("#map-container").css("height","0");
            $(".properties_side_list").css("display","none");

        }

    });
    if($.isFunction('owlCarousel')){
        $("#owl-demo").owlCarousel({
            navigation : true
        });
    }

    $("body").on("click",function(e){
        var container = $(".getlocation");

        // If the target of the click isn't the container
        if(!container.is(e.target) && container.has(e.target).length === 0){
            $(".list_suggest").hide();
        }
    });
    $(document).ready(function(){
        $(this).scrollTop(0);
    });
    $(document).ready(function(){

        var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
            removeItemButton: true,
            maxItemCount:false,
            searchResultLimit:false,
            renderChoiceLimit:false
        });


    });
    $(document).ready(function(){
        $.ajax({
            type: 'get',
            url: "/api/v1/get-term-conditions",
            /* processData: false,
             contentType: false,*/
            dataType: 'json',
            async:false,
            success: function (data) {

                $("#term_condition_body").html(data.html);
            },

        });
        $('#modal_terms').on('click', function() {

            if($("input[name='modal_terms']:checked")){
                $("#terms").prop("checked",true);
                $("#exampleModal").modal("hide");
                $(document.body).removeClass("modal-open");
                $(".modal-backdrop").remove();
            }
            else{


            }
        });
        $(".showInfoArea").click(function() {


            $.ajax({
                url: "/api/v1/area-units",
                type: "get",
                dataType: 'json',
                async:false,
                data:{area:$("#square").val(),unit:$("#area_units").val()},

                success: function (response) {
                    $(".infoTitle").removeClass("d-none");
                    $(".infoTitle").html(response.html);

                },
            });

        });
        $( ".showInfoArea" ).mouseleave(function() {
            $(".infoTitle").addClass('d-none');
        });
        $("#update_area").click(function () {

            $.ajax({
                url: "ajax/area_unit_update",
                type: "get",
                dataType: 'json',
                async:false,
                data:{area_unit:$("#area_units-val").val()},

                success: function (response) {
                    if(response.status)
                    {
                        //  $("#area_modal").modal("hide");
                        window.location.reload();
                    }else
                    {
                        //$("#area_modal").modal("hide");
                        window.location.reload();
                    }
                },
            });
        });
        $("#update_currency").click(function () {

            $.ajax({
                url: "ajax/currency_unit_update",
                type: "get",
                dataType: 'json',
                async:false,
                data:{currency_unit:$("#currency_val").val()},

                success: function (response) {
                    if(response.status)
                    {
                        //  $("#currency_modal").modal("hide");
                        window.location.reload();
                    }else
                    {
                        //$("#currency_modal").modal("hide");
                        window.location.reload();
                    }
                },
            });
        });

        $(".price-dropdown").click(function () {

        });
    });
    $(window).load(function () {
        $('.price-min-ul li').each(function(i, obj) {
            //test
            var min_price=$("input[name='min_price']").val();
            var li_attr=$(this).attr('data-value');
            if(min_price==li_attr)
            {
                $(this).trigger("click");
            }
        });
        $('.price-max-ul li').each(function(i, obj) {
                //test
                var max_price=$("input[name='max_price']").val();
                var li_attr=$(this).attr('data-value');
                if(max_price==li_attr)
                {
                    $(this).trigger("click");
                }
            }
        );
    });
    /*   $(document).ready(function(){

           $("#contact-form").validationEngine();


       });*/

})(jQuery);
