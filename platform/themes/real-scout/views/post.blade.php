<section class="blog-post-hero">
    <div class="blog-post-hero__inner">
        @if ($post->categories->count())
            <span class="blog-post-hero__category">{{ $post->categories->first()->name }}</span>
        @endif
        <h1 class="blog-post-hero__title">{{ $post->name }}</h1>
        <div class="blog-post-hero__meta">
            <span><i class="fas fa-calendar"></i> {{ $post->created_at->format('d M, Y') }}</span>
            <span><i class="fas fa-eye"></i> {{ $post->views }}</span>
        </div>
        <p class="blog-post-hero__breadcrumb">
            <a href="{{ route('public.index') }}">{{ __('Home') }}</a>
            <span>/</span>
            <a href="{{ route('public.blog') }}">{{ __('Blog') }}</a>
            <span>/</span>
            {{ $post->name }}
        </p>
    </div>
</section>

@if ($post->image)
    <div class="blog-post-image">
        <img src="{{ RvMedia::getImageUrl($post->image, 'medium', false, RvMedia::getDefaultImage()) }}"
            alt="{{ $post->name }}">
    </div>
@endif

<section class="blog-section">
    <div class="blog-section__inner">
        <div class="blog-section__main">
            <div class="blog-post-content">
                {!! clean($post->content, 'youtube') !!}
            </div>

            @if ($post->tags->count())
                <div class="blog-post-tags">
                    <span class="blog-post-tags__label">{{ __('Tags') }}:</span>
                    @foreach ($post->tags as $tag)
                        <a href="{{ $tag->url }}">{{ $tag->name }}</a>
                    @endforeach
                </div>
            @endif

            <div class="blog-post-share">
                {!! Theme::partial('share', ['title' => __('Share this post'), 'description' => $post->description]) !!}
            </div>

            @php $relatedPosts = get_related_posts($post->id, 2); @endphp
            @if ($relatedPosts->count())
                <div class="blog-post-related">
                    <h3 class="blog-post-related__title">{{ __('Related posts') }}</h3>
                    <div class="blog-card-grid blog-card-grid--related">
                        @foreach ($relatedPosts as $relatedItem)
                            <article class="blog-card">
                                <a href="{{ $relatedItem->url }}" title="{{ $relatedItem->name }}" class="blog-card__image-link">
                                    <img
                                        src="{{ RvMedia::getImageUrl($relatedItem->image, 'small', false, RvMedia::getDefaultImage()) }}"
                                        alt="{{ $relatedItem->name }}" class="blog-card__image" loading="lazy">
                                </a>
                                <div class="blog-card__body">
                                    <h3 class="blog-card__title">
                                        <a href="{{ $relatedItem->url }}" title="{{ $relatedItem->name }}">{{ $relatedItem->name }}</a>
                                    </h3>
                                    <p class="blog-card__excerpt">{{ Str::words($relatedItem->description, 24) }}</p>
                                    <div class="blog-card__meta">
                                        <span><i class="fas fa-calendar"></i> {{ $relatedItem->created_at->format('d M, Y') }}</span>
                                        <span><i class="fas fa-eye"></i> {{ $relatedItem->views }}</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
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
