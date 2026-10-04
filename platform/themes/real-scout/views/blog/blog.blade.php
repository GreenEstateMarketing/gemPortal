{{--
    Redesigned /blog listing page - photo hero, category filter/search,
    featured article, 3-column grid, topic tiles, newsletter signup.
    $posts/$featuredPost/$categories/$categoryId/$keyword/$hasFilters all
    come from PublicController::getIndex(). Styled by blog-listing.css,
    enqueued only on the public.blog route (see config.php) - doesn't touch
    blog.css, which post.blade.php and category.blade.php still rely on.

    Grid and featured sections swap order based on $hasFilters: when a
    category/search filter is active, the matching results (grid) are what
    the visitor actually came for, so they render above the otherwise-generic
    "Featured & Latest" spotlight rather than making them scroll past it.
--}}
{!! Theme::partial('blog/hero-section') !!}
{!! Theme::partial('blog/filter-section', compact('filterCategories', 'categoryId', 'keyword')) !!}
@if ($hasFilters)
    {!! Theme::partial('blog/grid-section', compact('posts', 'hasFilters')) !!}
    {!! Theme::partial('blog/featured-section', compact('featuredPost')) !!}
@else
    {!! Theme::partial('blog/featured-section', compact('featuredPost')) !!}
    {!! Theme::partial('blog/grid-section', compact('posts', 'hasFilters')) !!}
@endif
{!! Theme::partial('blog/topics-section', compact('categories')) !!}
{!! Theme::partial('blog/newsletter-section') !!}
