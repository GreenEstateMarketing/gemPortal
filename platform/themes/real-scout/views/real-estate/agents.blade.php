<section class="sales-team">
    <div class="container">
        <h4 class="heading-center"><span>Agents</span></h4>

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
