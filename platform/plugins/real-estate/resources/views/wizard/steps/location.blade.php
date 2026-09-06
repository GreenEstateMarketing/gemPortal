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
                <select class="wizard-select" data-field="country_id" id="wizard-country">
                    <option value="">{{ __('Select country') }}</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" {{ ($p->country_id == $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
                <div class="wizard-error" data-error-for="country_id"></div>
            </div>

            <div class="wizard-field">
                <label>{{ __('State') }}</label>
                <select class="wizard-select" data-field="state_id" id="wizard-state">
                    @if ($selectedState)
                        <option value="{{ $selectedState->id }}" selected>{{ $selectedState->name }}</option>
                    @else
                        <option value="">{{ __('Select state') }}</option>
                    @endif
                </select>
                <div class="wizard-error" data-error-for="state_id"></div>
            </div>

            <div class="wizard-field">
                <label>{{ __('City') }}</label>
                <select class="wizard-select" data-field="city_id" id="wizard-city">
                    @if ($p->city)
                        <option value="{{ $p->city->id }}" selected>{{ $p->city->name }}</option>
                    @else
                        <option value="">{{ __('Select city') }}</option>
                    @endif
                </select>
                <div class="wizard-error" data-error-for="city_id"></div>
            </div>

            <div class="wizard-field">
                <label>{{ __('City Area') }}</label>
                <select class="wizard-select" data-field="city_area_id" id="wizard-city-area">
                    @if ($p->cityArea)
                        <option value="{{ $p->cityArea->id }}" selected>{{ $p->cityArea->name }}</option>
                    @else
                        <option value="">{{ __('Select city area') }}</option>
                    @endif
                </select>
                <div class="wizard-error" data-error-for="city_area_id"></div>
            </div>

            <div class="wizard-field wizard-field--span2">
                <label>{{ __('Address') }}</label>
                <input type="text" class="wizard-input" data-field="location" id="wizard-location-input" value="{{ $p->location }}" placeholder="{{ __('Search for an address...') }}">
                <div class="wizard-error" data-error-for="location"></div>
            </div>

            <div class="wizard-field wizard-field--span2">
                <div id="wizard-map" style="width:100%;height:320px;border-radius:12px;overflow:hidden;border:1px solid var(--pw-border);"></div>
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

        <h3 style="margin-top:32px;margin-bottom:14px;font-size:16px;">{{ __('Nearby Facilities') }}</h3>
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
    var countrySelect = document.getElementById('wizard-country');
    var stateSelect = document.getElementById('wizard-state');
    var citySelect = document.getElementById('wizard-city');
    var cityAreaSelect = document.getElementById('wizard-city-area');

    function fetchInto(url, params, select, placeholder, valueKey, labelKey) {
        var query = new URLSearchParams(params).toString();
        fetch(url + '?' + query, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                var items = json.data || json;
                select.innerHTML = '<option value="">' + placeholder + '</option>';
                items.forEach(function (item) {
                    var opt = document.createElement('option');
                    opt.value = item[valueKey];
                    opt.textContent = item[labelKey];
                    select.appendChild(opt);
                });
            });
    }

    countrySelect.addEventListener('change', function () {
        if (!this.value) return;
        fetchInto('{{ route('ajax.states') }}', { country_id: this.value }, stateSelect, '{{ __('Select state') }}', 'id', 'name');
        cityAreaSelect.innerHTML = '<option value="">{{ __('Select city area') }}</option>';
    });

    stateSelect.addEventListener('change', function () {
        if (!this.value) return;
        fetchInto('{{ route('ajax.property-cities') }}', { state_id: this.value }, citySelect, '{{ __('Select city') }}', 'id', 'name');
    });

    citySelect.addEventListener('change', function () {
        if (!this.value) return;
        fetchInto('/ajax/get-city-areas', { city_id: this.value }, cityAreaSelect, '{{ __('Select city area') }}', 'id', 'city_area_name');
    });

    function initMap() {
        var latInput = document.getElementById('wizard-latitude');
        var lngInput = document.getElementById('wizard-longitude');
        var locationInput = document.getElementById('wizard-location-input');

        var startLat = parseFloat(latInput.value) || 25.276987;
        var startLng = parseFloat(lngInput.value) || 55.296249;

        var map = new google.maps.Map(document.getElementById('wizard-map'), {
            zoom: 14,
            center: { lat: startLat, lng: startLng }
        });

        var marker = new google.maps.Marker({
            position: { lat: startLat, lng: startLng },
            map: map,
            draggable: true
        });

        var geocoder = new google.maps.Geocoder();

        function reverseGeocode(latLng) {
            geocoder.geocode({ location: latLng }, function (results, status) {
                if (status === 'OK' && results[0]) {
                    locationInput.value = results[0].formatted_address;
                }
            });
        }

        marker.addListener('dragend', function () {
            var pos = marker.getPosition();
            latInput.value = pos.lat();
            lngInput.value = pos.lng();
            reverseGeocode(pos);
        });

        map.addListener('click', function (event) {
            marker.setPosition(event.latLng);
            latInput.value = event.latLng.lat();
            lngInput.value = event.latLng.lng();
            reverseGeocode(event.latLng);
        });

        var searchBox = new google.maps.places.SearchBox(locationInput);
        searchBox.addListener('places_changed', function () {
            var places = searchBox.getPlaces();
            if (!places.length || !places[0].geometry) return;
            var loc = places[0].geometry.location;
            map.setCenter(loc);
            marker.setPosition(loc);
            latInput.value = loc.lat();
            lngInput.value = loc.lng();
        });
    }

    if (window.google && window.google.maps) {
        initMap();
    } else {
        window.addEventListener('load', initMap);
    }
})();
</script>
