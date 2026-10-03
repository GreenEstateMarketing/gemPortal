<section class="agent-directory">
    <div class="agent-directory__hero" style="background-image: url('{{ Theme::asset()->url('images/agents-hero-bg.png') }}')">
        <div class="agent-directory__hero-overlay"></div>
        <div class="agent-directory__hero-inner">
            <span class="agent-directory__eyebrow">GEMlisting Agent Directory</span>
            <h1 class="agent-directory__heading">Meet the people who <span>make property</span> happen.</h1>
            <p class="agent-directory__subtext">
                Connect with experienced real estate professionals who understand your market, your goals and your next move.
            </p>
            <div class="agent-directory__hero-actions">
                <a href="#agent-search-section" class="agent-directory__btn agent-directory__btn--primary">
                    Explore Agents <i class="fas fa-arrow-down"></i>
                </a>
                <a href="{{ route('member.login') }}" class="agent-directory__btn agent-directory__btn--outline">
                    Become an Agent
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
                    <span>Verified Agents</span>
                </div>
            </div>
            <div class="agent-directory__stat">
                <span class="agent-directory__stat-icon"><i class="fas fa-map-marker-alt"></i></span>
                <div class="agent-directory__stat-body">
                    <strong>{{ $statCities }}+</strong>
                    <span>Cities Covered</span>
                </div>
            </div>
            <div class="agent-directory__stat">
                <span class="agent-directory__stat-icon"><i class="fas fa-home"></i></span>
                <div class="agent-directory__stat-body">
                    <strong>{{ $statProperties }}+</strong>
                    <span>Properties Listed</span>
                </div>
            </div>
            <div class="agent-directory__stat">
                <span class="agent-directory__stat-icon"><i class="fas fa-star"></i></span>
                <div class="agent-directory__stat-body">
                    <strong>{{ $statRating > 0 ? $statRating . '/5' : 'New' }}</strong>
                    <span>Client Rating</span>
                </div>
            </div>
        </div>
    </div>

    <div class="agent-directory__body" id="agent-search-section">
        <div class="agent-directory__intro">
            <div class="agent-directory__intro-text">
                <span class="agent-directory__intro-eyebrow">Our Professionals</span>
                <h2 class="agent-directory__intro-heading">Find an agent <span>that fits you.</span></h2>
            </div>
            <p class="agent-directory__intro-subtext">
                Search our verified network of real estate professionals by name, location or specialty.
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
                <span class="agent-directory__cta-eyebrow">Are You A Real Estate Professional?</span>
                <h2 class="agent-directory__cta-heading">Grow your business with GEMlisting.</h2>
                <p class="agent-directory__cta-subtext">
                    Join our network and connect with serious property buyers, sellers and investors.
                </p>
            </div>
            <a href="{{ route('member.login') }}" class="agent-directory__btn agent-directory__btn--primary agent-directory__cta-btn">
                Join GEMlisting <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
