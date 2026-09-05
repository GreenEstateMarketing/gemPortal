{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/property-categories.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/property-categories') !!}
    Included from:  layouts/homepagenew.blade.php, between about-us and the footer partials

    DATA GAPS (read before touching content):
    - re_categories has NO image column/relation at all, and categories are
      added/renamed dynamically with no admin upload UI for a per-category
      photo. Card images are auto-fetched from Pixabay by category name
      (getCategoryImageUrlFromPixabay() in functions.php, needs
      PIXABAY_API_KEY in .env - free key at https://pixabay.com/api/docs/),
      cached 7 days. Drop a local file named "{category-name-slug}.jpg" into
      categories/ (e.g. categories/house.jpg) to override a specific
      category's image without touching Pixabay at all - checked first,
      before the API call. Falls back to categories/_placeholder.jpg if
      neither a local override nor a Pixabay match/API key exists.
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
                    // Image priority: (1) a manually-dropped-in local file
                    // named after the category, for whenever you want to
                    // override a specific one - (2) a Pixabay photo matched
                    // by category name, cached 7 days (see
                    // getCategoryImageUrlFromPixabay() in functions.php) -
                    // (3) the shared local placeholder if neither exists.
                    $categorySlug = \Illuminate\Support\Str::slug($category->name);
                    $categoryLocalImagePath = 'images/home-page-new/categories/' . $categorySlug . '.jpg';
                    $categoryLocalImageExists = file_exists(
                        platform_path('themes/real-scout/public/' . $categoryLocalImagePath),
                    );

                    if ($categoryLocalImageExists) {
                        $categoryImageUrl = Theme::asset()->url($categoryLocalImagePath);
                    } else {
                        $categoryImageUrl =
                            getCategoryImageUrlFromPixabay($category->name) ??
                            Theme::asset()->url('images/home-page-new/categories/_placeholder.jpg');
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
