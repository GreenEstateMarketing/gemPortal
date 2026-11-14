{{--
    Path in theme:  platform/themes/real-scout/partials/how-it-works/seller-cta.blade.php
    Rendered via:   views/real-estate/how-it-works-seller.blade.php, route "public.how-it-works-seller".

    Same cream-card + doodle pattern as buyer-cta.blade.php. Button goes
    to the real property-wizard entry point (general-property-wizard.show,
    platform/plugins/real-estate/routes/web.php:664), not the legacy
    "general-add-property" redirect alias.
--}}
<div class="how-works-cta-wrap">
    <section class="how-works-cta">
        <span class="how-works-cta__doodle" aria-hidden="true">
            <svg viewBox="0 0 120 70" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#e0a63e" stroke-width="1.6" stroke-linejoin="round" stroke-linecap="round">
                <path d="M6 44V62H32V44M2 46L19 31L36 46" />
                <path d="M26 32V62H60V32M22 35L43 15L64 35" />
                <rect x="32" y="44" width="8" height="12" />
                <path d="M52 44V62H84V44M48 46L68 29L88 46" />
                <rect x="60" y="50" width="8" height="10" />
            </svg>
        </span>

        <div class="how-works-cta__content">
            <h2 class="how-works-cta__heading">{{ __('Ready to List Your Property?') }}</h2>
            <p class="how-works-cta__text">{{ __('Get started today and reach more buyers!') }}</p>
        </div>

        <a href="{{ route('general-property-wizard.show') }}" class="how-works-cta__btn">
            {{ __('List Your Property') }} <i class="fas fa-arrow-right"></i>
        </a>
    </section>
</div>
