{{--
    3-column article grid for $posts (paginator, excludes $featuredPost - see
    PublicController::getIndex()). Anchor id="blog-articles" is the hero's
    "Explore Articles" scroll target.
--}}
<section class="blog-listing-grid" id="blog-articles">
    <div class="blog-listing-grid__inner">
        @if ($hasFilters)
            <div class="blog-listing-grid__filtered-bar">
                <span><i class="fas fa-filter"></i> {{ __('Showing filtered results') }}</span>
                <a href="{{ route('public.blog') }}" class="blog-listing-grid__clear">
                    <i class="fas fa-undo"></i> {{ __('Clear Filters') }}
                </a>
            </div>
        @endif

        @if ($posts->count())
            <div class="blog-listing-grid__grid">
                @foreach ($posts as $post)
                    <article class="blog-listing-card">
                        <a href="{{ $post->url }}" title="{{ $post->name }}" class="blog-listing-card__image-link">
                            <img src="{{ RvMedia::getImageUrl($post->image, 'small', false, RvMedia::getDefaultImage()) }}"
                                 alt="{{ $post->name }}" class="blog-listing-card__image" loading="lazy">
                            @if ($post->categories->count())
                                <span class="blog-listing-card__badge">{{ $post->categories->first()->name }}</span>
                            @endif
                        </a>
                        <div class="blog-listing-card__body">
                            <h3 class="blog-listing-card__title">
                                <a href="{{ $post->url }}" title="{{ $post->name }}">{{ $post->name }}</a>
                            </h3>
                            <p class="blog-listing-card__excerpt">{{ Str::words(strip_tags($post->description), 18) }}</p>
                            <div class="blog-listing-card__footer">
                                <span class="blog-listing-card__date">{{ $post->created_at->format('M d, Y') }}</span>
                                <a href="{{ $post->url }}" class="blog-listing-card__read">{{ __('Read') }} <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="blog-listing-pagination">
                {!! $posts->withQueryString()->links() !!}
            </div>
        @else
            <p class="blog-listing-grid__empty">{{ __('No articles found.') }}</p>
        @endif
    </div>
</section>
