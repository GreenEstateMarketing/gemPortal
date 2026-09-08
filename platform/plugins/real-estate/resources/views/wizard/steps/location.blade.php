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
        <p class="wizard-hint" style="margin-bottom:14px;">{{ __('Suggested automatically from the map location above - add the ones that apply, or add your own below.') }}</p>
        <div class="wizard-map-notice" id="wizard-facility-notice" style="display:none;"></div>
        <div class="wizard-facility-candidates" data-facility-candidates style="display:none;">
            <p class="wizard-hint" style="margin-bottom:8px;">{{ __('Suggested nearby facilities - click Add to include one.') }}</p>
            <div class="wizard-facility-candidates__list" data-facility-candidates-list></div>
        </div>
        <div data-facility-rows data-facilities="{{ $facilities->map(function ($f) { return ['id' => $f->id, 'name' => $f->name, 'icon' => $f->icon, 'google_place_type' => $f->google_place_type]; })->toJson() }}">
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
    // Our own Facility list (Airport, Bank, School...) has no location data
    // of its own - it's just a generic category list. To actually find
    // real nearby places we ask Google Places (already loaded for the
    // address search box above) for the closest place of each facility
    // that has a mapped google_place_type (set per-facility in the admin),
    // then compute the distance ourselves. Facilities with no mapped type
    // are left out of auto-detection - they're still addable manually below.
    function buildFacilityTypeMap() {
        var container = document.querySelector('[data-facility-rows]');
        var map = {};
        if (!container) {
            return map;
        }
        try {
            JSON.parse(container.getAttribute('data-facilities') || '[]').forEach(function (f) {
                if (f.google_place_type) {
                    // Last one wins if two facilities share a type.
                    map[f.google_place_type] = { id: f.id, name: f.name, icon: f.icon };
                }
            });
        } catch (e) {
            return {};
        }
        return map;
    }

    function haversineMeters(lat1, lng1, lat2, lng2) {
        var R = 6371000;
        var toRad = function (d) { return d * Math.PI / 180; };
        var dLat = toRad(lat2 - lat1);
        var dLng = toRad(lng2 - lng1);
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function formatDistance(meters) {
        return meters < 1000 ? Math.round(meters) + 'm' : (meters / 1000).toFixed(1) + 'km';
    }

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

    var comboboxes = {};
    document.querySelectorAll('[data-combobox]').forEach(function (el) {
        comboboxes[el.getAttribute('data-combobox')] = createCombobox(el);
    });

    var countryHidden = document.querySelector('[data-combobox="country"] [data-combobox-value]');
    var stateHidden = document.querySelector('[data-combobox="state"] [data-combobox-value]');
    var cityHidden = document.querySelector('[data-combobox="city"] [data-combobox-value]');

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

        var geocoder = new google.maps.Geocoder();
        var placesService = new google.maps.places.PlacesService(map);

        function addOrUpdateFacilityRow(facilityId, distanceLabel) {
            var container = document.querySelector('[data-facility-rows]');
            var template = document.querySelector('[data-facility-template]');
            if (!container || !template) {
                return;
            }

            var rowSelects = Array.prototype.slice.call(container.querySelectorAll('[data-facility-id]'));

            // Already picked (manually, or from an earlier detection) -
            // leave whatever distance is there alone.
            if (rowSelects.some(function (select) { return select.value === String(facilityId); })) {
                return;
            }

            var emptySelect = rowSelects.filter(function (select) { return !select.value; })[0];
            var row = emptySelect ? emptySelect.closest('[data-facility-row]') : null;

            if (!row) {
                container.appendChild(template.content.cloneNode(true));
                var rows = container.querySelectorAll('[data-facility-row]');
                row = rows[rows.length - 1];
            }

            var select = row.querySelector('[data-facility-id]');
            var distanceInput = row.querySelector('[data-facility-distance]');
            select.value = facilityId;
            distanceInput.value = distanceLabel;
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function getSelectedFacilityIds() {
            var container = document.querySelector('[data-facility-rows]');
            if (!container) {
                return [];
            }
            return Array.prototype.slice.call(container.querySelectorAll('[data-facility-id]'))
                .map(function (select) { return select.value; })
                .filter(function (value) { return !!value; });
        }

        // Full set of the last detection pass's matches, keyed by facility
        // id - kept around (not deleted on Add) so that removing a selected
        // facility can bring it back as a candidate without re-querying
        // Places. The candidate list actually shown is this set minus
        // whatever's currently selected, computed fresh on every render.
        var allDetected = {};

        function renderCandidates() {
            var wrapper = document.querySelector('[data-facility-candidates]');
            var list = document.querySelector('[data-facility-candidates-list]');
            if (!wrapper || !list) {
                return;
            }

            var selectedIds = getSelectedFacilityIds();
            var visibleIds = Object.keys(allDetected).filter(function (id) {
                return selectedIds.indexOf(id) === -1;
            });

            list.innerHTML = '';

            visibleIds.forEach(function (id) {
                var candidate = allDetected[id];
                var chip = document.createElement('div');
                chip.className = 'wizard-facility-candidate';
                chip.setAttribute('data-candidate-facility-id', id);

                if (candidate.icon) {
                    var icon = document.createElement('i');
                    icon.className = candidate.icon;
                    chip.appendChild(icon);
                }

                var label = document.createElement('span');
                label.textContent = candidate.name;
                chip.appendChild(label);

                var distance = document.createElement('span');
                distance.className = 'wizard-facility-candidate__distance';
                distance.textContent = '— ' + candidate.distanceLabel;
                chip.appendChild(distance);

                var addBtn = document.createElement('button');
                addBtn.type = 'button';
                addBtn.className = 'wizard-btn wizard-btn--ghost';
                addBtn.setAttribute('data-candidate-add', '');
                addBtn.textContent = '{{ __('Add') }}';
                chip.appendChild(addBtn);

                list.appendChild(chip);
            });

            wrapper.style.display = visibleIds.length ? 'block' : 'none';
        }

        function detectNearbyFacilities(lat, lng) {
            if (!isFinite(lat) || !isFinite(lng)) {
                return;
            }

            var notice = document.getElementById('wizard-facility-notice');
            var typeMap = buildFacilityTypeMap();
            var placeTypes = Object.keys(typeMap);

            allDetected = {};
            renderCandidates();

            if (!placeTypes.length) {
                return;
            }

            if (notice) {
                notice.textContent = '{{ __('Looking for nearby facilities...') }}';
                notice.style.display = 'block';
            }

            var pending = placeTypes.length;
            var hadApiError = false;

            function done() {
                pending -= 1;
                if (pending > 0) {
                    return;
                }

                renderCandidates();

                if (!notice) {
                    return;
                }

                if (hadApiError) {
                    notice.textContent = '{{ __('Couldn\'t check for nearby facilities right now. You can still add them manually below.') }}';
                    notice.style.display = 'block';
                } else if (!Object.keys(allDetected).length) {
                    notice.textContent = '{{ __('No matching facilities found nearby.') }}';
                    notice.style.display = 'block';
                } else {
                    notice.style.display = 'none';
                }
            }

            // radius and rankBy:DISTANCE are mutually exclusive on the
            // Places API, and we need a hard 10km cap, so we search a fixed
            // radius (ranked by prominence) and pick the true nearest result
            // ourselves via haversine distance across the returned set.
            placeTypes.forEach(function (placeType) {
                var facility = typeMap[placeType];
                placesService.nearbySearch({
                    location: { lat: lat, lng: lng },
                    radius: 10000,
                    type: placeType
                }, function (results, status) {
                    if (status === google.maps.places.PlacesServiceStatus.OK && results && results.length) {
                        var nearestMeters = null;
                        results.forEach(function (result) {
                            if (!result.geometry || !result.geometry.location) {
                                return;
                            }
                            var placeLoc = result.geometry.location;
                            var distanceMeters = haversineMeters(lat, lng, placeLoc.lat(), placeLoc.lng());
                            if (nearestMeters === null || distanceMeters < nearestMeters) {
                                nearestMeters = distanceMeters;
                            }
                        });

                        if (nearestMeters !== null && nearestMeters <= 10000) {
                            allDetected[facility.id] = {
                                name: facility.name,
                                icon: facility.icon,
                                distanceLabel: formatDistance(nearestMeters)
                            };
                        }
                    } else if (status !== google.maps.places.PlacesServiceStatus.ZERO_RESULTS) {
                        hadApiError = true;
                    }
                    done();
                });
            });
        }

        var candidatesList = document.querySelector('[data-facility-candidates-list]');
        if (candidatesList) {
            candidatesList.addEventListener('click', function (event) {
                if (!event.target.matches('[data-candidate-add]')) {
                    return;
                }
                var chip = event.target.closest('[data-candidate-facility-id]');
                if (!chip) {
                    return;
                }
                var id = chip.getAttribute('data-candidate-facility-id');
                var candidate = allDetected[id];
                if (!candidate) {
                    return;
                }
                addOrUpdateFacilityRow(id, candidate.distanceLabel);
                renderCandidates();
            });
        }

        // A facility freed up by removing/changing a row elsewhere (handled
        // in property-wizard.js's syncFacilityOptions) should be able to
        // reappear here without a new Places lookup.
        document.addEventListener('wizard:facility-rows-changed', renderCandidates);

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

        function placeMarkerAt(lat, lng, shouldReverseGeocode) {
            var pos = { lat: lat, lng: lng };
            map.setCenter(pos);
            map.setZoom(15);
            marker.setPosition(pos);
            latInput.value = lat;
            lngInput.value = lng;
            if (shouldReverseGeocode) {
                reverseGeocode(pos);
            }
            detectNearbyFacilities(lat, lng);
        }

        marker.addListener('dragend', function () {
            var pos = marker.getPosition();
            latInput.value = pos.lat();
            lngInput.value = pos.lng();
            reverseGeocode(pos);
            detectNearbyFacilities(pos.lat(), pos.lng());
        });

        map.addListener('click', function (event) {
            marker.setPosition(event.latLng);
            latInput.value = event.latLng.lat();
            lngInput.value = event.latLng.lng();
            reverseGeocode(event.latLng);
            detectNearbyFacilities(event.latLng.lat(), event.latLng.lng());
        });

        var searchBox = new google.maps.places.SearchBox(locationInput);
        searchBox.addListener('places_changed', function () {
            var places = searchBox.getPlaces();
            if (!places.length || !places[0].geometry) return;
            hideNotice();
            var loc = places[0].geometry.location;
            map.setCenter(loc);
            marker.setPosition(loc);
            latInput.value = loc.lat();
            lngInput.value = loc.lng();
            detectNearbyFacilities(loc.lat(), loc.lng());
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

    if (window.google && window.google.maps) {
        initMap();
    } else {
        window.addEventListener('load', initMap);
    }
})();
</script>
