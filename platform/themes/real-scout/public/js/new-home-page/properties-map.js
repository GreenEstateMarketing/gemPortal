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

    var map = L.map(canvas, { scrollWheelZoom: false }).setView([30.3753, 69.3451], 6); // Pakistan-wide default

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

    function buildSearchParams(form) {
        var formData = new FormData(form);
        var params = new URLSearchParams();

        formData.forEach(function (value, key) {
            if (key === 'type') {
                return; // remapped below
            }
            if (value !== '') {
                params.append(key, value);
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
