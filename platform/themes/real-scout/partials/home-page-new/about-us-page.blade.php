{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/about-us-page.blade.php
    Rendered via:   the [gem-about-us] shortcode (registered in
                     functions/functions.php), placed inside the "About us"
                     admin Page's content field.

    Not to be confused with home-page-new/about-us.blade.php, which is the
    short "About GEMlisting" teaser section on the homepage itself (its
    "Learn More About Us" link points at this page).

    No <section class="legal-content"> wrapper here - page.blade.php's
    "legal" template branch already provides the hero (from $page->name)
    and the wrapper around this content.
--}}
@php
    $aboutSections = [
        [
            'title' => __('Who We Are'),
            'body' => [
                __('Greens Estate Marketing (Private) Limited is the online real estate marketplace established in 2020 to introduce novelty and modernity to the real estate sector of Pakistan. We connect buyers, tenants, landlords, members and agents on a single, easy-to-use platform.'),
            ],
        ],
        [
            'title' => __('Our Mission'),
            'body' => [
                __('We combine market knowledge, trusted professionals and personalized support to make property decisions simpler and more confident - whether you\'re searching for your first home, growing a rental portfolio, or listing a property for sale or rent.'),
            ],
        ],
        [
            'title' => __('What We Offer'),
            'body' => [__('Our platform brings together everything you need for a smooth property journey:')],
            'list' => [
                __('Verified property listings for sale and rent across houses, apartments, commercial units and plots.'),
                __('A guided listing wizard that makes it simple for members and agents to publish accurate, well-presented listings.'),
                __('A network of professional, reviewed agents who understand local markets.'),
                __('Direct inquiry and messaging tools to connect buyers and tenants with agents and landlords.'),
            ],
        ],
        [
            'title' => __('Why Choose Greens Estate Marketing (Private) Limited'),
            'body' => [__('What sets us apart:')],
            'list' => [
                __('Verified Properties - we focus on authentic and reliable property information.'),
                __('Trusted Professionals - connect with experienced agents who understand the market.'),
                __('Market Expertise - make smarter decisions with professional property guidance.'),
                __('Secure Transactions - professional support throughout your property journey.'),
            ],
        ],
        [
            'title' => __('Our Commitment'),
            'body' => [
                __('We\'re committed to building a transparent, reliable marketplace for the real estate sector of Pakistan, and to continuously improving the platform based on the needs of the buyers, tenants, landlords, members and agents who use it every day.'),
            ],
        ],
    ];
@endphp

<div class="legal-content__intro">
    <p>
        {{ __('Learn more about Greens Estate Marketing (Private) Limited, the team behind it, and what we\'re building for buyers, tenants, landlords, members and agents across Pakistan.') }}
    </p>
</div>

<div class="legal-content__sections">
    @foreach ($aboutSections as $index => $section)
        <div class="legal-section">
            <div class="legal-section__number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
            <div class="legal-section__body">
                <h2>{{ $section['title'] }}</h2>
                @foreach ($section['body'] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
                @if (!empty($section['list']))
                    <ul class="legal-section__list">
                        @foreach ($section['list'] as $item)
                            <li><i class="fas fa-check"></i> {{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="legal-contact-card">
    <div class="legal-contact-card__icon"><i class="fas fa-envelope-open-text"></i></div>
    <div class="legal-contact-card__body">
        <h3>{{ __('Want To Know More?') }}</h3>
        <p>{{ __('Reach out to our team - we\'re happy to answer any questions about Greens Estate Marketing (Private) Limited.') }}</p>
        <ul class="legal-contact-card__details">
            @if (theme_option('address'))
                <li><i class="fas fa-map-marker-alt"></i> {{ theme_option('address') }}</li>
            @endif
            @if (theme_option('hotline'))
                <li><a href="tel:{{ theme_option('hotline') }}"><i class="fas fa-phone"></i> {{ theme_option('hotline') }}</a></li>
            @endif
            @if (theme_option('email'))
                <li><a href="mailto:{{ theme_option('email') }}"><i class="fas fa-envelope"></i> {{ theme_option('email') }}</a></li>
            @endif
        </ul>
    </div>
</div>
