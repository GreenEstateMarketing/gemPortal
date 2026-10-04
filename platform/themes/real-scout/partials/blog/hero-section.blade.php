{{--
    Photo hero for the redesigned /blog listing - same treatment as
    properties/projects hero sections (partials/properties/search-section.blade.php,
    partials/projects/hero-section.blade.php), reusing an existing theme stock
    image rather than a new upload. "Explore Articles" just scrolls down to
    the grid section (#blog-articles), no extra route needed.
--}}
<section class="blog-listing-hero">
    <div class="blog-listing-hero__overlay"></div>
    <div class="blog-listing-hero__inner">
        <span class="blog-listing-hero__eyebrow">{{ strtoupper(theme_option('seo_title', 'GEMlisting')) }} {{ __('Journal') }}</span>
        <h1 class="blog-listing-hero__heading">{{ __('Real Estate Insights') }}<br>{{ __('For Your Next Move') }}</h1>
        <p class="blog-listing-hero__text">
            {{ __('Explore property guides, market insights, investment ideas and practical advice designed to help you make informed real-estate decisions.') }}
        </p>
        <a href="#blog-articles" class="blog-listing-hero__btn">
            {{ __('Explore Articles') }} <i class="fas fa-arrow-down"></i>
        </a>
    </div>
</section>
