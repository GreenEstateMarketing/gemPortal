{{--
    "Featured & Latest" heading + the single large $featuredPost card.
    $featuredPost is the newest is_featured=1 published post, falling back
    to the newest published post if none are flagged (PublicController::getIndex()),
    same fallback pattern as projects/spotlight-section.blade.php's $spotlightProject.
--}}
@if ($featuredPost)
    <section class="blog-listing-featured">
        <div class="blog-listing-featured__inner">
            <div class="blog-listing-featured__header">
                <span class="blog-listing-featured__eyebrow">{{ __('Featured & Latest') }}</span>
                <h2 class="blog-listing-featured__heading">{{ __('Stories That Help You Move Smarter') }}</h2>
                <p class="blog-listing-featured__text">
                    {{ __('From buying your first home to understanding property investment, discover useful insights from the world of real estate.') }}
                </p>
            </div>

            <article class="blog-listing-featured__card">
                <div class="blog-listing-featured__image-wrap">
                    <span class="blog-listing-featured__ribbon">{{ __('Featured Article') }}</span>
                    <img src="{{ RvMedia::getImageUrl($featuredPost->image, 'medium', false, RvMedia::getDefaultImage()) }}"
                         alt="{{ $featuredPost->name }}" class="blog-listing-featured__image" loading="lazy">
                </div>
                <div class="blog-listing-featured__body">
                    @if ($featuredPost->categories->count())
                        <span class="blog-listing-featured__category">{{ $featuredPost->categories->first()->name }}</span>
                    @endif
                    <h3 class="blog-listing-featured__title">
                        <a href="{{ $featuredPost->url }}">{{ $featuredPost->name }}</a>
                    </h3>
                    <p class="blog-listing-featured__excerpt">{{ Str::limit(strip_tags($featuredPost->description), 180) }}</p>
                    <div class="blog-listing-featured__meta">
                        <span>
                            <i class="fas fa-user"></i>
                            {{ optional($featuredPost->author)->getFullName() ?: (theme_option('seo_title', 'GEMlisting') . ' ' . __('Editorial')) }}
                        </span>
                        <span><i class="fas fa-calendar"></i> {{ $featuredPost->created_at->format('F d, Y') }}</span>
                        <span><i class="fas fa-clock"></i> {{ max(1, (int) ceil(str_word_count(strip_tags($featuredPost->content)) / 200)) }} {{ __('min read') }}</span>
                    </div>
                    <a href="{{ $featuredPost->url }}" class="blog-listing-featured__link">
                        {{ __('Read Full Article') }} <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
        </div>
    </section>
@endif
