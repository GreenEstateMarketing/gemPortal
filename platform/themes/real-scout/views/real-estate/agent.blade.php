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
@endphp

<section class="agent-detail">
    <div class="agent-detail__hero">
        <div class="agent-detail__hero-inner">
            <img src="{{ $account->image_path ? Storage::url($account->image_path) : $account->avatar_url }}"
                alt="{{ $account->getFullName() }}" class="agent-detail__avatar">

            <div class="agent-detail__intro">
                <span class="agent-detail__role">{{ __('Property Consultant') }}</span>
                <h1 class="agent-detail__name">{{ $account->getFullName() }}</h1>
                <p class="agent-detail__specialty">{{ $agentSpecialty ?: __('Real Estate Properties') }}</p>

                <div class="agent-detail__actions">
                    @if ($account->phone)
                        <a href="tel:{{ $account->phone }}" class="agent-detail__action" title="{{ __('Call') }}">
                            <i class="fas fa-phone"></i>
                        </a>
                    @endif
                    @if ($account->email)
                        <a href="mailto:{{ $account->email }}" class="agent-detail__action" title="{{ __('Email') }}">
                            <i class="fas fa-envelope"></i>
                        </a>
                    @endif
                    @if ($agentWhatsapp)
                        <a href="https://wa.me/{{ $agentWhatsapp }}" target="_blank" rel="noopener"
                            class="agent-detail__action" title="{{ __('WhatsApp') }}">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="agent-detail__stats-wrap">
        <div class="agent-detail__stats">
            <div class="agent-detail__stat">
                <strong>{{ $properties->total() }}</strong>
                <span>{{ __('Properties Listed') }}</span>
            </div>
            <div class="agent-detail__stat">
                <strong>{{ $account->years_of_experience ?: '—' }}</strong>
                <span>{{ __('Years Experience') }}</span>
            </div>
            <div class="agent-detail__stat">
                <strong>{{ $account->city->name ?? '—' }}</strong>
                <span>{{ __('Based In') }}</span>
            </div>
        </div>
    </div>

    <div class="agent-detail__body">
        @if ($account->description)
            <div class="agent-detail__bio-wrap">
                <p class="agent-detail__bio">{{ $account->description }}</p>
            </div>
        @endif

        <div class="agent-detail__listings">
            <h2 class="agent-detail__listings-heading">{{ __('Properties by this agent') }}</h2>

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
                        @endphp
                        <a href="{{ $property->url }}" title="{{ $property->name }}" class="agent-detail__property-card">
                            <div class="agent-detail__property-image-wrap">
                                <img src="{{ RvMedia::getImageUrl($property->image, 'medium', false, RvMedia::getDefaultImage()) }}"
                                    alt="{{ $property->name }}" class="agent-detail__property-image" loading="lazy">
                                <span class="agent-detail__property-badge{{ $badgeColorClass }}">{{ $badgeLabel }}</span>
                            </div>
                            <div class="agent-detail__property-body">
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
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="agent-detail__pagination">
                    {!! $properties->withQueryString()->links() !!}
                </div>
            @else
                <p class="agent-detail__empty">{{ __('0 results') }}</p>
            @endif
        </div>
    </div>
</section>
