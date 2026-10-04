{{--
    Category pills + search box for the blog listing. Plain GET links/form
    reloading the page with ?category_id=/?q= - no JS framework dependency,
    consistent with this site avoiding the already-broken Vue search bundle
    used elsewhere. $filterCategories/$categoryId/$keyword come from
    PublicController::getIndex() - $filterCategories is a max-6 list that's
    randomized when no filter is active, or narrowed to only categories
    present in the current filtered results otherwise (see the controller's
    comment), so every pill here is guaranteed to lead somewhere non-empty.
    Pill links preserve the current $keyword so that guarantee still holds
    after a click.
--}}
<section class="blog-listing-filter">
    <div class="blog-listing-filter__inner">
        <div class="blog-listing-filter__pills">
            <a href="{{ route('public.blog') }}" class="blog-listing-filter__pill {{ !$categoryId ? 'is-active' : '' }}">
                {{ __('All Articles') }}
            </a>
            @foreach ($filterCategories as $category)
                <a href="{{ route('public.blog', $keyword ? ['category_id' => $category->id, 'q' => $keyword] : ['category_id' => $category->id]) }}"
                   class="blog-listing-filter__pill {{ (string) $categoryId === (string) $category->id ? 'is-active' : '' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('public.blog') }}" method="GET" class="blog-listing-filter__search">
            @if ($categoryId)
                <input type="hidden" name="category_id" value="{{ $categoryId }}">
            @endif
            <input type="text" name="q" value="{{ $keyword }}" placeholder="{{ __('Search articles...') }}">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
</section>
