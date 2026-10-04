{{--
    3 editorial "browse by topic" tiles, each linking to the blog listing
    filtered by a real seeded category (see the 2026_10_04_000001 migration)
    - same by-name category lookup pattern footer.blade.php already uses for
    its "Property Types" links. Images reused from existing theme assets,
    no new uploads.
--}}
@php
    $topicTiles = [
        ['label' => __('Market Trends'), 'title' => __('Property Trends to Watch'), 'image' => 'images/home-page-new/cta-move-bg.jpg'],
        ['label' => __('Property Guide'), 'title' => __('Designing a Better Home'), 'image' => 'images/home-page-new/why-choose-living-room.png'],
        ['label' => __('Investment'), 'title' => __('Building Long-Term Value'), 'image' => 'images/home-page-new/properties-hero-bg.jpg'],
    ];
@endphp

<section class="blog-listing-topics">
    <div class="blog-listing-topics__inner">
        @foreach ($topicTiles as $tile)
            @php $topicCategory = $categories->firstWhere('name', $tile['label']); @endphp
            <a href="{{ $topicCategory ? route('public.blog', ['category_id' => $topicCategory->id]) : route('public.blog') }}"
               class="blog-listing-topics__tile">
                <img src="{{ Theme::asset()->url($tile['image']) }}" alt="{{ $tile['title'] }}" class="blog-listing-topics__image" loading="lazy">
                <div class="blog-listing-topics__overlay"></div>
                <span class="blog-listing-topics__label">{{ strtoupper($tile['label']) }}</span>
                <h3 class="blog-listing-topics__title">{{ $tile['title'] }}</h3>
            </a>
        @endforeach
    </div>
</section>
