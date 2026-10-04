{{--
    Photo hero matching /properties' hero (partials/properties/search-section.blade.php)
    - same background image/overlay treatment (see projects-hero.css), with
    its own simple name/location/type search form instead of the shared
    home-page-new/search-bar partial (that one is property-rental specific -
    bedrooms/price range - and doesn't fit a project search).
--}}
<section class="projects-hero">
    <div class="projects-hero__overlay"></div>
    <div class="projects-hero__inner">
        <span class="projects-hero__eyebrow"><i class="fas fa-building"></i> {{ __('Explore Projects') }}</span>
        <h1 class="projects-hero__heading">
            {{ __('Find a project') }}<br>
            <span class="projects-hero__heading--accent">{{ __("you'll love.") }}</span>
        </h1>
        <p class="projects-hero__text">
            {{ __('Discover modern residential and commercial projects in the best locations.') }}
        </p>

        <form action="{{ route('public.projects') }}" method="GET" class="projects-search-card">
            <div class="projects-search-card__field">
                <label>{{ __('Search Project') }}</label>
                <div class="projects-search-card__input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="name" value="{{ $name }}" placeholder="{{ __('Project name...') }}">
                </div>
            </div>
            <div class="projects-search-card__field">
                <label>{{ __('Location') }}</label>
                <div class="projects-search-card__input">
                    <i class="fas fa-map-marker-alt"></i>
                    <select name="city_id" id="projects-city-select">
                        <option value="">{{ __('All locations') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" {{ (string) $cityId === (string) $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="projects-search-card__field">
                <label>{{ __('Type') }}</label>
                <div class="projects-search-card__input">
                    <i class="fas fa-building"></i>
                    <select name="category_id">
                        <option value="">{{ __('All types') }}</option>
                        @foreach ($categories as $id => $categoryName)
                            <option value="{{ $id }}" {{ (string) $categoryId === (string) $id ? 'selected' : '' }}>{{ $categoryName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="projects-search-card__submit">
                {{ __('Search') }} <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>
</section>
