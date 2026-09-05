{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/testimonials.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/testimonials') !!}
    Included from:  layouts/homepagenew.blade.php, between meet-agents and the footer partials

    Static content, per design - no database involved. The middle card
    gets a gold top accent border to match the design's "featured"
    treatment; swap $featuredIndex below if a different one should stand
    out instead.
--}}
@php
    $testimonials = [
        [
            'name' => 'Ahmed R.',
            'role' => __('Property Buyer'),
            'quote' => __("GEM Properties made the process of finding our new home incredibly easy. Their team was professional, helpful and transparent."),
        ],
        [
            'name' => 'Sara M.',
            'role' => __('Home Buyer'),
            'quote' => __('The agent understood exactly what we were looking for and showed us excellent options. Highly recommended.'),
        ],
        [
            'name' => 'Hassan K.',
            'role' => __('Property Seller'),
            'quote' => __('Professional service from start to finish. GEM helped us sell our property smoothly and at a good market value.'),
        ],
    ];
    $featuredIndex = 1;
@endphp
<section class="testimonials">
    <div class="container testimonials__inner">

        <div class="testimonials__intro">
            <span class="testimonials__eyebrow">{{ __('Client Reviews') }}</span>
            <h2 class="testimonials__heading">{{ __('What Our Clients Say?') }}</h2>
            <p class="testimonials__text">
                {{ __('Real experiences from people who trusted GEM Properties with their property journey.') }}
            </p>
        </div>

        <div class="testimonials__grid">
            @foreach ($testimonials as $index => $testimonial)
                <div class="testimonials__card @if ($index === $featuredIndex) testimonials__card--featured @endif">
                    <div class="testimonials__card-top">
                        <div class="testimonials__stars">
                            @for ($i = 0; $i < 5; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <i class="fas fa-quote-right testimonials__quote-icon"></i>
                    </div>

                    <p class="testimonials__quote">{{ $testimonial['quote'] }}</p>

                    <div class="testimonials__author">
                        <span class="testimonials__avatar">{{ Str::substr($testimonial['name'], 0, 1) }}</span>
                        <div>
                            <h4 class="testimonials__author-name">{{ $testimonial['name'] }}</h4>
                            <span class="testimonials__author-role">{{ $testimonial['role'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
