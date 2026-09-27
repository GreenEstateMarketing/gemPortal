{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/privacy-policy.blade.php
    Rendered via:   the [gem-privacy-policy] shortcode (registered in
                     functions/functions.php), placed inside the Privacy
                     Policy admin Page's content field.

    This partial defines its own :root fallback for the --header-* custom
    properties (same pattern as site-footer.css) rather than relying on
    home-page-new/header.css being loaded, since this page uses the plain
    "legal" template/layout (inner-header + footer), not the homepagenew
    layout. Its CSS and fonts are registered globally in config.php's
    beforeRenderTheme (gated on the "privacy-policy" URL) rather than as
    <link> tags here - a <link> placed in this content ends up nested
    inside the empty <p> that the page module's clean() helper wraps the
    page's raw "[gem-privacy-policy]" text in before this shortcode is
    expanded, and that empty <p>'s default browser margin then renders as
    a stray gap under the site header. Content itself never goes through
    clean() - only the literal "[gem-privacy-policy]" token in the Page's
    content field does, so this partial is free to use real heading tags,
    classes and structure to match the home page's design.
--}}
@php
    $privacySections = [
        [
            'title' => __('Information We Collect'),
            'body' => [
                __('We collect information you provide directly to us, such as when you create an account, list a property, submit an inquiry, message an agent, or contact our support team. This may include:'),
            ],
            'list' => [
                __('Contact details - name, email address, phone number and mailing address.'),
                __('Account information - username, password and profile details such as your photo or bio.'),
                __('Property details - listings, photos, floor plans, pricing and location data you submit as an agent or member.'),
                __('Inquiry and communication data - messages exchanged with agents, landlords or our support team.'),
                __('Payment information - billing details processed by our payment providers when you pay for a listing or subscription (we do not store full card numbers on our servers).'),
            ],
        ],
        [
            'title' => __('Information Collected Automatically'),
            'body' => [
                __('When you browse GEMlisting, we automatically collect certain information about your device and how you use our site, including:'),
            ],
            'list' => [
                __('Usage data - pages viewed, searches performed, properties saved and time spent on the site.'),
                __('Device data - IP address, browser type, operating system and general location derived from your IP address.'),
                __('Cookies and similar technologies - used to keep you signed in, remember your preferences and understand how our site is used (see "Cookies & Tracking" below).'),
            ],
        ],
        [
            'title' => __('How We Use Your Information'),
            'body' => [__('We use the information we collect to:')],
            'list' => [
                __('Operate, maintain and improve our property listings, search and matching features.'),
                __('Connect buyers, tenants and members with agents and landlords.'),
                __('Process transactions, subscriptions and payments related to listings.'),
                __('Send you service updates, account notifications and, where you have opted in, marketing communications.'),
                __('Detect, investigate and prevent fraud, abuse and other unauthorized or illegal activity.'),
                __('Comply with our legal obligations and enforce our Terms & Conditions.'),
            ],
        ],
        [
            'title' => __('How We Share Your Information'),
            'body' => [
                __('We do not sell your personal information. We may share information in the following circumstances:'),
            ],
            'list' => [
                __('With agents and landlords - so they can respond to your property inquiries or manage listings you interact with.'),
                __('With service providers - payment processors, hosting providers and analytics partners who help us operate the platform, under contractual confidentiality obligations.'),
                __('For legal reasons - if required by law, regulation, legal process or governmental request, or to protect the rights, property and safety of GEMlisting, our users or the public.'),
                __('In a business transfer - if GEMlisting is involved in a merger, acquisition or sale of assets, your information may be transferred as part of that transaction.'),
            ],
        ],
        [
            'title' => __('Cookies & Tracking Technologies'),
            'body' => [
                __('We use cookies and similar technologies to keep you signed in, remember your search preferences (such as currency and area unit), and understand how visitors use our site so we can improve it. You can control or disable cookies through your browser settings, though some parts of GEMlisting may not function properly without them.'),
            ],
        ],
        [
            'title' => __('Data Security'),
            'body' => [
                __('We use reasonable administrative, technical and physical safeguards designed to protect your personal information from unauthorized access, disclosure, alteration or destruction. However, no method of transmission over the internet or electronic storage is completely secure, and we cannot guarantee absolute security.'),
            ],
        ],
        [
            'title' => __('Data Retention'),
            'body' => [
                __('We retain personal information for as long as your account is active or as needed to provide you services, comply with our legal obligations, resolve disputes and enforce our agreements. When information is no longer needed, we take reasonable steps to delete or anonymize it.'),
            ],
        ],
        [
            'title' => __('Your Rights & Choices'),
            'body' => [__('Depending on your location, you may have the right to:')],
            'list' => [
                __('Access, correct or update the personal information we hold about you.'),
                __('Request deletion of your account and associated personal information.'),
                __('Opt out of marketing communications at any time using the unsubscribe link in our emails.'),
                __('Object to or restrict certain processing of your information.'),
            ],
            'after' => [
                __('To exercise any of these rights, please contact us using the details below.'),
            ],
        ],
        [
            'title' => __('Children\'s Privacy'),
            'body' => [
                __('GEMlisting is not directed to children under 18, and we do not knowingly collect personal information from children. If you believe a child has provided us with personal information, please contact us so we can remove it.'),
            ],
        ],
        [
            'title' => __('Third-Party Links'),
            'body' => [
                __('Our site may contain links to third-party websites, such as agent websites or social media pages. We are not responsible for the privacy practices of those third parties, and we encourage you to review their privacy policies before providing any personal information.'),
            ],
        ],
        [
            'title' => __('Changes to This Policy'),
            'body' => [
                __('We may update this Privacy Policy from time to time to reflect changes in our practices or for legal, operational or regulatory reasons. We will post the updated policy on this page with a revised "last updated" date, and encourage you to review it periodically.'),
            ],
        ],
    ];

    $privacyWhatsapp = preg_replace('/\D/', '', (string) theme_option('hotline'));
@endphp

{{--
    No <section class="legal-content">/container wrapper here - page.blade.php's
    "legal" template branch already provides the hero (from $page->name) and
    this wrapper around whatever [gem-*] shortcode expands to. This partial
    renders only the inner content.
--}}
<p class="legal-content__updated">{{ __('Last updated') }}: {{ \Illuminate\Support\Carbon::now()->format('F j, Y') }}</p>

<div class="legal-content__intro">
    <p>
        {{ __('Greens Estate Marketing (Private) Limited also mentioned as GEMlisting ("we", "us" or "our") respects your privacy and is committed to protecting the personal information of everyone who uses our website and services, including buyers, tenants, landlords, members and agents. This Privacy Policy explains what information we collect, how we use and share it, and the choices you have.') }}
    </p>
    <p>
        {{ __('By using GEMlisting, you agree to the collection and use of information in accordance with this policy. If you do not agree, please discontinue use of our site and services.') }}
    </p>
</div>

        <div class="legal-content__sections">
            @foreach ($privacySections as $index => $section)
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
                <h3>{{ __('Questions About This Policy?') }}</h3>
                <p>{{ __('If you have any questions or requests regarding this Privacy Policy or how we handle your information, reach out to us.') }}</p>
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
