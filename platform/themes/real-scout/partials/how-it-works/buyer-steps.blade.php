{{--
    Path in theme:  platform/themes/real-scout/partials/how-it-works/buyer-steps.blade.php
    Rendered via:   views/real-estate/how-it-works-buyer.blade.php, route "public.how-it-works-buyer".

    v2: vertical timeline (numbered badge + text card + photo panel per
    step), replacing v1's zigzag cards + curved SVG connector arrows
    (which looked jagged and never quite reached the next card cleanly).
    No curves/diagonals here at all - just a single straight dashed
    "spine" (border-left, see .how-works-steps__spine in the CSS) sitting
    at a fixed x under the odd badges (01, 03), with even badges (02, 04)
    getting only a short independent decorative stub. Confirmed against
    the mockup by cropping/zooming it, not guessed.

    Each badge sits immediately to the left of the TEXT card, never the
    image - so row order flips: odd steps are [badge][text][image],
    even steps are [image][badge][text]. See how-it-works-page.css's
    .how-works-steps__row--reverse for the flip.
--}}
@php
    $steps = [
        [
            'title' => __('Search Properties'),
            'text' => __('Use our powerful search tools to find properties by location, type, price range and more.'),
            'image' => 'buyer_search_location.png',
            'alt' => __('Search properties by location'),
        ],
        [
            'title' => __('Explore Listings'),
            'text' => __('Browse detailed listings with high-quality photos, key features and verified information.'),
            'image' => 'buyer_explore_property_listings.png',
            'alt' => __('Browse detailed property listings'),
        ],
        [
            'title' => __('Connect with Agents'),
            'text' => __('Get in touch with trusted agents or property owners directly through our platform.'),
            'image' => 'buyer_connect_agent.png',
            'alt' => __('Connect with a trusted agent'),
        ],
        [
            'title' => __('Move Forward'),
            'text' => __('Schedule a visit, ask more questions, and complete your purchase with confidence.'),
            'image' => 'buyer_move_forward_handshake.png',
            'alt' => __('Complete your property purchase'),
        ],
    ];
@endphp

<section class="how-works-steps">
    <div class="how-works-steps__inner">
        <div class="how-works-steps__intro">
            <div class="how-works-steps__heading-row">
                <span class="how-works-steps__rule"></span>
                <h2 class="how-works-steps__heading">{{ __('The Buyer Process') }}</h2>
                <span class="how-works-steps__rule"></span>
            </div>
            <p class="how-works-steps__text">
                {{ __('Follow these simple steps to find and purchase your ideal property on GEMlisting.') }}
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
