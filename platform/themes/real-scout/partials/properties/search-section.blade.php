{{--
    Photo hero matching the home page's hero (partials/home-page-new/header.blade.php)
    - same background image, same dark overlay treatment - sized down for an
    inner page (shorter, no marketing copy/CTA buttons) and wrapping the SAME
    search bar partial reused verbatim (no fork, see properties-search.css).
--}}
<section class="properties-hero">
    <div class="properties-hero__overlay"></div>
    <div class="properties-hero__inner">
        <span class="properties-hero__eyebrow">{{ __('Find Your Next Property') }}</span>
        <h1 class="properties-hero__heading">
            {{ __('Discover a place') }}<br>
            <span class="properties-hero__heading--accent">{{ __("you'll love to live.") }}</span>
        </h1>
        <p class="properties-hero__text">
            {{ __('Explore verified properties, trusted agents and premium real estate opportunities with GEMlisting.') }}
        </p>
        <div class="properties-hero__search">
            {!! Theme::partial('home-page-new/search-bar') !!}
        </div>
    </div>
</section>
