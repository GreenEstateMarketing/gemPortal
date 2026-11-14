{{--
    Path in theme:  platform/themes/real-scout/partials/how-it-works/buyer-hero.blade.php
    Rendered via:   views/real-estate/how-it-works-buyer.blade.php, route "public.how-it-works-buyer".

    v2: two-column hero (text left, photo right) replacing v1's full-bleed
    photo+dark-overlay band. The image's left edge fades into the light
    background via a CSS mask instead of sitting under a dark overlay -
    text reads directly against the page background, not against the photo.
--}}
<section class="how-works-hero">
    <div class="how-works-hero__inner">
        <div class="how-works-hero__text">
            <span class="how-works-hero__pill">{{ __('For Buyers') }}</span>
            <h1 class="how-works-hero__heading">{{ __('Find. Explore. Buy.') }}</h1>
            <h2 class="how-works-hero__subheading">{{ __('Your Complete Buying Journey') }}</h2>
            <p class="how-works-hero__text-body">
                {{ __("Discover the perfect property with confidence. Here's how GEMlisting makes your buying process simple and secure.") }}
            </p>
            <span class="how-works-hero__accent"></span>
        </div>
        <div class="how-works-hero__media">
            <img src="{{ Theme::asset()->url('images/how-it-works/buyer_hero_luxury_house.png') }}" alt="{{ __('Modern luxury home') }}" class="how-works-hero__image" />
        </div>
    </div>
</section>
