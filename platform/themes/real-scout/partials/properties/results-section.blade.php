{{--
    Live search results grid - sits between the search bar and the map, per
    request. Populated entirely client-side by js/new-home-page/properties-map.js,
    reusing the exact same AJAX response (public.ajax.properties, mapsearch)
    already being fetched for the map pins - no separate request, no
    server-rendered properties here. Card markup/classes match
    properties/highlights-section.blade.php's cards (properties-highlights.css).
--}}
<section class="properties-highlights properties-highlights--results">
    <div class="properties-highlights__inner">
        <h2 class="properties-highlights__heading">{{ __('Search Results') }}</h2>
        <p class="properties-highlights__text">{{ __('Properties matching your search.') }}</p>

        <div id="properties-search-results" class="properties-highlights__grid">
            <p class="properties-highlights__empty">{{ __('Loading properties...') }}</p>
        </div>

        {{-- Populated (and shown) by properties-map.js only when a search
             comes back with zero results - it fills this in with the same
             search progressively broadened (area removed, then city too)
             by FlexHomeController::ajaxGetProperties, so a too-narrow
             search isn't a dead end. --}}
        <div id="properties-search-suggestions" class="properties-highlights__suggestions" style="display:none">
            <h3 class="properties-highlights__suggestions-heading">{{ __('You might also be interested in') }}</h3>
            <p class="properties-highlights__suggestions-text"></p>
            <div class="properties-highlights__grid"></div>
        </div>
    </div>
</section>
