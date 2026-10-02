{{--
    Map of published properties. Pins are fetched/rendered entirely
    client-side by js/new-home-page/properties-map.js (Leaflet), which
    also listens for the search-section form's submit to re-fetch filtered
    pins via the existing public.ajax.properties (mapsearch) endpoint - no
    server-rendered pin data here, so this partial needs no $properties.
--}}
<section class="properties-map-section">
    <div class="properties-map-section__inner">
        <div class="properties-map-section__head">
            <div>
                <span class="properties-map-section__eyebrow">{{ __('Explore Locations') }}</span>
                <h2 class="properties-map-section__heading">{{ __('Find Properties on the Map') }}</h2>
                <p class="properties-map-section__text">
                    {{ __('Explore available properties by location and discover homes near your preferred area.') }}
                </p>
            </div>
            <button type="button" id="propertiesMyLocationBtn" class="properties-map-section__location-btn">
                <i class="fas fa-location-crosshairs"></i> {{ __('My Location') }}
            </button>
        </div>

        <div class="properties-map">
            <div id="properties-map-canvas" class="properties-map-canvas"
                data-ajax-url="{{ route('public.ajax.properties') }}"></div>
            <div id="properties-map-count" class="properties-map__count-badge">0 {{ __('Properties Found') }}</div>
        </div>
    </div>
</section>
