<section class="agent-directory">
    <div class="agent-directory__hero" style="background-image: url('{{ Theme::asset()->url('images/agents-hero-bg.png') }}')">
        <div class="agent-directory__hero-overlay"></div>
        <div class="agent-directory__hero-inner">
            <span class="agent-directory__eyebrow">{{ __('GEMlisting Agent Directory') }}</span>
            <h1 class="agent-directory__heading">{{ __('Meet the people who') }} <span>{{ __('make property') }}</span> {{ __('happen.') }}</h1>
            <p class="agent-directory__subtext">
                {{ __('Connect with experienced real estate professionals who understand your market, your goals and your next move.') }}
            </p>
            <div class="agent-directory__hero-actions">
                <a href="#agent-search-section" class="agent-directory__btn agent-directory__btn--primary">
                    {{ __('Explore Agents') }} <i class="fas fa-arrow-down"></i>
                </a>
                <a href="{{ route('public.agent-application.create') }}" class="agent-directory__btn agent-directory__btn--outline">
                    {{ __('Become an Agent') }}
                </a>
            </div>
        </div>
    </div>

    <div class="agent-directory__stats-wrap">
        <div class="agent-directory__stats">
            <div class="agent-directory__stat">
                <span class="agent-directory__stat-icon"><i class="fas fa-user-tie"></i></span>
                <div class="agent-directory__stat-body">
                    <strong>{{ $statVerifiedAgents }}+</strong>
                    <span>{{ __('Verified Agents') }}</span>
                </div>
            </div>
            <div class="agent-directory__stat">
                <span class="agent-directory__stat-icon"><i class="fas fa-map-marker-alt"></i></span>
                <div class="agent-directory__stat-body">
                    <strong>{{ $statCities }}+</strong>
                    <span>{{ __('Cities Covered') }}</span>
                </div>
            </div>
            <div class="agent-directory__stat">
                <span class="agent-directory__stat-icon"><i class="fas fa-home"></i></span>
                <div class="agent-directory__stat-body">
                    <strong>{{ $statProperties }}+</strong>
                    <span>{{ __('Properties Listed') }}</span>
                </div>
            </div>
            <div class="agent-directory__stat">
                <span class="agent-directory__stat-icon"><i class="fas fa-star"></i></span>
                <div class="agent-directory__stat-body">
                    <strong>{{ $statRating > 0 ? $statRating . '/5' : __('New') }}</strong>
                    <span>{{ __('Client Rating') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="agent-directory__body" id="agent-search-section">
        <div class="agent-directory__intro">
            <div class="agent-directory__intro-text">
                <span class="agent-directory__intro-eyebrow">{{ __('Our Professionals') }}</span>
                <h2 class="agent-directory__intro-heading">{{ __('Find an agent') }} <span>{{ __('that fits you.') }}</span></h2>
            </div>
            <p class="agent-directory__intro-subtext">
                {{ __('Search our verified network of real estate professionals by name, location or specialty.') }}
            </p>
        </div>

        <agent-search
            url="{{ route('public.ajax.agents') }}"
            cities-url="{{ route('public.ajax.cities-by-country') }}"
            countries="{{ json_encode($countries) }}"
            cities="{{ json_encode($cities) }}"
            languages="{{ json_encode($languages) }}"
            categories="{{ json_encode($categories) }}"
            top-categories="{{ json_encode($topCategories) }}"
            default-country-id="{{ $defaultCountryId }}"
            default-city-id="{{ $defaultCityId }}"
        ></agent-search>
    </div>

    <div class="agent-directory__cta">
        <div class="agent-directory__cta-inner">
            <span class="agent-directory__cta-icon"><i class="fas fa-handshake"></i></span>
            <div class="agent-directory__cta-text">
                <span class="agent-directory__cta-eyebrow">{{ __('Are You A Real Estate Professional?') }}</span>
                <h2 class="agent-directory__cta-heading">{{ __('Grow your business with GEMlisting.') }}</h2>
                <p class="agent-directory__cta-subtext">
                    {{ __('Join our network and connect with serious property buyers, sellers and investors.') }}
                </p>
            </div>
            <a href="{{ route('public.agent-application.create') }}" class="agent-directory__btn agent-directory__btn--primary agent-directory__cta-btn">
                {{ __('Join GEMlisting') }} <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
