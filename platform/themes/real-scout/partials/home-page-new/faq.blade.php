{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/faq.blade.php
    Rendered via:   the [gem-faq] shortcode (registered in
                     functions/functions.php), placed inside the FAQ admin
                     Page's content field.

    No <section class="legal-content"> wrapper here - page.blade.php's
    "legal" template branch already provides the hero (from $page->name)
    and the wrapper around this content.
--}}
@php
    $faqs = [
        [
            'q' => __('Is it free to search for properties on GEMlisting?'),
            'a' => __('Yes. Browsing and searching property listings, viewing agent profiles and contacting agents through GEMlisting is completely free for buyers and tenants.'),
        ],
        [
            'q' => __('How do I list my property for sale or rent?'),
            'a' => __('Click "Add Property" in the main menu, sign in or create an account, and follow the guided listing wizard to add your property details, photos and pricing. Your listing will be reviewed and published once it meets our listing guidelines.'),
        ],
        [
            'q' => __('Do I need to be a licensed real estate agent to list a property?'),
            'a' => __('No. Property owners (members) can list their own properties directly. Real estate agents can also register for an agent account, which unlocks additional tools for managing multiple listings and clients.'),
        ],
        [
            'q' => __('How are agents on GEMlisting verified?'),
            'a' => __('Agent accounts go through a review process before they\'re able to publish listings under an agent profile. We also encourage buyers and tenants to independently verify any agent\'s credentials before entering into an agreement.'),
        ],
        [
            'q' => __('Does GEMlisting charge a commission on sales or rentals?'),
            'a' => __('GEMlisting does not charge buyers or tenants any fee to use the platform. Listing fees or featured-listing charges may apply for agents and landlords - any such fees are shown clearly before you publish a paid listing.'),
        ],
        [
            'q' => __('How do I contact an agent or property owner about a listing?'),
            'a' => __('Open any property listing and use the inquiry form or contact details on the page to message the agent or owner directly. You can also connect via WhatsApp where available.'),
        ],
        [
            'q' => __('Can I schedule a property viewing through the site?'),
            'a' => __('Yes, you can request a viewing directly through a listing\'s inquiry form. The agent or landlord will coordinate the exact date and time with you.'),
        ],
        [
            'q' => __('How do I edit or remove my listing?'),
            'a' => __('Sign in to your account dashboard, go to your property listings, and choose to edit or remove any listing you\'ve published. Changes are typically reflected on the site within a few hours after moderation.'),
        ],
        [
            'q' => __('What should I do if I suspect a listing is fraudulent?'),
            'a' => __('Please contact our support team right away with the listing details. We take fraudulent or misleading listings seriously and will investigate and remove them where appropriate.'),
        ],
        [
            'q' => __('Is my personal information safe with GEMlisting?'),
            'a' => __('We take reasonable measures to protect your personal information as described in our Privacy Policy. We never sell your personal information to third parties.'),
        ],
        [
            'q' => __('How do I reset my password?'),
            'a' => __('On the login page, click "Forgot password?" and follow the instructions sent to your registered email address to set a new password.'),
        ],
    ];
@endphp

<div class="legal-content__intro">
    <p>
        {{ __('Answers to the questions we hear most often from buyers, tenants, landlords, members and agents using GEMlisting. Can\'t find what you\'re looking for? Reach out to us using the details below.') }}
    </p>
</div>

{{--
    Uses native <details>/<summary> rather than a JS-driven toggle - this
    site has a pre-existing, broken global Vue mount on #app (see the
    "[Vue warn]: Cannot find element: #app" / DOM-replacement console
    errors present site-wide) that clobbers listeners attached to content
    inside #app after the page's own inline <script> tags have already run.
    <details> needs no JS at all, so it isn't affected by that.
--}}
<div class="legal-faq">
    @foreach ($faqs as $faq)
        <details class="legal-faq__item">
            <summary class="legal-faq__question">
                <span>{{ $faq['q'] }}</span>
                <span class="legal-faq__icon"><i class="fas fa-plus"></i></span>
            </summary>
            <div class="legal-faq__answer">
                <p>{{ $faq['a'] }}</p>
            </div>
        </details>
    @endforeach
</div>

<div class="legal-contact-card">
    <div class="legal-contact-card__icon"><i class="fas fa-envelope-open-text"></i></div>
    <div class="legal-contact-card__body">
        <h3>{{ __('Still Have Questions?') }}</h3>
        <p>{{ __('Our support team is happy to help with anything not covered above.') }}</p>
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
