/*
    Path in theme: platform/themes/real-scout/public/js/new-home-page/properties-map.js
    Loaded via:    config.php's beforeRenderTheme, gated on route name "public.properties"
                    (footer container, after the Leaflet CDN script).

    No-ops entirely on any page where #properties-map-canvas doesn't exist
    (e.g. the home page, which renders the identical #frmhomesearch search
    bar markup but must keep its normal full-page-navigation submit behavior
    untouched).
*/
(function () {
    'use strict';

    var canvas = document.getElementById('properties-map-canvas');
    if (!canvas || typeof L === 'undefined') {
        return;
    }

    var ajaxUrl = canvas.getAttribute('data-ajax-url');
    var visitorLat = parseFloat(canvas.getAttribute('data-visitor-lat'));
    var visitorLng = parseFloat(canvas.getAttribute('data-visitor-lng'));
    var visitorSource = canvas.getAttribute('data-visitor-source');
    var countBadge = document.getElementById('properties-map-count');
    var searchForm = document.getElementById('frmhomesearch');
    var locationBtn = document.getElementById('propertiesMyLocationBtn');
    var typeField = document.getElementById('txttypesearch');
    var resultsGrid = document.getElementById('properties-search-results');

    // The search bar's own Buy/Rent/Projects tab-switching used to be broken
    // site-wide (a duplicate jQuery include in config.php wiped out the
    // owlCarousel plugin and crashed app.js's ready handler before it reached
    // the tab-click binding - now fixed at the source). Kept this redundant,
    // idempotent handler anyway so this page's search still doesn't depend on
    // that global script succeeding.
    var tabs = searchForm ? searchForm.querySelectorAll('.hero-search-card__tab') : [];
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');
            if (typeField && tab.getAttribute('rel') !== 'project') {
                typeField.value = tab.getAttribute('rel');
            }
        });
    });

    // Pakistan-wide fallback, used whenever the visitor's location couldn't be
    // resolved to anything better than the app's hardcoded default (source "default").
    var initialCenter = [30.3753, 69.3451];
    var initialZoom = 6;

    var hasVisitorCoords = (visitorSource === 'ip' || visitorSource === 'browser') &&
        !isNaN(visitorLat) && !isNaN(visitorLng) && (visitorLat !== 0 || visitorLng !== 0);

    if (hasVisitorCoords) {
        initialCenter = [visitorLat, visitorLng];
        initialZoom = 11; // city-level zoom, since ip/browser coords are city-precision
    }

    var map = L.map(canvas, { scrollWheelZoom: true }).setView(initialCenter, initialZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    var markersLayer = L.layerGroup().addTo(map);

    var goldPinIcon = L.divIcon({
        className: 'properties-map-pin-wrapper',
        html: '<div class="properties-map-pin"><span>G</span></div>',
        iconSize: [34, 34],
        iconAnchor: [17, 34],
        popupAnchor: [0, -34]
    });

    function escapeHtml(value) {
        var div = document.createElement('div');
        div.textContent = value == null ? '' : String(value);
        return div.innerHTML;
    }

    function buildPopupHtml(property) {
        var image = property.image
            ? '<img class="properties-map-popup__image" src="' + escapeHtml(property.image) + '" alt="' + escapeHtml(property.name) + '">'
            : '';

        return (
            '<div class="properties-map-popup">' +
            image +
            '<p class="properties-map-popup__price">' + escapeHtml(property.price) + '</p>' +
            '<a class="properties-map-popup__title" href="' + escapeHtml(property.url) + '">' + escapeHtml(property.name_short || property.name) + '</a>' +
            '</div>'
        );
    }

    function buildResultCardHtml(property) {
        var image = property.image
            ? '<img src="' + escapeHtml(property.image) + '" alt="' + escapeHtml(property.name) + '" class="properties-highlights__card-image" loading="lazy">'
            : '';
        var badge = property.type === 'rent' ? 'For Rent' : 'For Sale';

        var meta = '';
        if (property.number_bedroom) {
            meta += '<span><i class="fas fa-bed"></i> ' + escapeHtml(property.number_bedroom) + ' Beds</span>';
        }
        if (property.number_bathroom) {
            meta += '<span><i class="fas fa-bath"></i> ' + escapeHtml(property.number_bathroom) + ' Baths</span>';
        }
        if (property.square_text) {
            meta += '<span><i class="fas fa-ruler-combined"></i> ' + escapeHtml(property.square_text) + '</span>';
        }

        var location = property.location
            ? '<p class="properties-highlights__card-location"><i class="fas fa-map-marker-alt"></i> ' + escapeHtml(property.location) + '</p>'
            : '';

        return (
            '<a href="' + escapeHtml(property.url) + '" title="' + escapeHtml(property.name) + '" class="properties-highlights__card">' +
            '<div class="properties-highlights__card-image-wrap">' + image +
            '<span class="properties-highlights__card-badge">' + badge + '</span>' +
            '</div>' +
            '<div class="properties-highlights__card-body">' +
            '<p class="properties-highlights__card-price">' + escapeHtml(property.price) + '</p>' +
            '<h3 class="properties-highlights__card-title">' + escapeHtml(property.name_short || property.name) + '</h3>' +
            location +
            '<div class="properties-highlights__card-meta">' + meta + '</div>' +
            '</div>' +
            '</a>'
        );
    }

    function renderResultsGrid(properties) {
        if (!resultsGrid) {
            return;
        }

        if (!properties.length) {
            resultsGrid.innerHTML = '<p class="properties-highlights__empty">No properties match your search.</p>';
            return;
        }

        resultsGrid.innerHTML = properties.map(buildResultCardHtml).join('');
    }

    function renderPins(properties) {
        markersLayer.clearLayers();

        var bounds = [];

        (properties || []).forEach(function (property) {
            var lat = parseFloat(property.latitude);
            var lng = parseFloat(property.longitude);

            if (!lat || !lng) {
                return;
            }

            var marker = L.marker([lat, lng], { icon: goldPinIcon }).bindPopup(buildPopupHtml(property));
            markersLayer.addLayer(marker);
            bounds.push([lat, lng]);
        });

        if (countBadge) {
            countBadge.textContent = bounds.length + (bounds.length === 1 ? ' Property Found' : ' Properties Found');
        }

        if (bounds.length > 0) {
            map.fitBounds(bounds, { maxZoom: 13, padding: [40, 40] });
        }
    }

    function fetchAndRenderPins(params) {
        var query = new URLSearchParams(params);

        if (resultsGrid) {
            resultsGrid.innerHTML = '<p class="properties-highlights__empty">Searching...</p>';
        }

        fetch(ajaxUrl + '?' + query.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                var data = json && Array.isArray(json.data) ? json.data : [];
                renderPins(data);
                renderResultsGrid(data);
            })
            .catch(function () {
                if (countBadge) {
                    countBadge.textContent = '0 Properties Found';
                }
                renderResultsGrid([]);
            });
    }

    // The "Area" range inputs/hidden field use different names AND a
    // different value format than what PropertyRepository::getPropertiesByMap()
    // actually reads - the form was never wired to the real filter keys
    // (min_square/max_square/unit), so selecting e.g. "1 to 15 marla"
    // silently filtered nothing at all server-side and every property
    // leaked through regardless of size.
    var FIELD_REMAP = {
        'min_unit': 'min_square',
        'max_unit': 'max_square',
        'selected-unit': 'unit'
    };

    // #selected-unit's value is a human label from getDefaultAreaByUnitForNextPage()
    // ("Marla", "Square feet", ...) - the backend's unit-conversion switch
    // in getPropertiesByMap() matches on the short codes used by the Area
    // Unit modal's own <select> (m², ft², marla, yard, kanal) instead.
    var UNIT_LABEL_TO_CODE = {
        'Square meter': 'm²',
        'Square feet': 'ft²',
        'Marla': 'marla',
        'Yards': 'yard',
        'Kanal': 'kanal'
    };

    function buildSearchParams(form) {
        var formData = new FormData(form);
        var params = new URLSearchParams();

        formData.forEach(function (value, key) {
            if (key === 'type') {
                return; // remapped below
            }
            var realKey = FIELD_REMAP[key] || key;
            if (realKey === 'unit') {
                value = UNIT_LABEL_TO_CODE[value] || value;
            }
            if (value !== '') {
                params.append(realKey, value);
            }
        });

        var propertyType = form.querySelector('#txttypesearch');
        params.set('property_type', propertyType ? propertyType.value : 'sale');
        params.set('type', 'mapsearch');
        params.set('per_page', 300);

        return params;
    }

    // Initial, unfiltered load (reuses the server's location-default fallback).
    fetchAndRenderPins({ type: 'mapsearch', per_page: 300 });

    if (searchForm) {
        searchForm.addEventListener('submit', function (event) {
            var activeTab = searchForm.querySelector('.hero-search-card__tab.active');
            if (activeTab && activeTab.getAttribute('rel') === 'project') {
                return; // let it navigate normally to public.projects
            }
            event.preventDefault();
            fetchAndRenderPins(buildSearchParams(searchForm));
        });
    }

    if (locationBtn) {
        locationBtn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    map.setView([position.coords.latitude, position.coords.longitude], 13);
                },
                function () {
                    alert('Unable to retrieve your location.');
                }
            );
        });
    }
})();
