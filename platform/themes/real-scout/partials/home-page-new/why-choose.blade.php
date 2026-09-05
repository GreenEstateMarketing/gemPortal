{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/why-choose.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/why-choose') !!}
    Included from:  layouts/homepagenew.blade.php, between the header and footer partials

    IMAGE: home-page-new/why-choose-living-room.jpg is a placeholder (reusing
    an existing theme banner image) so the section isn't broken while there's
    no real asset yet. Swap that file for the actual photo and this partial
    needs no changes.
--}}
<section class="why-choose" id="why-choose-gem">
    <div class="container why-choose__inner">

        <div class="why-choose__content">
            <span class="why-choose__eyebrow">{{ __('Why Choose GEM') }}</span>

            <h2 class="why-choose__heading">
                {{ __('A Better Way to') }} <span class="why-choose__heading--accent">{{ __('Find') }}</span><br>
                <span class="why-choose__heading--accent">{{ __('Property.') }}</span>
            </h2>

            <p class="why-choose__text">
                {{ __('We combine market knowledge, trusted professionals and personalized support to make property decisions simpler and more confident.') }}
            </p>

            <div class="why-choose__features">
                <div class="why-choose__feature">
                    <span class="why-choose__feature-icon"><i class="fas fa-shield-alt"></i></span>
                    <div>
                        <h3 class="why-choose__feature-title">{{ __('Verified Properties') }}</h3>
                        <p class="why-choose__feature-text">{{ __('We focus on authentic and reliable property information.') }}</p>
                    </div>
                </div>

                <div class="why-choose__feature">
                    <span class="why-choose__feature-icon"><i class="fas fa-user-tie"></i></span>
                    <div>
                        <h3 class="why-choose__feature-title">{{ __('Trusted Professionals') }}</h3>
                        <p class="why-choose__feature-text">{{ __('Connect with experienced agents who understand the market.') }}</p>
                    </div>
                </div>

                <div class="why-choose__feature">
                    <span class="why-choose__feature-icon"><i class="fas fa-chart-line"></i></span>
                    <div>
                        <h3 class="why-choose__feature-title">{{ __('Market Expertise') }}</h3>
                        <p class="why-choose__feature-text">{{ __('Make smarter decisions with professional property guidance.') }}</p>
                    </div>
                </div>

                <div class="why-choose__feature">
                    <span class="why-choose__feature-icon"><i class="fas fa-lock"></i></span>
                    <div>
                        <h3 class="why-choose__feature-title">{{ __('Secure Transactions') }}</h3>
                        <p class="why-choose__feature-text">{{ __('Professional support throughout your property journey.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="why-choose__media">
            <img src="{{ Theme::asset()->url('images/home-page-new/why-choose-living-room.png') }}"
                alt="{{ __('Modern living room') }}" class="why-choose__image">

            <div class="why-choose__badge">
                <span class="why-choose__badge-icon"><i class="fas fa-star"></i></span>
                <div>
                    <h4 class="why-choose__badge-title">{{ __('Trusted Service') }}</h4>
                    <p class="why-choose__badge-text">{{ __('Professional property guidance') }}</p>
                </div>
            </div>
        </div>

    </div>
</section>
