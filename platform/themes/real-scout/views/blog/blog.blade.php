<section class="blog-hero">
    <div class="blog-hero__inner">
        <span class="blog-hero__eyebrow">{{ __('Blog') }}</span>
        <h1 class="blog-hero__heading">{{ __('News & Updates') }}</h1>
        <p class="blog-hero__breadcrumb">
            <a href="{{ route('public.index') }}">{{ __('Home') }}</a>
            <span>/</span>
            {{ __('Blog') }}
        </p>
    </div>
</section>

<section class="blog-section">
    <div class="blog-section__inner">
        <div class="blog-section__main">
            @if ($posts->count() > 0)
                <div class="blog-card-grid">
                    @foreach ($posts as $post)
                        <article class="blog-card">
                            <a href="{{ $post->url }}" title="{{ $post->name }}" class="blog-card__image-link">
                                <img
                                    src="{{ RvMedia::getImageUrl($post->image, 'small', false, RvMedia::getDefaultImage()) }}"
                                    alt="{{ $post->name }}" class="blog-card__image" loading="lazy">
                            </a>
                            <div class="blog-card__body">
                                @if ($post->categories->count())
                                    <span class="blog-card__category">{{ $post->categories->first()->name }}</span>
                                @endif
                                <h3 class="blog-card__title">
                                    <a href="{{ $post->url }}" title="{{ $post->name }}">{{ $post->name }}</a>
                                </h3>
                                <p class="blog-card__excerpt">{{ Str::words($post->description, 24) }}</p>
                                <div class="blog-card__meta">
                                    <span><i class="fas fa-calendar"></i> {{ $post->created_at->format('d M, Y') }}</span>
                                    <span><i class="fas fa-eye"></i> {{ $post->views }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="blog-pagination">
                    {!! $posts->withQueryString()->links() !!}
                </div>
            @else
                <p class="blog-section__empty">{{ __('No posts found.') }}</p>
            @endif
        </div>

        <aside class="blog-sidebar">
            <div class="blog-sidebar__widget">
                <h4 class="blog-sidebar__title">{{ __('Categories') }}</h4>
                <ul class="blog-sidebar__list">
                    @foreach (get_categories(['select' => ['categories.id', 'categories.name']]) as $category)
                        <li><a href="{{ $category->url }}">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="blog-sidebar__widget">
                <h4 class="blog-sidebar__title">{{ __('Recent Posts') }}</h4>
                <ul class="blog-sidebar__recent">
                    @foreach (get_recent_posts(5) as $recentPost)
                        <li>
                            <a href="{{ $recentPost->url }}" class="blog-sidebar__recent-link">
                                <img
                                    src="{{ RvMedia::getImageUrl($recentPost->image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                    alt="{{ $recentPost->name }}">
                                <span>{{ $recentPost->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>
</section>
