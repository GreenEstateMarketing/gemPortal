{{--
    "Featured Projects" grid. $projects is a paginator built in
    PublicController::getProjects() - filtered by the hero search form
    (name/city_id/category_id) when present. Pagination links aren't shown
    here (the design is a fixed-size grid), but $projects stays a real
    paginator so platform/themes/flex-home/views/real-estate/projects.blade.php
    (which does call ->withQueryString()->links()) keeps working too.
--}}
<section class="projects-grid-section">
    <div class="projects-grid-section__inner">
        <div class="projects-grid-section__header">
            <div>
                <span class="projects-grid-section__eyebrow">{{ __('Our Projects') }}</span>
                <h2 class="projects-grid-section__heading">{{ __('Premium Projects') }}</h2>
                <p class="projects-grid-section__text">{{ __('Explore carefully selected projects from popular locations.') }}</p>
            </div>
            @if ($hasFilters)
                <a href="{{ route('public.projects') }}" class="projects-grid-section__clear">
                    <i class="fas fa-undo"></i> {{ __('Clear Filters') }}
                </a>
            @endif
        </div>

        @if ($projects->count())
            <div class="projects-grid-section__grid">
                @foreach ($projects as $project)
                    @php
                        $statusValue = $project->status ? $project->status->getValue() : null;
                        $statusLabel = $project->status ? $project->status->label() : null;
                        $statusClass = $statusValue ? 'is-' . $statusValue : '';
                    @endphp
                    <a href="{{ $project->url }}" title="{{ $project->name }}" class="projects-grid-section__card">
                        <div class="projects-grid-section__card-image-wrap">
                            <img
                                src="{{ RvMedia::getImageUrl($project->image, 'medium', false, RvMedia::getDefaultImage()) }}"
                                alt="{{ $project->name }}" class="projects-grid-section__card-image" loading="lazy">
                            @if ($project->is_featured)
                                <span class="projects-grid-section__card-ribbon"><i class="fas fa-star"></i> {{ __('Featured') }}</span>
                            @endif
                            @if ($statusLabel)
                                <span class="projects-grid-section__card-status {{ $statusClass }}">{{ $statusLabel }}</span>
                            @endif
                        </div>
                        <div class="projects-grid-section__card-body">
                            @if ($project->city && $project->city->name)
                                <p class="projects-grid-section__card-location">
                                    <i class="fas fa-map-marker-alt"></i> {{ $project->city->name }}
                                </p>
                            @endif
                            <h3 class="projects-grid-section__card-title">{{ $project->name }}</h3>
                            @if ($project->description)
                                <p class="projects-grid-section__card-desc">{{ Str::limit(strip_tags($project->description), 90) }}</p>
                            @endif
                            <div class="projects-grid-section__card-footer">
                                @if ($project->price_from || $project->price_to)
                                    <span class="projects-grid-section__card-price">
                                        {{ format_price($project->price_from ?: $project->price_to, $project->currency) }}
                                    </span>
                                @endif
                                <span class="projects-grid-section__card-view">{{ __('View') }} <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="projects-grid-section__empty">{{ __('No projects match your search.') }}</p>
        @endif
    </div>
</section>
