{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/shipping-policy.blade.php
    Rendered via:   the [gem-shipping-policy] shortcode (registered in
                     functions/functions.php), placed inside the
                     Shipping/Delivery Policy admin Page's content field.

    GEM Properties doesn't ship physical goods - this page instead covers
    how our digital services (listing publication, notifications, document
    delivery) are "delivered", which is the common framing payment
    providers expect from a Shipping/Delivery Policy page even for a
    services-only platform. No <section class="legal-content"> wrapper here
    - page.blade.php's "legal" template branch already provides the hero
    (from $page->name) and the wrapper around this content.
--}}
@php
    $shippingSections = [
        [
            'title' => __('Nature of Our Services'),
            'body' => [
                __('gemlisting.co is an online real estate marketplace. We do not sell, ship or deliver any physical goods. Everything we provide - property listings, agent connections, inquiries and account features - is delivered digitally through our website.'),
            ],
        ],
        [
            'title' => __('Listing Publication Timelines'),
            'body' => [__('When you submit a property listing (as a member, agent or through Add Property), it is typically processed and published as follows:')],
            'list' => [
                __('Free listings - reviewed and published within 1-2 business days after submission.'),
                __('Featured or paid listings - reviewed and published within 24 hours of a successful payment, subject to our listing guidelines.'),
                __('Listing updates and edits - changes you make to an existing listing usually go live within a few hours after moderation.'),
            ],
        ],
        [
            'title' => __('Digital Communications & Notifications'),
            'body' => [
                __('Account confirmations, inquiry notifications, wishlist alerts and other communications are delivered electronically to the email address or phone number on your account, typically within minutes of the triggering action. Delivery times can vary depending on your email or mobile provider and are outside of our direct control.'),
            ],
        ],
        [
            'title' => __('Documents & Agreements'),
            'body' => [
                __('Where a transaction involves documents such as a Letter of Representation, viewing confirmation or agreement, these are delivered digitally (by email or through your account dashboard) rather than by post or courier, unless a specific agent or landlord arranges otherwise directly with you.'),
            ],
        ],
        [
            'title' => __('Delays'),
            'body' => [__('Occasionally, delivery of listings or notifications may be delayed due to:')],
            'list' => [
                __('High volume of new listings or inquiries awaiting moderation.'),
                __('Scheduled maintenance or unplanned technical issues.'),
                __('Incomplete or inaccurate information submitted with a listing, requiring follow-up before it can be published.'),
            ],
            'after' => [
                __('We work to keep these delays to a minimum and will let you know if a submission needs attention before it can go live.'),
            ],
        ],
        [
            'title' => __('Questions About This Policy'),
            'body' => [
                __('If a listing, notification or document you expected has not been delivered within the timelines above, please contact our support team using the details below and we will look into it.'),
            ],
        ],
    ];
@endphp

<p class="legal-content__updated">{{ __('Last updated') }}: {{ \Illuminate\Support\Carbon::now()->format('F j, Y') }}</p>

<div class="legal-content__intro">
    <p>
        {{ __('This Shipping/Delivery Policy explains how gemlisting.co delivers its services to buyers, tenants, landlords, members and agents. As an online real estate marketplace, we do not ship physical products - "delivery" here refers to how and when our digital services reach you.') }}
    </p>
</div>

<div class="legal-content__sections">
    @foreach ($shippingSections as $index => $section)
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
                @if (!empty($section['after']))
                    @foreach ($section['after'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="legal-contact-card">
    <div class="legal-contact-card__icon"><i class="fas fa-envelope-open-text"></i></div>
    <div class="legal-contact-card__body">
        <h3>{{ __('Questions About Delivery?') }}</h3>
        <p>{{ __('If something you expected hasn\'t arrived - a listing, a notification, a document - reach out and we\'ll help sort it out.') }}</p>
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
