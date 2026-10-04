{{--
    Category pills + search box for the blog listing. Plain GET links/form
    reloading the page with ?category_id=/?q= - no JS framework dependency,
    consistent with this site avoiding the already-broken Vue search bundle
    used elsewhere. $categories/$categoryId/$keyword come from
    PublicController::getIndex().
--}}
<section class="blog-listing-filter">
    <div class="blog-listing-filter__inner">
        <div class="blog-listing-filter__pills">
            <a href="{{ route('public.blog') }}" class="blog-listing-filter__pill {{ !$categoryId ? 'is-active' : '' }}">
                {{ __('All Articles') }}
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('public.blog', ['category_id' => $category->id]) }}"
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
