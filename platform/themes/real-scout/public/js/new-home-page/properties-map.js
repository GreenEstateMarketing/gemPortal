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
    var suggestionsSection = document.getElementById('properties-search-suggestions');
    var suggestionsGrid = suggestionsSection ? suggestionsSection.querySelector('.properties-highlights__grid') : null;
    var suggestionsText = suggestionsSection ? suggestionsSection.querySelector('.properties-highlights__suggestions-text') : null;

    // The search bar's own Buy/Rent/Projects tab-switching used to be broken
    // site-wide (a duplicate jQuery include in config.php wiped out the
    // owlCarousel plugin and crashed app.js's ready handler before it reached
    // the tab-click binding - now fixed at the source). Kept this redundant,
    // idempotent handler anyway so this page's search still doesn't depend on
    // that global script succeeding.
    // The tabs are rendered as siblings of #frmhomesearch, not inside it
    // (see search-bar.blade.php), so they must be queried from the document,
    // not scoped to the form - scoping to the form here always finds none.
    var tabs = document.querySelectorAll('.hero-search-card__tab');
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

    function renderResultsGrid(properties, suggestions) {
        if (!resultsGrid) {
            return;
        }

        if (!properties.length) {
            resultsGrid.innerHTML = '<p class="properties-highlights__empty">No properties found matching your search criteria.</p>';
            renderSuggestions(suggestions);
            return;
        }

        resultsGrid.innerHTML = properties.map(buildResultCardHtml).join('');
        // Only ever relevant as a "nothing matched, here's something close"
        // fallback - hide it the moment there IS a real result set, even if
        // the response happened to carry suggestions from an earlier filter
        // state.
        renderSuggestions(null);
    }

    // "suggestions" (added by FlexHomeController::ajaxGetProperties's
    // mapsearch branch) is the progressively-broadened result set - area
    // removed, then city removed too - used only when the exact search came
    // back empty, so the visitor isn't just left looking at "no properties".
    function renderSuggestions(suggestions) {
        if (!suggestionsSection) {
            return;
        }

        if (!suggestions || !suggestions.data || !suggestions.data.length) {
            suggestionsSection.style.display = 'none';
            return;
        }

        if (suggestionsText) {
            suggestionsText.textContent = suggestions.text;
        }
        if (suggestionsGrid) {
            suggestionsGrid.innerHTML = suggestions.data.map(buildResultCardHtml).join('');
        }

        suggestionsSection.style.display = '';
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
                renderResultsGrid(data, json && json.suggestions);
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

    // When arriving here with a query string (e.g. from the home page's own
    // copy of this same search form, submitted as a plain GET), reflect
    // those values back into the form's fields - and the separate label
    // spans that mirror hidden/range fields but don't listen for this kind
    // of programmatic update - so the UI shows what was actually searched.
    function populateFormFromQuery(form) {
        if (!form) {
            return false;
        }

        var query = new URLSearchParams(window.location.search);
        var hasFilters = false;

        query.forEach(function (value, key) {
            if (!value) {
                return;
            }
            if (key.slice(-2) === '[]') {
                // Array-style fields (currently just the area/neighborhood
                // "keyword[]" chips) aren't plain single-value fields with a
                // fixed set of elements to write into - homechoosen.js's
                // own restoreAreaChipsFromQuery() rebuilds those chips (and
                // their hidden inputs) from the same query string already,
                // so this generic single-value setter must leave them alone:
                // it would otherwise stamp every matching hidden input with
                // whichever value it last saw, losing all but one chip.
                hasFilters = true;
                return;
            }
            var fields = form.querySelectorAll('[name="' + key + '"]');
            if (!fields.length) {
                return;
            }
            fields.forEach(function (field) {
                var changed = field.value !== value;
                field.value = value;
                // #city_id is wrapped in select2 (homechoosen.js) - it only
                // redraws its own fake widget in response to a jQuery
                // "change" event, so a plain DOM .value assignment leaves
                // the correct option selected under the hood but the
                // visible label stuck on whatever it showed before. Only
                // fire that when the value is actually changing though:
                // homechoosen.js's own change handler clears every area
                // chip on a city change (correctly, for a real change) -
                // triggering it here when city_id already had this exact
                // value (the common case, since the blade template now
                // also restores it server-side) would wipe out the area
                // chips restoreAreaChipsFromQuery() just finished adding.
                if (changed && field.tagName === 'SELECT' && window.jQuery) {
                    window.jQuery(field).trigger('change');
                }
            });
            hasFilters = true;
        });

        if (!hasFilters) {
            return false;
        }

        tabs.forEach(function (tab) {
            tab.classList.toggle('active', !!typeField && tab.getAttribute('rel') === typeField.value);
        });

        var categoryId = query.get('category_id');
        var categoryLabel = categoryId &&
            form.querySelector('.p-category[data-id="' + categoryId + '"], .category-li-item[data-id="' + categoryId + '"]');
        if (categoryLabel) {
            form.querySelectorAll('.category_id_text').forEach(function (el) {
                el.textContent = categoryLabel.textContent.trim();
            });
        }

        var textMirrors = {
            min_price: '.min_price_text',
            max_price: '.max_price_text',
            min_unit: '.min_unit_text',
            max_unit: '.max_unit_text'
        };
        Object.keys(textMirrors).forEach(function (key) {
            var value = query.get(key);
            if (value) {
                form.querySelectorAll(textMirrors[key]).forEach(function (el) {
                    el.textContent = value;
                });
            }
        });

        return true;
    }

    var initialQuery = new URLSearchParams(window.location.search);
    var hasQueryFilters = false;
    initialQuery.forEach(function (value, key) {
        if (value && searchForm && searchForm.querySelector('[name="' + key + '"]')) {
            hasQueryFilters = true;
        }
    });

    if (!hasQueryFilters) {
        // Initial, unfiltered load (reuses the server's location-default fallback).
        fetchAndRenderPins({ type: 'mapsearch', per_page: 300 });
    } else if (searchForm) {
        if (resultsGrid) {
            resultsGrid.innerHTML = '<p class="properties-highlights__empty">Searching...</p>';
        }

        // scripts.js's "Property Type" popover init (loaded further down the
        // page) unconditionally resets the category field/label to the first
        // category on every load, inside a jQuery $(document).ready callback
        // - which, being deferred, runs after this plain script regardless of
        // tag order, clobbering any category restored here synchronously.
        // `load` reliably fires after that deferred callback has already run.
        window.addEventListener('load', function () {
            populateFormFromQuery(searchForm);

            // homechoosen.js's own restoreAreaChipsFromQuery() (its city-areas
            // ajax call is sent with jQuery's async:false) rebuilds the area
            // chips' hidden "keyword[]" inputs from this same query string -
            // in practice that doesn't reliably finish before `load` fires,
            // so fetching immediately here can race it and search without
            // the area filter the URL actually asked for. Poll briefly for
            // those hidden inputs to show up before searching; give up and
            // search without them rather than hang if something's wrong.
            var expectedAreaIds = initialQuery.getAll('keyword[]');
            var waited = 0;

            (function waitForAreaChipsThenFetch() {
                var restored = expectedAreaIds.every(function (id) {
                    return !!searchForm.querySelector('input[name="keyword[]"][value="' + id + '"]');
                });

                if (restored || waited >= 2000) {
                    fetchAndRenderPins(buildSearchParams(searchForm));
                    return;
                }

                waited += 100;
                setTimeout(waitForAreaChipsThenFetch, 100);
            })();
        });
    }

    // Raw (un-remapped) form values, matching exactly the query-string shape
    // populateFormFromQuery() above reads back - buildSearchParams()'s output
    // can't be reused here as-is: it's remapped/padded for the ajax endpoint
    // (min_square/max_square/unit, property_type, type=mapsearch, per_page),
    // which would both look wrong in the address bar and fail to round-trip
    // through populateFormFromQuery on a later reload.
    function buildRawQueryParams(form) {
        var formData = new FormData(form);
        var params = new URLSearchParams();

        formData.forEach(function (value, key) {
            if (value === '') {
                return;
            }
            // "keyword[]" (area chips) legitimately repeats once per chip -
            // .set() would keep only the last one. Every other field only
            // ever repeats as an exact duplicate of itself (the same price/
            // area inputs appear in more than one category-dependent block
            // of the form), where de-duping via .set() is what keeps the
            // URL readable.
            if (key.slice(-2) === '[]') {
                params.append(key, value);
            } else {
                params.set(key, value);
            }
        });

        return params;
    }

    if (searchForm) {
        searchForm.addEventListener('submit', function (event) {
            var activeTab = searchForm.querySelector('.hero-search-card__tab.active');
            if (activeTab && activeTab.getAttribute('rel') === 'project') {
                return; // let it navigate normally to public.projects
            }
            event.preventDefault();

            // Keep the address bar in sync with whatever was just searched,
            // so reloading (or sharing/bookmarking the URL) reproduces the
            // same results instead of whatever was in it on first arrival.
            var rawParams = buildRawQueryParams(searchForm);
            var newUrl = window.location.pathname + (rawParams.toString() ? '?' + rawParams.toString() : '');
            history.replaceState(null, '', newUrl);

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
