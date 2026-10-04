{{--
    "Find us on the map" - plain Google Maps iframe embed centered on the
    spotlight project's lat/lng. Reuses the exact embed markup already used
    by views/real-estate/project.blade.php's own map block (no API key, no
    Leaflet - simpler than /properties' map since this shows a single pin,
    not a filterable multi-pin search map).
--}}
@if ($spotlightProject && $spotlightProject->latitude && $spotlightProject->longitude)
    <section class="projects-map-section">
        <div class="projects-map-section__inner">
            <div class="projects-map-section__header">
                <div>
                    <span class="projects-map-section__eyebrow">{{ __('Location') }}</span>
                    <h2 class="projects-map-section__heading">{{ __('Find us on the map') }}</h2>
                </div>
                <p class="projects-map-section__text">{{ __('Prime location with easy access to nearby facilities.') }}</p>
            </div>
            <div class="projects-map-section__map">
                <iframe
                    width="100%" height="500"
                    src="https://maps.google.com/maps?q={{ $spotlightProject->latitude }},{{ $spotlightProject->longitude }}&t=&z=13&ie=UTF8&iwloc=&output=embed"
                    frameborder="0" scrolling="no" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
@endif
