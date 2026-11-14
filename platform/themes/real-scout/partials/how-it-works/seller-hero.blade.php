{{--
    Path in theme:  platform/themes/real-scout/partials/how-it-works/seller-hero.blade.php
    Rendered via:   views/real-estate/how-it-works-seller.blade.php, route "public.how-it-works-seller".

    Same two-column hero structure as buyer-hero.blade.php, but a single
    H1 (no separate subheading line) per the seller mockup. Reuses the
    SAME photo as the buyer hero (images/how-it-works/buyer_hero_luxury_house.png)
    - no distinct seller hero photo was provided, and the mockup's hero
    photo is visually the same luxury-house style image anyway.
--}}
<section class="how-works-hero">
    <div class="how-works-hero__inner">
        <div class="how-works-hero__text">
            <span class="how-works-hero__pill">{{ __('For Sellers') }}</span>
            <h1 class="how-works-hero__heading">{{ __('List Your Property with Confidence.') }}</h1>
            <p class="how-works-hero__text-body">
                {{ __('A simple 6-step process to get your property listed and seen by the right buyers.') }}
            </p>
            <span class="how-works-hero__accent"></span>
        </div>
        <div class="how-works-hero__media">
            <img src="{{ Theme::asset()->url('images/how-it-works/buyer_hero_luxury_house.png') }}" alt="{{ __('Modern luxury home') }}" class="how-works-hero__image" />
        </div>
    </div>
</section>
