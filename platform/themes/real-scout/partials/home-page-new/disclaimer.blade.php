{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/disclaimer.blade.php
    Rendered via:   the [gem-disclaimer] shortcode (registered in
                     functions/functions.php), placed inside the Disclaimer
                     admin Page's content field.

    No <section class="legal-content"> wrapper here - page.blade.php's
    "legal" template branch already provides the hero (from $page->name)
    and the wrapper around this content.
--}}
@php
    $disclaimerSections = [
        [
            'title' => __('Listing Accuracy'),
            'body' => [
                __('Property listings on GEMlisting - including descriptions, photos, floor plans, pricing, availability and location details - are submitted by agents, landlords and members. While we take reasonable steps to moderate listings, we do not independently verify every detail and cannot guarantee that any listing is complete, accurate, current or error-free. Prices, availability and features can change without notice.'),
            ],
        ],
        [
            'title' => __('No Professional Advice'),
            'body' => [
                __('Nothing on GEMlisting constitutes legal, financial, tax or investment advice. Content on this site is provided for general informational purposes only. Before making any property, financial or legal decision, you should seek advice from a qualified real estate agent, lawyer, financial advisor or other relevant professional.'),
            ],
        ],
        [
            'title' => __('Not a Party to Transactions'),
            'body' => [
                __('GEMlisting is a platform that connects buyers, tenants, landlords, members and agents - we are not a party to any sale, purchase, lease or other transaction arranged between users. We do not guarantee the conduct, reliability or qualifications of any agent, landlord, tenant or buyer using our platform, and any agreement you enter into is solely between you and the other party.'),
            ],
        ],
        [
            'title' => __('Independent Verification Advised'),
            'body' => [__('Before proceeding with any property transaction, we strongly recommend that you:')],
            'list' => [
                __('Personally view the property and verify its condition, size and features.'),
                __('Independently confirm ownership, title and legal status of the property.'),
                __('Verify the identity and credentials of any agent, landlord or other party you deal with.'),
                __('Obtain independent legal and financial advice before signing any agreement or making any payment.'),
            ],
        ],
        [
            'title' => __('Third-Party Links & Content'),
            'body' => [
                __('Our site may link to third-party websites or display third-party content (such as agent profiles or embedded maps) for convenience. We do not endorse and are not responsible for the accuracy, legality or content of any third-party site or service.'),
            ],
        ],
        [
            'title' => __('No Warranty'),
            'body' => [
                __('GEMlisting and its content are provided "as is" and "as available" without warranties of any kind, whether express or implied, including but not limited to warranties of accuracy, merchantability, fitness for a particular purpose or non-infringement. We do not guarantee that the site will be uninterrupted, secure or error-free.'),
            ],
        ],
        [
            'title' => __('Limitation of Liability'),
            'body' => [
                __('To the fullest extent permitted by law, GEMlisting and its owners, employees and affiliates shall not be liable for any direct, indirect, incidental or consequential loss or damage arising from your use of the site, reliance on any listing or content, or any transaction or dealing with another user.'),
            ],
        ],
        [
            'title' => __('Changes to This Disclaimer'),
            'body' => [
                __('We may update this Disclaimer from time to time to reflect changes in our practices or for legal, operational or regulatory reasons. We will post the updated version on this page with a revised "last updated" date.'),
            ],
        ],
    ];
@endphp

<p class="legal-content__updated">{{ __('Last updated') }}: {{ \Illuminate\Support\Carbon::now()->format('F j, Y') }}</p>

<div class="legal-content__intro">
    <p>
        {{ __('The following disclaimer applies to your use of GEMlisting ("we", "us" or "our") and the property listings, agent information and other content available on our platform. Please read it carefully alongside our Terms & Conditions and Privacy Policy.') }}
    </p>
</div>

<div class="legal-content__sections">
    @foreach ($disclaimerSections as $index => $section)
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
        <h3>{{ __('Questions About This Disclaimer?') }}</h3>
        <p>{{ __('If you have any questions about this Disclaimer, reach out to us.') }}</p>
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
