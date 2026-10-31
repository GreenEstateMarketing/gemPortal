@php
    $agentWhatsapp = preg_replace('/\D/', '', (string) $account->phone);

    // Specialty is "real" admin-assigned expertise when set; falls back to
    // the categories of the agent's own listings (same derivation used on
    // the homepage's meet-agents cards) for agents created before that
    // field existed, then to a generic label if neither is available.
    $specialtyNames = $account->specialties->pluck('name')->filter()->unique();

    if ($specialtyNames->isEmpty()) {
        $specialtyNames = $account->properties()
            ->with('category')
            ->get()
            ->pluck('category.name')
            ->filter()
            ->unique();
    }

    $agentSpecialty = $specialtyNames->take(3)->join(', ', ' & ');

    // Rating stars: full stars rounded down, one half star when the
    // remainder is >= 0.5, the rest empty — out of 5.
    $ratingAvgRounded = round($ratingAvg ?? 0, 1);
    $ratingFullStars = (int) floor($ratingAvgRounded);
    $ratingHasHalfStar = ($ratingAvgRounded - $ratingFullStars) >= 0.5;
    $ratingEmptyStars = 5 - $ratingFullStars - ($ratingHasHalfStar ? 1 : 0);
@endphp

<section class="agent-detail">
    <div class="agent-detail__hero" style="background-image: url('{{ Theme::asset()->url('images/agent-detail-bg.png') }}')">
        <div class="agent-detail__hero-overlay"></div>
        <div class="agent-detail__hero-inner">
            <span class="agent-detail__eyebrow">{{ __('Our Agents') }}</span>
            <h1 class="agent-detail__hero-name">{{ $account->getFullName() }}</h1>
            <p class="agent-detail__hero-role">
                {{ __('Real Estate Consultant') }}{{ $agentSpecialty ? ' | ' . $agentSpecialty : '' }}
            </p>
            <span class="agent-detail__hero-rule"></span>
        </div>
    </div>

    <div class="agent-detail__profile-wrap">
        <div class="agent-detail__profile">
            <div class="agent-detail__photo">
                <img src="{{ $account->image_path ? Storage::url($account->image_path) : $account->avatar_url }}"
                    alt="{{ $account->getFullName() }}">
            </div>

            <div class="agent-detail__info">
                @if ($account->confirmed_at)
                    <span class="agent-detail__verified"><i class="fas fa-shield-alt"></i> {{ __('Verified Agent') }}</span>
                @endif

                <h2 class="agent-detail__name">{{ $account->getFullName() }}</h2>
                <p class="agent-detail__role">
                    {{ __('Real Estate Consultant') }}{{ $agentSpecialty ? ' | ' . $agentSpecialty : '' }}
                </p>

                <div class="agent-detail__rating">
                    @for ($i = 0; $i < $ratingFullStars; $i++)
                        <i class="fas fa-star"></i>
                    @endfor
                    @if ($ratingHasHalfStar)
                        <i class="fas fa-star-half-alt"></i>
                    @endif
                    @for ($i = 0; $i < $ratingEmptyStars; $i++)
                        <i class="far fa-star"></i>
                    @endfor
                    <span class="agent-detail__rating-text">
                        {{ $ratingCount > 0 ? number_format($ratingAvgRounded, 1) : __('New') }}
                        ({{ $ratingCount }} {{ __('reviews') }})
                    </span>
                </div>

                <div class="agent-detail__contacts">
                    @if ($account->phone)
                        <a href="tel:{{ $account->phone }}" class="agent-detail__contact">
                            <i class="fas fa-phone"></i> {{ $account->phone }}
                        </a>
                    @endif
                    @if ($account->email)
                        <a href="mailto:{{ $account->email }}" class="agent-detail__contact">
                            <i class="fas fa-envelope"></i> {{ $account->email }}
                        </a>
                    @endif
                    @if ($account->city && $account->city->name)
                        <span class="agent-detail__contact">
                            <i class="fas fa-map-marker-alt"></i> {{ $account->city->name }}
                        </span>
                    @endif
                </div>

                @if ($account->description)
                    <p class="agent-detail__bio">{{ $account->description }}</p>
                @endif

                <div class="agent-detail__cta">
                    @if ($account->phone)
                        <a href="tel:{{ $account->phone }}" class="agent-detail__btn agent-detail__btn--primary">
                            <i class="fas fa-phone"></i> {{ __('Call Now') }}
                        </a>
                    @endif
                    <button type="button" class="agent-detail__btn agent-detail__btn--outline" id="agentMessageTrigger">
                        <i class="fas fa-comment-dots"></i> {{ __('Message') }}
                    </button>
                    <div class="agent-detail__share">
                        {!! Theme::partial('share', ['title' => __('Share this agent'), 'description' => $account->getFullName()]) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="agent-detail__stats-wrap">
        <div class="agent-detail__stats">
            <div class="agent-detail__stat">
                <span class="agent-detail__stat-icon"><i class="fas fa-home"></i></span>
                <strong>{{ $propertiesSoldCount }}+</strong>
                <span class="agent-detail__stat-label">{{ __('Properties Sold') }}</span>
            </div>
            <div class="agent-detail__stat">
                <span class="agent-detail__stat-icon"><i class="fas fa-user-friends"></i></span>
                <strong>{{ $happyClientsCount }}+</strong>
                <span class="agent-detail__stat-label">{{ __('Happy Clients') }}</span>
            </div>
            <div class="agent-detail__stat">
                <span class="agent-detail__stat-icon"><i class="fas fa-award"></i></span>
                <strong>{{ $account->years_of_experience ?: '—' }}+</strong>
                <span class="agent-detail__stat-label">{{ __('Years Experience') }}</span>
            </div>
            <div class="agent-detail__stat">
                <span class="agent-detail__stat-icon"><i class="fas fa-star"></i></span>
                <strong>{{ $ratingCount > 0 ? number_format($ratingAvgRounded, 1) : __('New') }}</strong>
                <span class="agent-detail__stat-label">{{ __('Client Rating') }}</span>
            </div>
        </div>
    </div>

    <div class="agent-detail__services">
        <div class="agent-detail__services-intro">
            <h2>{{ __('My Services') }}</h2>
            <p>{{ __('I provide professional real estate services to help you make the right decision.') }}</p>
        </div>
        <div class="agent-detail__services-grid">
            <div class="agent-detail__service">
                <span class="agent-detail__service-icon"><i class="fas fa-home"></i></span>
                <strong>{{ __('Buy Properties') }}</strong>
                <span>{{ __('Find your dream home') }}</span>
            </div>
            <div class="agent-detail__service">
                <span class="agent-detail__service-icon"><i class="fas fa-tag"></i></span>
                <strong>{{ __('Sell Properties') }}</strong>
                <span>{{ __('Get the best value') }}</span>
            </div>
            <div class="agent-detail__service">
                <span class="agent-detail__service-icon"><i class="fas fa-chart-line"></i></span>
                <strong>{{ __('Investment Advice') }}</strong>
                <span>{{ __('Grow your wealth') }}</span>
            </div>
            <div class="agent-detail__service">
                <span class="agent-detail__service-icon"><i class="fas fa-users"></i></span>
                <strong>{{ __('Property Consultation') }}</strong>
                <span>{{ __('Expert guidance') }}</span>
            </div>
        </div>
    </div>

    <div class="agent-detail__listings">
        <div class="agent-detail__listings-header">
            <div>
                <h2>{{ __('Properties by This Agent') }}</h2>
                <p>{{ __('Explore some of the best properties listed by') }} {{ $account->getFullName() }}.</p>
            </div>
        </div>

        @if ($properties->count())
            <div class="agent-detail__grid">
                @foreach ($properties as $property)
                    @php
                        // Matches properties-highlights's badge logic:
                        // selling/renting/building keep the plain type
                        // label, sold/rented/closed show the status instead.
                        $badgeLabel = in_array($property->status->getValue(), ['sold', 'rented', 'closed'])
                            ? $property->status->label()
                            : $property->type->label();
                        $badgeColorClasses = ['selling', 'renting', 'sold', 'rented', 'building', 'closed', 'unpublished'];
                        $badgeColorClass = in_array($property->status->getValue(), $badgeColorClasses)
                            ? ' agent-detail__property-badge--' . $property->status->getValue()
                            : '';
                        $propertyIsFavourite = in_array($property->id, $favouriteIds);
                    @endphp
                    <div class="agent-detail__property-card">
                        <a href="{{ $property->url }}" title="{{ $property->name }}" class="agent-detail__property-image-wrap">
                            <img src="{{ RvMedia::getImageUrl($property->image, 'medium', false, RvMedia::getDefaultImage()) }}"
                                alt="{{ $property->name }}" class="agent-detail__property-image" loading="lazy">
                            <span class="agent-detail__property-badge{{ $badgeColorClass }}">{{ $badgeLabel }}</span>
                            <button type="button" class="agent-detail__property-favorite add-to-wishlist"
                                data-id="{{ $property->id }}"
                                data-is-favourite="{{ $propertyIsFavourite ? 'true' : 'false' }}"
                                title="{{ __('Save to favorites') }}">
                                <i class="{{ $propertyIsFavourite ? 'fas' : 'far' }} fa-heart"></i>
                            </button>
                        </a>
                        <a href="{{ $property->url }}" title="{{ $property->name }}" class="agent-detail__property-body">
                            <p class="agent-detail__property-price">{{ format_price($property->price, $property->currency) }}</p>
                            <h3 class="agent-detail__property-title">{{ $property->name }}</h3>
                            @if ($property->city && $property->city->name)
                                <p class="agent-detail__property-location">
                                    <i class="fas fa-map-marker-alt"></i> {{ $property->city->name }}
                                </p>
                            @endif
                            <div class="agent-detail__property-meta">
                                @if ($property->number_bedroom)
                                    <span><i class="fas fa-bed"></i> {{ $property->number_bedroom }} {{ __('Beds') }}</span>
                                @endif
                                @if ($property->number_bathroom)
                                    <span><i class="fas fa-bath"></i> {{ $property->number_bathroom }} {{ __('Baths') }}</span>
                                @endif
                                @if ($property->square)
                                    <span><i class="fas fa-ruler-combined"></i> {{ $property->square_text }}</span>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="agent-detail__pagination">
                {!! $properties->withQueryString()->links() !!}
            </div>
        @else
            <p class="agent-detail__empty">{{ __('0 results') }}</p>
        @endif
    </div>

    <div class="agent-detail__modal" id="agentMessageModal">
        <div class="agent-detail__modal-overlay" id="agentMessageModalOverlay"></div>
        <div class="agent-detail__modal-dialog">
            <button type="button" class="agent-detail__modal-close" id="agentMessageModalClose" aria-label="{{ __('Close') }}">
                <i class="fas fa-times"></i>
            </button>
            {!! Theme::partial('consult-form', ['type' => 'agent', 'data' => $account]) !!}
        </div>
    </div>
</section>
