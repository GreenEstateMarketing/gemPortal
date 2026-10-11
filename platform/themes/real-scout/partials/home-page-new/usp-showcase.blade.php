{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/usp-showcase.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/usp-showcase') !!}
    Included from:  layouts/homepagenew.blade.php, between the header and why-choose partials

    This is the homepage's primary USP/brand section - dark, gold-accented,
    placed right after the main hero so it's the first thing every visitor
    sees. It is intentionally separate from the "Why Choose GEMlisting"
    section below it, even though the messaging is related.

    IMAGE: home-page-new/why-choose-living-room.png is reused here as a
    placeholder (the same image the "Why Choose" section already uses) so
    the section isn't broken while there's no dedicated photo yet. Swap the
    img src for the real asset and this partial needs no changes.
--}}
<section class="usp-showcase" id="usp-showcase">
    <div class="container usp-showcase__inner">

        <div class="usp-showcase__content">
            <span class="usp-showcase__badge">
                <span class="usp-showcase__badge-dot"></span>
                {{ __('The GEMlisting Experience') }}
            </span>

            <span class="usp-showcase__eyebrow">{{ __('Your Property. Your Future.') }}</span>

            <h2 class="usp-showcase__heading">
                {{ __('A Better Way to Find Your') }}<br>
                <span class="usp-showcase__heading--accent">{{ __('Place in Pakistan.') }}</span>
            </h2>

            <p class="usp-showcase__text">
                {{ __('From your dream home to your next investment, discover property opportunities with a smarter, simpler and more confident search experience.') }}
            </p>

            <div class="usp-showcase__features">
                <div class="usp-showcase__feature">
                    <span class="usp-showcase__feature-icon"><i class="fas fa-home"></i></span>
                    <div class="usp-showcase__feature-body">
                        <h3 class="usp-showcase__feature-title">{{ __('Properties for Every Vision') }}</h3>
                        <p class="usp-showcase__feature-text">{{ __('Houses, apartments, plots and more.') }}</p>
                    </div>
                    <i class="fas fa-arrow-right usp-showcase__feature-arrow"></i>
                </div>

                <div class="usp-showcase__feature">
                    <span class="usp-showcase__feature-icon"><i class="fas fa-compass"></i></span>
                    <div class="usp-showcase__feature-body">
                        <h3 class="usp-showcase__feature-title">{{ __('Discover with Ease') }}</h3>
                        <p class="usp-showcase__feature-text">{{ __('Explore property options in one place.') }}</p>
                    </div>
                    <i class="fas fa-arrow-right usp-showcase__feature-arrow"></i>
                </div>

                <div class="usp-showcase__feature">
                    <span class="usp-showcase__feature-icon"><i class="fas fa-shield-alt"></i></span>
                    <div class="usp-showcase__feature-body">
                        <h3 class="usp-showcase__feature-title">{{ __('Choose with Confidence') }}</h3>
                        <p class="usp-showcase__feature-text">{{ __('Use property information to guide your decisions.') }}</p>
                    </div>
                    <i class="fas fa-arrow-right usp-showcase__feature-arrow"></i>
                </div>
            </div>

            <div class="usp-showcase__actions">
                <a href="{{ route('public.properties') }}" class="btn-primary">
                    {{ __('Explore Properties') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="{{ route('public.member.properties.create') }}" class="usp-showcase__link">
                    {{ __('List Your Property') }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="usp-showcase__tagline">
                <i class="fas fa-asterisk"></i>
                {{ __('Your Next Chapter Starts Here') }}
            </div>
        </div>

        <div class="usp-showcase__media">
            <div class="usp-showcase__image-frame">
                <img src="{{ Theme::asset()->url('images/home-page-new/why-choose-living-room.png') }}"
                    alt="{{ __('Modern living room') }}" class="usp-showcase__image">

                <div class="usp-showcase__chip usp-showcase__chip--top">
                    <i class="fas fa-map-marker-alt"></i>
                    {{ __('Property Discovery') }}
                </div>

                <div class="usp-showcase__card usp-showcase__card--search">
                    <span class="usp-showcase__card-icon"><i class="fas fa-star"></i></span>
                    <div>
                        <span class="usp-showcase__card-eyebrow">{{ __('Your Search') }}</span>
                        <strong class="usp-showcase__card-title">{{ __('Starts here.') }}</strong>
                    </div>
                </div>

                <div class="usp-showcase__overlay-text">
                    <span class="usp-showcase__overlay-eyebrow">{{ __('More Than a Property') }}</span>
                    <h3 class="usp-showcase__overlay-heading">{{ __('Find the place that feels like yours.') }}</h3>
                </div>

                <div class="usp-showcase__card usp-showcase__card--destination">
                    <span class="usp-showcase__card-icon usp-showcase__card-icon--check"><i class="fas fa-check"></i></span>
                    <div>
                        <strong class="usp-showcase__card-title">{{ __('One Destination.') }}</strong>
                        <span class="usp-showcase__card-eyebrow">{{ __('More ways to explore property.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="usp-showcase__marquee" aria-hidden="true">
        <div class="usp-showcase__marquee-track">
            @for ($i = 0; $i < 2; $i++)
                <span class="usp-showcase__marquee-item">{{ __('Find a Home') }}</span>
                <i class="fas fa-asterisk usp-showcase__marquee-divider"></i>
                <span class="usp-showcase__marquee-item">{{ __('Discover Opportunities') }}</span>
                <i class="fas fa-asterisk usp-showcase__marquee-divider"></i>
                <span class="usp-showcase__marquee-item">{{ __('Explore Real Estate') }}</span>
                <i class="fas fa-asterisk usp-showcase__marquee-divider"></i>
                <span class="usp-showcase__marquee-item">{{ __('Make Your Next Move') }}</span>
                <i class="fas fa-asterisk usp-showcase__marquee-divider"></i>
            @endfor
        </div>
    </div>
</section>
