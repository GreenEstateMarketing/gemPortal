{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/property-categories.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/property-categories') !!}
    Included from:  layouts/homepagenew.blade.php, between about-us and the footer partials

    DATA GAPS (read before touching content):
    - re_categories has NO image column/relation at all, and categories are
      added/renamed dynamically with no admin upload UI for a per-category
      photo. Image priority per card:
        1) images/home-page-new/categories/{category-name-slug}/ - a folder
           of photos (e.g. categories/house/1.jpg, 2.jpg, ...); one is
           picked at random on every page load.
        2) images/home-page-new/categories/{category-name-slug}.jpg - a
           single override file, for a one-off category.
        3) Pixabay, auto-fetched by category name
           (getCategoryImageUrlFromPixabay() in functions.php, needs
           PIXABAY_API_KEY in .env - free key at
           https://pixabay.com/api/docs/), cached 7 days.
        4) categories/_placeholder.jpg if none of the above exist.
      Image files must also be copied into public/themes/real-scout/... -
      the theme's public/ dir is a build-time copy, not a symlink.
    - Only one category in the whole table ("Factory") has a real
      `description`; everything else is empty. Falls back to a generic
      "Explore quality {name} listings..." line when empty so the layout
      never shows blank space - swap in real copy per category whenever
      you have it (same column, no schema change needed).

    Both the "Search" button and every card's "Explore" link point to
    route('public.properties', ['category_id' => ...]) - the exact same
    URL/param the header search bar's Property Type field submits to.
--}}
@php
    $propertyCategoryCards = \Botble\RealEstate\Models\Category::query()
        ->where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)
        ->where('parent_id', '!=', 0)
        ->inRandomOrder()
        ->limit(5)
        ->get();

    $categoryParentNames = \Botble\RealEstate\Models\Category::query()
        ->whereIn('id', $propertyCategoryCards->pluck('parent_id')->filter()->unique())
        ->pluck('name', 'id');
@endphp
<section class="property-categories">
    <div class="container property-categories__inner">

        <div class="property-categories__intro">
            <span class="property-categories__eyebrow">{{ __('Property Categories') }}</span>
            <h2 class="property-categories__heading">{{ __('Search By Property Type') }}</h2>
            <p class="property-categories__text">
                {{ __('Choose a property category and discover options that match your needs.') }}
            </p>

            <form action="{{ route('public.properties') }}" method="GET" class="property-categories__search"
                id="propertyCategorySearchForm" autocomplete="off">
                <input type="hidden" name="category_id" id="propertyCategorySearchId" value="">

                <span class="property-categories__search-icon"><i class="fas fa-search"></i></span>
                <input type="text" id="propertyCategorySearchInput" class="property-categories__search-input"
                    placeholder="{{ __('Search property categories...') }}">
                <button type="submit" class="property-categories__search-btn">{{ __('Search') }}</button>

                <div class="property-categories__suggestions" id="propertyCategorySuggestions" style="display:none"></div>
            </form>
        </div>

        <div class="property-categories__grid">
            @foreach ($propertyCategoryCards as $category)
                @php
                    // Image priority: (1) a random pick from a local folder
                    // named after the category (images/home-page-new/categories/{slug}/,
                    // e.g. categories/house/1.jpg) - re-rolled on every page
                    // load - (2) a single manually-dropped-in local file
                    // named after the category, for a one-off override -
                    // (3) a Pixabay photo matched by category name, cached
                    // 7 days (see getCategoryImageUrlFromPixabay() in
                    // functions.php) - (4) the shared local placeholder if
                    // none of the above exist.
                    $categorySlug = \Illuminate\Support\Str::slug($category->name);
                    $categoryImagesDir = 'images/home-page-new/categories/' . $categorySlug;
                    $categoryImagesDirFullPath = platform_path('themes/real-scout/public/' . $categoryImagesDir);

                    $categoryImageUrl = null;

                    if (is_dir($categoryImagesDirFullPath)) {
                        $categoryImageFiles = collect(glob($categoryImagesDirFullPath . '/*.*'))
                            ->filter(fn ($file) => in_array(
                                Str::lower(pathinfo($file, PATHINFO_EXTENSION)),
                                ['jpg', 'jpeg', 'png', 'webp', 'avif'],
                            ))
                            ->values();

                        if ($categoryImageFiles->isNotEmpty()) {
                            $categoryImageUrl = Theme::asset()->url(
                                $categoryImagesDir . '/' . basename($categoryImageFiles->random()),
                            );
                        }
                    }

                    if (! $categoryImageUrl) {
                        $categoryLocalImagePath = 'images/home-page-new/categories/' . $categorySlug . '.jpg';
                        $categoryLocalImageExists = file_exists(
                            platform_path('themes/real-scout/public/' . $categoryLocalImagePath),
                        );

                        $categoryImageUrl = $categoryLocalImageExists
                            ? Theme::asset()->url($categoryLocalImagePath)
                            : (getCategoryImageUrlFromPixabay($category->name) ??
                                Theme::asset()->url('images/home-page-new/categories/_placeholder.jpg'));
                    }

                    $categoryDescription = $category->description
                        ? $category->description
                        : __(':name listings tailored to your needs.', ['name' => $category->name]);
                @endphp
                <div class="property-categories__card">
                    <img src="{{ $categoryImageUrl }}" alt="{{ $category->name }}"
                        class="property-categories__card-image">

                    <div class="property-categories__card-body">
                        @if ($parentName = $categoryParentNames->get($category->parent_id))
                            <span class="property-categories__card-tag">{{ Str::upper($parentName) }}</span>
                        @endif

                        <h3 class="property-categories__card-title">{{ $category->name }}</h3>
                        <p class="property-categories__card-text">{{ Str::limit($categoryDescription, 80) }}</p>

                        <a href="{{ route('public.properties', ['category_id' => $category->id]) }}"
                            class="property-categories__card-cta">
                            {{ __('Explore') }} <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
