{{--
    3 editorial "browse by topic" tiles, each linking to the blog listing
    filtered by a real seeded category (see the 2026_10_04_000001 migration)
    - same by-name category lookup pattern footer.blade.php already uses for
    its "Property Types" links.

    Each tile's image is a random published post's own uploaded image from
    that category (RvMedia::getImageUrl, same convention as grid-section.php
    and featured-section.php), re-rolled on every page load since nothing
    caches this. The 'image' below is only a per-tile FALLBACK, used when a
    category has no published post with an image yet.
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
            @php
                $topicCategory = $categories->firstWhere('name', $tile['label']);
                $randomPost = $topicCategory
                    ? \Botble\Blog\Models\Post::query()
                        ->where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)
                        ->whereNotNull('image')
                        ->where('image', '!=', '')
                        ->whereHas('categories', fn ($q) => $q->where('categories.id', $topicCategory->id))
                        ->inRandomOrder()
                        ->first()
                    : null;
                $tileImageUrl = $randomPost
                    ? RvMedia::getImageUrl($randomPost->image, 'medium')
                    : Theme::asset()->url($tile['image']);
            @endphp
            <a href="{{ $topicCategory ? route('public.blog', ['category_id' => $topicCategory->id]) : route('public.blog') }}"
               class="blog-listing-topics__tile">
                <img src="{{ $tileImageUrl }}" alt="{{ $tile['title'] }}" class="blog-listing-topics__image" loading="lazy">
                <div class="blog-listing-topics__overlay"></div>
                <span class="blog-listing-topics__label">{{ strtoupper($tile['label']) }}</span>
                <h3 class="blog-listing-topics__title">{{ $tile['title'] }}</h3>
            </a>
        @endforeach
    </div>
</section>
