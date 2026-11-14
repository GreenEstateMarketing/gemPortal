{{--
    Path in theme:  platform/themes/real-scout/partials/how-it-works/seller-steps.blade.php
    Rendered via:   views/real-estate/how-it-works-seller.blade.php, route "public.how-it-works-seller".

    Same structure as buyer-steps.blade.php, including the badge/spine
    behavior: the numbered badge always sits next to the TEXT card and
    alternates sides with it (01 left, 02 right, 03 left, ...), exactly
    like the buyer page - not pinned to one fixed column. An earlier
    version of this file kept the badge in a single column because that
    matched the mockup's own layout, but the site's convention (and
    what was asked for) is to keep the alternating badge behavior
    consistent across both pages.
--}}
@php
    $steps = [
        [
            'title' => __('Submit Ad'),
            'text' => __('Fill in your ad content, upload property pictures and provide ownership documents.'),
            'image' => '02_submit_ad.jpg',
            'alt' => __('Submit your property ad'),
        ],
        [
            'title' => __('Choose Agent'),
            'text' => __('Pick your favorite agent or let GEMlisting choose the right professional for you.'),
            'image' => '03_choose_agent.jpg',
            'alt' => __('Choose your agent'),
        ],
        [
            'title' => __('Ad Verification'),
            'text' => __('Verification of ad content, pictures, location and ownership documentation.'),
            'image' => '04_ad_verification.jpg',
            'alt' => __('Ad verification checklist'),
        ],
        [
            'title' => __('Sign Contract'),
            'text' => __('Sign the Letter of Representation with GEMlisting.'),
            'image' => '05_sign_contract.jpg',
            'alt' => __('Sign the listing contract'),
        ],
        [
            'title' => __('Listing Payment'),
            'text' => __('Pay the required fee for your property advertisement listing.'),
            'image' => '06_listing_payment.jpg',
            'alt' => __('Pay the listing fee'),
        ],
        [
            'title' => __('Ad Listing'),
            'text' => __('Your property becomes live for 03 / 06 months according to the package.'),
            'image' => '07_ad_listing.png',
            'alt' => __('Your property is now listed'),
        ],
    ];
@endphp

<section class="how-works-steps">
    <div class="how-works-steps__inner">
        <div class="how-works-steps__intro">
            <div class="how-works-steps__heading-row">
                <span class="how-works-steps__rule"></span>
                <h2 class="how-works-steps__heading">{{ __('The Seller Process') }}</h2>
                <span class="how-works-steps__rule"></span>
            </div>
            <p class="how-works-steps__text">
                {{ __('Follow these simple steps to list your property on GEMlisting.') }}
            </p>
        </div>

        <div class="how-works-steps__timeline">
            <span class="how-works-steps__spine"></span>

            @foreach ($steps as $index => $step)
                @php $isEven = $index % 2 === 1; @endphp
                <div class="how-works-steps__row @if ($isEven) how-works-steps__row--reverse @endif">
                    <div class="how-works-steps__image-panel">
                        <img src="{{ Theme::asset()->url('images/how-it-works/' . $step['image']) }}" alt="{{ $step['alt'] }}" />
                    </div>

                    <span class="how-works-steps__badge @if ($isEven) how-works-steps__badge--stub @endif">
                        {{ sprintf('%02d', $index + 1) }}
                    </span>

                    <div class="how-works-steps__card">
                        <h3 class="how-works-steps__title">{{ $step['title'] }}</h3>
                        <p class="how-works-steps__card-text">{{ $step['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
