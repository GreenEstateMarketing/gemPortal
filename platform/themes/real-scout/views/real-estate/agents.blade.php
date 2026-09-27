<section class="agent-directory">
    <div class="agent-directory__hero">
        <div class="agent-directory__hero-inner">
            <span class="agent-directory__eyebrow">Agent Directory</span>
            <h1 class="agent-directory__heading">Find Your Perfect <span>Agent</span></h1>
            <p class="agent-directory__subtext">Connect with top-performing, verified real estate agents in your area.</p>
        </div>
    </div>

    <div class="agent-directory__body">
        <agent-search
            url="{{ route('public.ajax.agents') }}"
            cities-url="{{ route('public.ajax.cities-by-country') }}"
            countries="{{ json_encode($countries) }}"
            cities="{{ json_encode($cities) }}"
            languages="{{ json_encode($languages) }}"
            categories="{{ json_encode($categories) }}"
            default-country-id="{{ $defaultCountryId }}"
            default-city-id="{{ $defaultCityId }}"
        ></agent-search>
    </div>
</section>
