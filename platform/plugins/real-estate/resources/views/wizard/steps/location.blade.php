@php
    $p = $property;
    $selectedState = $p->state_id ? \Botble\Location\Models\State::find($p->state_id) : null;
    $selectedFeatures = $p->features->pluck('id')->all();
    $selectedFacilities = $p->facilities->map(function ($facility) {
        return ['id' => $facility->id, 'distance' => $facility->pivot->distance];
    })->all();
@endphp

<div class="wizard-panel">
    <h2 class="wizard-panel__heading">{{ __('Location & Details') }}</h2>
    <p class="wizard-panel__description">{{ __('Where is the property, and what amenities does it offer?') }}</p>

    <form data-step-form data-collect-facilities action="{{ $stepUrls['location'] }}" method="post">
        <div class="wizard-field-grid">
            <div class="wizard-field">
                <label>{{ __('Country') }}</label>
                <div class="wizard-combobox" data-combobox="country" data-combobox-options="{{ $countries->map(function ($c) { return ['id' => $c->id, 'name' => $c->name]; })->toJson() }}">
                    <input type="text" class="wizard-input" data-combobox-input autocomplete="off" placeholder="{{ __('Search country...') }}" value="{{ optional($countries->firstWhere('id', $p->country_id))->name }}">
                    <input type="hidden" data-field="country_id" data-combobox-value value="{{ $p->country_id }}">
                    <div class="wizard-combobox__menu" data-combobox-menu></div>
                </div>
                <div class="wizard-error" data-error-for="country_id"></div>
            </div>

            <div class="wizard-field">
                <label>{{ __('State') }}</label>
                <div class="wizard-combobox" data-combobox="state">
                    <input type="text" class="wizard-input" data-combobox-input autocomplete="off" placeholder="{{ __('Search state...') }}" value="{{ optional($selectedState)->name }}">
                    <input type="hidden" data-field="state_id" data-combobox-value value="{{ $p->state_id }}">
                    <div class="wizard-combobox__menu" data-combobox-menu></div>
                </div>
                <div class="wizard-error" data-error-for="state_id"></div>
            </div>

            <div class="wizard-field">
                <label>{{ __('City') }}</label>
                <div class="wizard-combobox" data-combobox="city">
                    <input type="text" class="wizard-input" data-combobox-input autocomplete="off" placeholder="{{ __('Search city...') }}" value="{{ optional($p->city)->name }}">
                    <input type="hidden" data-field="city_id" data-combobox-value value="{{ $p->city_id }}">
                    <div class="wizard-combobox__menu" data-combobox-menu></div>
                </div>
                <div class="wizard-error" data-error-for="city_id"></div>
            </div>

            <div class="wizard-field">
                <label>{{ __('City Area') }}</label>
                <div class="wizard-combobox" data-combobox="city_area">
                    <input type="text" class="wizard-input" data-combobox-input autocomplete="off" placeholder="{{ __('Search city area...') }}" value="{{ optional($p->cityArea)->city_area_name }}">
                    <input type="hidden" data-field="city_area_id" data-combobox-value value="{{ $p->city_area_id }}">
                    <div class="wizard-combobox__menu" data-combobox-menu></div>
                </div>
                <div class="wizard-error" data-error-for="city_area_id"></div>
            </div>

            <div class="wizard-field wizard-field--span2">
                <label>{{ __('Address') }}</label>
                <input type="text" class="wizard-input" data-field="location" id="wizard-location-input" value="{{ $p->location }}" placeholder="{{ __('Search for an address...') }}">
                <div class="wizard-error" data-error-for="location"></div>
            </div>

            <div class="wizard-field wizard-field--span2">
                <div class="wizard-map-notice" id="wizard-map-notice" style="display:none;"></div>
                <div id="wizard-map" style="width:100%;height:320px;border-radius:12px;overflow:hidden;border:1px solid var(--pw-border);"></div>
                <span class="wizard-hint">{{ __('Drag the pin, click the map, or search above to fine-tune the exact location.') }}</span>
                <input type="hidden" data-field="latitude" id="wizard-latitude" value="{{ $p->latitude }}">
                <input type="hidden" data-field="longitude" id="wizard-longitude" value="{{ $p->longitude }}">
            </div>
        </div>

        <h3 style="margin-top:32px;margin-bottom:14px;font-size:16px;">{{ __('Features') }}</h3>
        <div class="wizard-checkbox-grid">
            @foreach ($features as $feature)
                <label class="wizard-checkbox">
                    <input type="checkbox" data-field="features" data-multiple="true" value="{{ $feature->id }}" {{ in_array($feature->id, $selectedFeatures) ? 'checked' : '' }}>
                    {{ $feature->name }}
                </label>
            @endforeach
        </div>

        <h3 style="margin-top:32px;margin-bottom:6px;font-size:16px;">{{ __('Nearby Facilities') }}</h3>
        <p class="wizard-hint" style="margin-bottom:14px;">{{ __('Add any facilities near this property and how far away they are.') }}</p>
        <div data-facility-rows>
            @forelse ($selectedFacilities as $facility)
                <div class="wizard-facility-row" data-facility-row>
                    <select class="wizard-select" data-facility-id>
                        <option value="">{{ __('Select facility') }}</option>
                        @foreach ($facilities as $option)
                            <option value="{{ $option->id }}" {{ ($facility['id'] == $option->id) ? 'selected' : '' }}>{{ $option->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" class="wizard-input" data-facility-distance value="{{ $facility['distance'] }}" placeholder="{{ __('e.g. 2km') }}">
                    <button type="button" class="wizard-btn wizard-btn--danger" data-facility-remove>{{ __('Remove') }}</button>
                </div>
            @empty
            @endforelse
        </div>
        <button type="button" class="wizard-btn wizard-btn--ghost" data-facility-add>
            <i class="fas fa-plus"></i> {{ __('Add Nearby Facility') }}
        </button>
        <template data-facility-template>
            <div class="wizard-facility-row" data-facility-row>
                <select class="wizard-select" data-facility-id>
                    <option value="">{{ __('Select facility') }}</option>
                    @foreach ($facilities as $option)
                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                    @endforeach
                </select>
                <input type="text" class="wizard-input" data-facility-distance placeholder="{{ __('e.g. 2km') }}">
                <button type="button" class="wizard-btn wizard-btn--danger" data-facility-remove>{{ __('Remove') }}</button>
            </div>
        </template>

        <div class="wizard-panel__actions">
            <a href="{{ $showBaseUrl }}?step=1" class="wizard-btn wizard-btn--ghost"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>
            <button type="submit" class="wizard-btn wizard-btn--primary" data-step-submit data-loading-text="{{ __('Saving...') }}">
                {{ __('Save & Continue') }} <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ setting('google_map_api_key') }}&libraries=places"></script>
<script>
(function () {
    // Minimal dependency-free searchable dropdown: a text input + hidden
    // value input + a filtered results menu, backed by a plain in-memory
    // options array. Used for country/state/city/city-area since a native
    // <select> with 250 countries is unusable, and the site doesn't
    // consistently load Select2's JS across all 4 wizard layouts.
    function createCombobox(container) {
        var input = container.querySelector('[data-combobox-input]');
        var hidden = container.querySelector('[data-combobox-value]');
        var menu = container.querySelector('[data-combobox-menu]');
        var options = [];
        var selectedLabel = input.value || '';

        try {
            var preset = container.getAttribute('data-combobox-options');
            if (preset) {
                options = JSON.parse(preset);
            }
        } catch (e) {
            options = [];
        }

        function close() {
            menu.classList.remove('wizard-combobox__menu--open');
            menu.innerHTML = '';
        }

        function render(filterText) {
            var term = (filterText || '').trim().toLowerCase();
            var matches = term
                ? options.filter(function (o) { return o.name.toLowerCase().indexOf(term) !== -1; })
                : options;

            menu.innerHTML = '';

            if (matches.length === 0) {
                var empty = document.createElement('div');
                empty.className = 'wizard-combobox__empty';
                empty.textContent = options.length === 0 ? '{{ __('Select the field above first') }}' : '{{ __('No matches') }}';
                menu.appendChild(empty);
            } else {
                matches.slice(0, 100).forEach(function (option) {
                    var item = document.createElement('div');
                    item.className = 'wizard-combobox__option';
                    item.textContent = option.name;
                    item.setAttribute('data-combobox-option-id', option.id);
                    menu.appendChild(item);
                });
            }

            menu.classList.add('wizard-combobox__menu--open');
        }

        menu.addEventListener('mousedown', function (event) {
            // mousedown (not click) so this fires before the input's blur.
            var item = event.target.closest('[data-combobox-option-id]');
            if (!item) {
                return;
            }
            var id = item.getAttribute('data-combobox-option-id');
            var option = options.filter(function (o) { return String(o.id) === String(id); })[0];
            selectOption(id, option ? option.name : item.textContent);
        });

        input.addEventListener('focus', function () {
            render(input.value === selectedLabel ? '' : input.value);
        });

        input.addEventListener('input', function () {
            render(input.value);
        });

        input.addEventListener('blur', function () {
            setTimeout(function () {
                // Revert stray typed text that was never actually selected.
                if (input.value !== selectedLabel) {
                    input.value = selectedLabel;
                }
                close();
            }, 150);
        });

        function selectOption(id, name) {
            hidden.value = id;
            input.value = name;
            selectedLabel = name;
            close();
            hidden.dispatchEvent(new Event('change'));
        }

        return {
            setOptions: function (newOptions) {
                options = newOptions;
            },
            clear: function () {
                options = [];
                selectedLabel = '';
                hidden.value = '';
                input.value = '';
                hidden.dispatchEvent(new Event('change'));
            }
        };
    }

    // Deferred to 'load': the theme mounts a Vue instance on #app in a
    // footer script (components.js), which recompiles and replaces
    // everything inside it - including this whole form. Wiring anything up
    // earlier attaches listeners to nodes that Vue then discards, so
    // nothing below can run until that mount has already happened.
    window.addEventListener('load', function () {

    var comboboxes = {};
    document.querySelectorAll('[data-combobox]').forEach(function (el) {
        comboboxes[el.getAttribute('data-combobox')] = createCombobox(el);
    });

    var countryHidden = document.querySelector('[data-combobox="country"] [data-combobox-value]');
    var stateHidden = document.querySelector('[data-combobox="state"] [data-combobox-value]');
    var cityHidden = document.querySelector('[data-combobox="city"] [data-combobox-value]');
    var cityAreaHidden = document.querySelector('[data-combobox="city_area"] [data-combobox-value]');

    // Set by initMap() once Google Maps is ready, so picking a city/city
    // area can recenter the map even though that logic lives inside
    // initMap()'s own closure.
    var mapController = null;

    function recenterOnAreaSelection() {
        if (!mapController) {
            return;
        }
        var cityAreaLabel = document.querySelector('[data-combobox="city_area"] [data-combobox-input]').value;
        var cityLabel = document.querySelector('[data-combobox="city"] [data-combobox-input]').value;
        var address = [cityAreaLabel, cityLabel].filter(Boolean).join(', ');
        if (address) {
            mapController.recenterOnAddress(address);
        }
    }

    function fetchOptions(url, params, labelKey) {
        var query = new URLSearchParams(params).toString();
        return fetch(url + '?' + query, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                var items = json.data || json;
                return items.map(function (item) {
                    return { id: item.id, name: item[labelKey] };
                });
            });
    }

    function loadStates(countryId) {
        return fetchOptions('{{ route('ajax.states') }}', { country_id: countryId }, 'name')
            .then(function (options) { comboboxes.state.setOptions(options); });
    }

    function loadCities(stateId) {
        return fetchOptions('{{ route('ajax.property-cities') }}', { state_id: stateId }, 'name')
            .then(function (options) { comboboxes.city.setOptions(options); });
    }

    function loadCityAreas(cityId) {
        return fetchOptions('/ajax/get-city-areas', { city_id: cityId }, 'city_area_name')
            .then(function (options) { comboboxes.city_area.setOptions(options); });
    }

    countryHidden.addEventListener('change', function () {
        comboboxes.state.clear();
        comboboxes.city.clear();
        comboboxes.city_area.clear();
        if (this.value) {
            loadStates(this.value);
        }
    });

    stateHidden.addEventListener('change', function () {
        comboboxes.city.clear();
        comboboxes.city_area.clear();
        if (this.value) {
            loadCities(this.value);
        }
    });

    cityHidden.addEventListener('change', function () {
        comboboxes.city_area.clear();
        if (this.value) {
            loadCityAreas(this.value);
        }
    });

    cityAreaHidden.addEventListener('change', function () {
        if (this.value) {
            recenterOnAreaSelection();
        }
    });

    // On load (resuming a draft), pre-populate each level's search options
    // for its already-selected parent, without touching the selected value.
    if (countryHidden.value) {
        loadStates(countryHidden.value);
    }
    if (stateHidden.value) {
        loadCities(stateHidden.value);
    }
    if (cityHidden.value) {
        loadCityAreas(cityHidden.value);
    }

    function initMap() {
        var latInput = document.getElementById('wizard-latitude');
        var lngInput = document.getElementById('wizard-longitude');
        var locationInput = document.getElementById('wizard-location-input');
        var mapNotice = document.getElementById('wizard-map-notice');

        // Islamabad, Pakistan - used only if there's no saved position yet
        // and the browser's geolocation can't be used either.
        var fallbackLat = 33.6844;
        var fallbackLng = 73.0479;

        var hasSavedPosition = latInput.value !== '' && lngInput.value !== '';
        var startLat = hasSavedPosition ? parseFloat(latInput.value) : fallbackLat;
        var startLng = hasSavedPosition ? parseFloat(lngInput.value) : fallbackLng;

        var map = new google.maps.Map(document.getElementById('wizard-map'), {
            zoom: 14,
            center: { lat: startLat, lng: startLng }
        });

        // Google's default marker is already a red pin.
        var marker = new google.maps.Marker({
            position: { lat: startLat, lng: startLng },
            map: map,
            draggable: true
        });

        // Maps sometimes measures #wizard-map before the surrounding
        // wizard layout has taken its final width (e.g. while fonts/icons
        // are still settling), leaving the map stuck rendered at a sliver
        // of its real size. Nudging it once on the next frame forces a
        // remeasure without a visible flash, and re-centering after undoes
        // the recentring `resize` itself triggers.
        requestAnimationFrame(function () {
            google.maps.event.trigger(map, 'resize');
            map.setCenter(marker.getPosition());
        });

        // Visualizes the property's general vicinity - a fixed 1km radius
        // around whatever point is currently selected, since city areas
        // don't carry their own boundary/radius data to draw from instead.
        var areaRadiusMeters = 1000;
        var areaCircle = new google.maps.Circle({
            map: map,
            center: { lat: startLat, lng: startLng },
            radius: areaRadiusMeters,
            strokeColor: '#4285F4',
            strokeOpacity: 0.8,
            strokeWeight: 1,
            fillColor: '#4285F4',
            fillOpacity: 0.12,
            clickable: false
        });

        function moveAreaCircleTo(pos) {
            areaCircle.setCenter(pos);
        }

        var geocoder = new google.maps.Geocoder();

        function showNotice(message) {
            if (!mapNotice) {
                return;
            }
            mapNotice.textContent = message;
            mapNotice.style.display = 'block';
        }

        function hideNotice() {
            if (mapNotice) {
                mapNotice.style.display = 'none';
            }
        }

        function reverseGeocode(latLng) {
            geocoder.geocode({ location: latLng }, function (results, status) {
                if (status === 'OK' && results[0]) {
                    locationInput.value = results[0].formatted_address;
                }
            });
        }

        // Lets picking a City / City Area above recenter the map, even
        // though city areas don't carry their own coordinates to jump to
        // directly - geocoding "<area>, <city>" as a search string is the
        // same fallback the admin's agent-coverage map already uses for
        // this same gap.
        mapController = {
            recenterOnAddress: function (address) {
                geocoder.geocode({ address: address }, function (results, status) {
                    if (status === 'OK' && results[0]) {
                        var loc = results[0].geometry.location;
                        placeMarkerAt(loc.lat(), loc.lng(), false);
                        locationInput.value = results[0].formatted_address;
                    }
                });
            }
        };

        function placeMarkerAt(lat, lng, shouldReverseGeocode) {
            var pos = { lat: lat, lng: lng };
            map.setCenter(pos);
            map.setZoom(15);
            marker.setPosition(pos);
            moveAreaCircleTo(pos);
            latInput.value = lat;
            lngInput.value = lng;
            if (shouldReverseGeocode) {
                reverseGeocode(pos);
            }
        }

        marker.addListener('dragend', function () {
            var pos = marker.getPosition();
            moveAreaCircleTo(pos);
            latInput.value = pos.lat();
            lngInput.value = pos.lng();
            reverseGeocode(pos);
        });

        map.addListener('click', function (event) {
            marker.setPosition(event.latLng);
            moveAreaCircleTo(event.latLng);
            latInput.value = event.latLng.lat();
            lngInput.value = event.latLng.lng();
            reverseGeocode(event.latLng);
        });

        var searchBox = new google.maps.places.SearchBox(locationInput);
        searchBox.addListener('places_changed', function () {
            var places = searchBox.getPlaces();
            if (!places.length || !places[0].geometry) return;
            hideNotice();
            var loc = places[0].geometry.location;
            map.setCenter(loc);
            marker.setPosition(loc);
            moveAreaCircleTo(loc);
            latInput.value = loc.lat();
            lngInput.value = loc.lng();
        });

        // Only auto-detect the visitor's current position for a brand new
        // draft - never override a location that was already picked/saved.
        if (hasSavedPosition) {
            return;
        }

        if (!navigator.geolocation) {
            showNotice('{{ __('Your browser doesn\'t support location detection. Search for an address above or click the map to set your location.') }}');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                hideNotice();
                placeMarkerAt(position.coords.latitude, position.coords.longitude, true);
            },
            function (error) {
                if (error.code === error.PERMISSION_DENIED) {
                    showNotice('{{ __('Location access is turned off. Please enable location permissions for this site in your browser settings so we can pinpoint your property automatically, or search for an address above / click the map to set it manually.') }}');
                } else {
                    showNotice('{{ __('Could not detect your current location. Search for an address above or click the map to set your location.') }}');
                }
            },
            { timeout: 8000 }
        );
    }

    // The Maps script tag above is a plain, blocking <script src> that runs
    // well before 'load' fires, so google.maps is already available here.
    initMap();

    }); // end window 'load' listener
})();
</script>
