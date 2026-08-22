<section class="main-homes">
    <div class="bgheadproject hidden-xs">
        <div class="description">
            <div class="container-fluid w90">
                <h1 class="text-center">{{ __('Properties') }}</h1>
                <p class="text-center">{{ theme_option('properties_description') }}</p>
                {!! Theme::partial('breadcrumb') !!}
            </div>
        </div>
    </div>
    <div class="container-fluid w90 padtop30">
        <div class="projecthome">
            <form action="{{ route('public.properties') }}" method="get">
                <div class="row rowm10">
                    <div class="col-md-3 col-sm-6">
                        @include(Theme::getThemeNamespace() . '::views.real-estate.includes.search-box', ['type' => 'property', 'categories' => $categories])
                    </div>
                    <div class="col-md-9 col-sm-6">
                        @if ($properties->count())
                            @include(Theme::getThemeNamespace() . '::views.real-estate.includes.filters')
                            <div class="row">
                                @foreach ($properties as $property)
                                    <div class="col-6 col-sm-6 col-md-4 colm10">
                                        <div class="item">
                                            <div class="blii">
                                                <div class="img">
                                                    <img class="thumb" data-src="{{ RvMedia::getImageUrl($property->image, 'small', false, RvMedia::getDefaultImage()) }}" src="{{ RvMedia::getImageUrl($property->image, 'small', false, RvMedia::getDefaultImage()) }}" alt="{{ $property->name }}">
                                                </div>
                                                <a href="{{ $property->url }}" class="linkdetail"></a>
                                                <div class="media-count-wrapper">
                                                    <div class="media-count">
                                                        <img src="{{ Theme::asset()->url('images/media-count.svg') }}" alt="media">
                                                        <span>{{ count($property->images) }}</span>
                                                    </div>
                                                </div>
                                                <div class="status">{!! $property->status->toHtml() !!}</div>
                                                <ul class="item-price-wrap hide-on-list"><li class="h-type"><span>{{ $property->category->name }}</span></li> <li class="item-price">{{ format_price($property->price, $property->currency) }}</li></ul>
                                            </div>

                                            <div class="description">
                                                <a href="#" class="text-orange heart add-to-wishlist" data-id="{{ $property->id }}" title="{{ __('I care about this property!!!') }}"><i class="far fa-heart"></i></a>
                                                <a href="{{ $property->url }}"><h5>{{ $property->name }}</h5>
                                                    <p class="dia_chi"><i class="fas fa-map-marker-alt"></i> {{ $property->city->name }}, {{ $property->city->state->name }}</p>
                                                </a>
                                                <p class="threemt bold500">
                                                    @if ($property->number_bedroom)
                                                        <span data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Number of rooms') }}"> <i><img src="{{ Theme::asset()->url('images/bed.svg') }}" alt="icon"></i> <i class="vti">{{ $property->number_bedroom }}</i> </span>
                                                    @endif
                                                    @if ($property->number_bathroom)
                                                        <span data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Number of rest rooms') }}">  <i><img src="{{ Theme::asset()->url('images/bath.svg') }}" alt="icon"></i> <i class="vti">{{ $property->number_bathroom }}</i></span>
                                                    @endif
                                                    @if ($property->square)
                                                        <span data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Square') }}"> <i><img src="{{ Theme::asset()->url('images/area.svg') }}" alt="icon"></i> <i class="vti">{{ $property->square_text }}</i> </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="item">{{ __('0 results') }}</p>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<br>
<div class="col-sm-12">
    <nav class="d-flex justify-content-center pt-3" aria-label="Page navigation example">
        {!! $properties->withQueryString()->links() !!}
    </nav>
</div>
<br>
<br>
