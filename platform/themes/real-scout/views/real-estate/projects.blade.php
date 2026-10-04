{!! Theme::partial('projects/hero-section', compact('cities', 'categories', 'name', 'cityId', 'categoryId')) !!}
{!! Theme::partial('projects/grid-section', compact('projects', 'hasFilters')) !!}
{!! Theme::partial('projects/spotlight-section', compact('spotlightProject', 'contactUrl')) !!}
{!! Theme::partial('projects/map-section', compact('spotlightProject')) !!}
{!! Theme::partial('projects/cta-section', compact('contactUrl')) !!}
