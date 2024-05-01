@php
    use Botble\Base\Enums\BaseStatusEnum;
    use Botble\Location\Repositories\Interfaces\CityInterface;

    $cities = collect([]);
    if (is_plugin_active('location')) {
        $cities = app(CityInterface::class)->advancedGet([
            'condition' => [
                'cities.is_featured' => true,
                'cities.status'      => BaseStatusEnum::PUBLISHED,
            ],
            'take'      => (int)theme_option('number_of_featured_cities', 10),
        ]);
    }
@endphp

@if ($cities->count())
    <div class="container-fluid w90">

        <div class="padtop70">
            <div class="areahome">
                <div class="row">
                    <div class="col-12">
                        <div class="property-head text-center">

                            <h4 class="mb-5"><span>Properties</span>  By Locations</h4>
                            <p>{{ theme_option('home_description_for_properties_by_locations') }}</p>
                        </div>
                    </div>
                </div>
                <div style="position:relative;">

                    <div class="owl-carousel" id="cityslide">
                        @foreach($cities as $city)
                            <div class="item itemarea">
                                <a href="{{ route('public.properties-by-city', $city->id) }}">
                                    <img src="{{ RvMedia::getImageUrl($city->image, 'small', false, RvMedia::getDefaultImage()) }}" alt="{{ $city->name }}">

                                </a>
                            </div>
                        @endforeach
                    </div>
                    <i class="am-next"><img src="{{ Theme::asset()->url('images/aleft.png') }}" alt="pre"></i>
                    <i class="am-prev"><img src="{{ Theme::asset()->url('images/aright.png') }}" alt="next"></i>
                </div>
            </div>

        </div>
    </div>
@endif
