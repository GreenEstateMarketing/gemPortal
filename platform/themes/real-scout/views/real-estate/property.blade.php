<main class="detailproject" style="background: #fff;">
    <div class="boxsliderdetail">
        <div class="slidetop">
            <div class="owl-carousel" id="listcarousel">
                @foreach ($property->images as $image)
                    <div class="item"><img  src="{{ RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage()) }}" class="showfullimg" rel="{{ $loop->index }}" alt="{{ $property->name }}"></div>
                @endforeach
            </div>
        </div>
        <!-- author id -->
        <input type="hidden" id="property_id" name="property_id" value="{{ $property->author->id }}"/>
        <div class="slidebot">
            <div style="max-width: 800px; margin: 0 auto;">
                <div class="owl-carousel" id="listcarouselthumb">
                    @foreach($property->images as $image)
                        <div class="item cthumb" rel="{{ $loop->index }}"><img  src="{{ RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage()) }}" class="showfullimg" rel="{{ $loop->index }}" alt="{{ $property->name }}"></div>
                    @endforeach
                </div>
                <i class="fas fa-chevron-right ar-next"></i>
                <i class="fas fa-chevron-left ar-prev"></i>
            </div>
        </div>
    </div>
    <div id="gallery" data-images="{{ json_encode($images) }}"></div>
    <input type="hidden" id="latitude" name="latitude" value="{{$property->latitude}}" />
    <input type="hidden" id="longitude" name="longitude" value="{{$property->longitude}}"/>

    <div class="container-fluid w90 padtop20">
        <div class="row">
            <div class="col-md-9">
                <div class="boxright">
                    <div class="row">
                        <div class="col-md-6">

                            <h1 class="property-price">{{ format_price($property->price, $property->currency) }}</h1>
                        </div>
                        <div class="col-md-6">
                            <div class="float-right">

                                <a href="#" class="text-brown heart add-to-wishlist" data-id="{{ $property->id }}" title="{{ __('I care about this property!!!') }}"><i class="far fa-heart fa-3x"></i></a>
                                <p>Favourite</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <h2 class="titlehouse property_name" id="house-40396">{{ $property->name }}</h2>
                            <p class="addresshouse"> {{ $property->location }}</p>

                        </div>
                        <div class="col-md-4">
                            <div class="row float-right">
                            <!-- <div class="col-md-12">
                                    <div class="d-flex align-items-center float-right">
                                        <h3 class="text-brown pl-1">{{ number_format($property->number_bedroom) }}</h3><a href="#" class="text-brown pl-1"  title="{{ __('Number of rooms') }}"><i class="far fa-bed fa-2x"></i></a>
                                        <p>Favourite</p>
                                        <h3 class="text-brown pl-4">{{ number_format($property->number_bathroom) }}</h3><a href="#" class="text-brown pl-1"  title="{{ __('Number of rest rooms') }}"><i class="far fa-bath fa-2x"></i></a>
                                        <p>Favourite</p>
                                    </div>


                                </div>-->
                                @if($property->category->id==1 || $property->category->parent_id==1)
                                <div class="col-md-6">
                                    <ul>
                                        <li><span>{{ number_format($property->number_bedroom) }} <a  class="text-brown pl-1"  title="{{ __('Number of rooms') }}"><i class="far fa-bed fa-2x"></i></a></span> </li>

                                        <li><p>Bedroom</p></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul>
                                        <li><span>{{ number_format($property->number_bathroom) }}<a  class="text-brown pl-1"  title="{{ __('Number of rest rooms') }}"><i class="far fa-bath fa-2x"></i></a></span></li>

                                        <li><p>Bathroom</p></li>
                                    </ul>
                                </div>
                                @endif

                            </div>

                        </div>
                    </div>
                    <br>
                    {!! Theme::partial('share', ['title' => __('Share this property'), 'description' => $property->name]) !!}
                </div>
               <!-- <section class="boxright">
                    <div class="row">
                        <div class="col-md-6">
                            <h2>$129,000</h2>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li><a href="#" class="text-brown heart add-to-wishlist" data-id="{{ $property->id }}" title="{{ __('I care about this property!!!') }}"><i class="far fa-heart"></i></a></li>
                                <li><p>Favourite</p></li>
                            </ul>
                        </div>
                    </div>
                </section>-->

                <div class="boxright">
                    <div class="row">
                        <div class="col property_tab">
                            <div class="tab" role="tabpanel">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#Section1" aria-controls="home" class="showGrid mr-3 ml-3" data-tab-id="1" role="tab"  data-toggle="tab"><i class="fa fa-star"></i> Highlights</a></li>
                                    <li role="presentation"><a href="#Section2" aria-controls="profile" role="tab"   class="showGrid mr-3 ml-3" data-tab-id="2" data-toggle="tab"><i class="fa fa-home"></i> Neighbourhood</a></li>
                                    <li role="presentation"><a href="#Section3" aria-controls="messages" role="tab"  class="showGrid mr-3 ml-3"  data-tab-id="3"  data-toggle="tab"><i class="fa fa-calculator"></i> Calculator</a></li>
                                    <li role="presentation"><a href="#Section4" aria-controls="messages" role="tab"   class="showGrid mr-3 ml-3" data-tab-id="4" data-toggle="tab"><i class="fas fa-chart-bar"></i> Statistics</a></li>

                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content tabs">
                                    <div role="tabpanel" class="tab-pane fade in active show" id="Section1">
                                        <h3 class="pt-3 primary-head">{{ __('Description') }}</h3>


                                        <p> {!! clean($property->description, 'youtube') !!}</p>

                                            <h3 class="primary-head">{{ __('Property Summary') }}</h3>
                                        <div class="row pt-3">
                                            <div class="col-md-3">
                                                <b class="">{{ __('Property Type') }}</b><p>{{ $property->category->name }}</p>
                                            </div>
                                            <div class="col-md-3"> <b class="">{{ __('Land Size') }}</b><div class="infoTitle d-none"></div><input type="hidden" id="area_units" name="area_units" value="{{setting('real_estate_square_unit') }}"/><input type="hidden" id="square" name="square" value="{{ $property->square }}"/> <p class="showInfoArea" style="cursor: pointer"> {{ $property->square_text }}</p></div>

                                            <div class="col-md-3">
                                                <b>{{ __('Built in') }}</b>
                                                <p>{{ $property->built_in ? $property->built_in : __('Not Available') }}</p>
                                            </div>

                                            <div class="col-md-3"><b class="">{{ __('Added At') }}</b><p>{{ $property->created_at }}</p></div><!-- later add parting type-->

                                        </div>
                                        @if ($property->features->count())
                                            <h3 class="primary-head">{{ __('Features') }}</h3>
                                            <div class="row">
                                                @foreach($property->features as $feature)
                                                    <div class="col-sm-4">
                                                        <p><i class="fas fa-check text-orange text0i"></i>  {{ $feature->name }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <br>

                                       </div>
                                    <div role="tabpanel" class="tab-pane fade" id="Section2">

                                        {!! Theme::partial('neighbourhood') !!}
                                        @if ($property->facilities->count())
                                             <div class="row pt-5">
                                                <div class="col-sm-12">
                                                    <h5 class="primary-head">{{ __('Distance key between facilities') }}</h5>
                                                    <div class="row">
                                                        @foreach($property->facilities as $facility)
                                                            @if($facility->pivot->distance)
                                                                <div class="col-sm-4">
                                                                    <p><i class="@if ($facility->icon) {{ $facility->icon }} @else fas fa-check @endif text-brown text0i"></i>  {{ $facility->name }} - {{ $facility->pivot->distance }} Km</p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade" id="Section3">
                                        {!! Theme::partial('mortage_calculator') !!}

                                    </div>
                                    <div role="tabpanel" class="tab-pane fade" id="Section4">
                                        Comming Soon

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
<!--                <div class="boxshadow mb-5 background-gray  light-gray-border">
                    <div class="row p-5">
                            <div class="col-md-4">
                                    <h4 class="primary-head">Building</h4>
                                    <h6>Bathrooms</h6>
                                    <div class="row pt-3">
                                        <div class="col-md-6"><label>Total</label><p>{{ number_format($property->number_bathroom) }}</p></div>
                                        <div class="col-md-6"><label>Partial</label><p>0</p></div>
                                    </div>
                            </div>
                            <div class="col-md-4">
                                     <h4 class="primary-head">Interior Features</h4>
                                <div class="row pt-3">
                                    <div class="col-md-6"><label>Basement Features</label><p>Six feet and over</p></div>
                                    <div class="col-md-6"><label>Basement Type</label><p>Full (Unfinished)</p></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h4 class="primary-head">Building Features</h4>
                                <div class="row pt-3">
                                    <div class="col-md-6"><label>Features</label><p>PVC window, Sliding windows, Hung windows (Guillotine), Non Paved Driveway</p></div>
                                    <div class="col-md-6"><label>Foundation Type</label><p>Stone</p></div>
                                </div>

                            </div>
                    </div>
                    <div class="bg-white light-gray-border">
                    <div class="row  p-5">
                        <div class="col-md-4">
                            <h4 class="primary-head">Heating & Cooling</h4>
                             <div class="row pt-3">
                                <div class="col-md-6"><label>Heating Type</label><p>Electricity</p></div>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <h4 class="primary-head">Utilities</h4>
                            <div class="row pt-3">
                                <div class="col-md-6"><label>Utility Sewer</label><p>Six feet and over</p></div>
                                <div class="col-md-6"><label>Water</label><p>Municipal water</p></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h4 class="primary-head">Features</h4>
                            <div class="row pt-3">
                                <div class="col-md-6"><label>Wifi</label><p>Security</p></div>
                                <div class="col-md-6"><label>Parking</label></div>

                            </div>

                        </div>
                    </div>
                    </div>
                    <div class="background-gray light-gray-border ">
                    <div class="row p-5">
                        <div class="col-md-4">
                            <h4 class="primary-head">Exterior Features</h4>
                          <div class="row pt-3">
                                <div class="col-md-6"><label>Roof Style</label><p>Steel</p></div>
                                <div class="col-md-6"><label>Exterior Finish</label><p>Aluminum siding, Vinyl</p></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h4 class="primary-head">Land</h4>
                            <div class="row pt-3">
                                <div class="col-md-6"><label>Lot Features</label><p>156.3 m</p></div>
                                <div class="col-md-6"><label>Zoning Type</label><p>Residential</p></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h4 class="primary-head">Building Features</h4>
                            <div class="row pt-3">
                                <div class="col-md-6"><label>Features</label><p>PVC window, Sliding windows, Hung windows (Guillotine), Non Paved Driveway</p></div>
                                <div class="col-md-6"><label>Foundation Type</label><p>Stone</p></div>
                            </div>

                        </div>
                    </div>
                    </div>
                </div>-->
{{--                <div class="boxright">--}}



                @if ($property->latitude)

                <div class="mapouter">
                    <div class="gmap_canvas">
                        <iframe id="gmap_canvas" width="100%" height="500"
                                src="https://maps.google.com/maps?q={{$property->latitude }},{{$property->longitude }}%20&t=&z=13&ie=UTF8&iwloc=&output=embed"
                                frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                    </div>
                </div>
                @endif

{{--                </div>--}}
            </div>
            <div class="col-md-3">
                @if ($property->author->id)
                    <div class="boxright">
                        <div class="head">
                            {{ __('Contact Agent') }}
                        </div>

                        <div class="row rowm10 itemagent">
                            <div class="col-sm-3 colm10">
                                @if ($property->author->username)
                                    <a href="{{ route('public.agent', $property->author->username) }}">
                                        <img src="{{ $property->author->avatar_url }}" alt="{{ $property->author->getFullName() }}" class="img-thumbnail">
                                    </a>
                                @else
                                    <img src="{{ $property->author->avatar_url }}" alt="{{ $property->author->getFullName() }}" class="img-thumbnail">
                                @endif
                            </div>
                            <div class="col-sm-8 colm10">
                                <div class="info">
                                    <p>
                                        <strong>
                                            @if ($property->author->username)
                                                <a href="{{ route('public.agent', $property->author->username) }}">{{ $property->author->getFullName() }}</a>
                                            @else
                                                {{ $property->author->getFullName() }}
                                            @endif
                                        </strong>
                                    </p>
                                    <button type="button" data-id="{{$property->author->id}}" class="showContact btn btn-info pt-1 pb-1">Show Contact <i class="fa fa-spinner d-none" aria-hidden="true"></i></button>


                                </div>

                            </div>
                        </div>
                        <div class="row rowm10 itemagent mt-3">
                            <div class="col">
                                <div class="showinfo contactInfo d-none">
                                    <p class="mobile mobile-p d-none"> <i class="fa fa-phone mr-1"></i><span id="mobile_text"></span> </p>
                                    <p><i class="fa fa-envelope mr-1" aria-hidden="true"></i><span id="email_text"></span> </p>
                                </div>
                            </div>
                        </div>
                        @if ($property->author->username)
                            <p class="mt-2"><span class="fas fa-arrow-circle-right"></span> <a href="{{ route('public.agent', $property->author->username) }}">{{ __('More properties by this agent') }}</a></p>
                        @endif
                    </div>
                @endif
                <div class="boxright">
                    {!! Theme::partial('consult-form', ['type' => 'property', 'data' => $property]) !!}
                </div>

            </div>
        </div>
        <h5 class="headifhouse mt-5">{{ __('Related properties') }}</h5>
        <related type="related" url="{{ route('public.ajax.properties') }}" property_id="{{ $property->id }}" category_id="{{ $property->category_id }}" city_id="{{ $property->city_id }}" ></related>
        <br>
        <br>
        <br>

    </div>
</main>
