<main class="detailproject" style="background: #fff;">
    <div class="boxsliderdetail">
        <div class="slidetop">
            <div class="owl-carousel" id="listcarousel">
                @foreach ($project->images as $image)
                    <div class="item"><img  src="{{ RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage()) }}" class="showfullimg" rel="{{ $loop->index }}" alt="{{ $project->name }}"></div>
                @endforeach
            </div>
        </div>
        <!-- author id -->
        <input type="hidden" id="property_id" name="property_id" value="{{ $project->author->id }}"/>
        <div class="slidebot">
            <div style="max-width: 800px; margin: 0 auto;">
                <div class="owl-carousel" id="listcarouselthumb">
                    @foreach($project->images as $image)
                        <div class="item cthumb" rel="{{ $loop->index }}"><img  src="{{ RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage()) }}" class="showfullimg" rel="{{ $loop->index }}" alt="{{ $project->name }}"></div>
                    @endforeach
                </div>
                <i class="fas fa-chevron-right ar-next"></i>
                <i class="fas fa-chevron-left ar-prev"></i>
            </div>
        </div>
    </div>
    <div id="gallery" data-images="{{ json_encode($images) }}"></div>
    <input type="hidden" id="latitude" name="latitude" value="{{$project->latitude}}" />
    <input type="hidden" id="longitude" name="longitude" value="{{$project->longitude}}"/>

    <div class="container-fluid w90 padtop20">
        <div class="row">
            <div class="col-md-9">
                <div class="boxright">
                    <div class="row">
                        <div class="col-md-6">

                        <!--                            <h1 class="property-price">{{ format_price($project->price, $project->currency) }}</h1>-->
                            @if ($project->price_from || $project->price_to)
                                <div><span>{{ __('Price') }}:</span> <b>@if ($project->price_from) <span class="from">{{ __('From') }}</span> {{ format_price($project->price_from, $project->currency, false)  }} @endif @if ($project->price_to) - {{ format_price($project->price_to, $project->currency) }} @endif</b></div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="float-right">

                                <a href="#" class="text-brown heart add-to-wishlist" data-id="{{ $project->id }}" title="{{ __('I care about this property!!!') }}"><i class="far fa-heart fa-3x"></i></a>
                                <p>Favourite</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <h2 class="titlehouse property_name" id="house-40396">{{ $project->name }}</h2>
                            <p class="addresshouse"> {{ $project->location }}</p>

                        </div>
                        <div class="col-md-4">
                            <div class="row float-right">
                            <!-- <div class="col-md-12">
                                    <div class="d-flex align-items-center float-right">
                                        <h3 class="text-brown pl-1">{{ number_format($project->number_bedroom) }}</h3><a href="#" class="text-brown pl-1"  title="{{ __('Number of rooms') }}"><i class="far fa-bed fa-2x"></i></a>
                                        <p>Favourite</p>
                                        <h3 class="text-brown pl-4">{{ number_format($project->number_bathroom) }}</h3><a href="#" class="text-brown pl-1"  title="{{ __('Number of rest rooms') }}"><i class="far fa-bath fa-2x"></i></a>
                                        <p>Favourite</p>
                                    </div>


                                </div>-->
                                @if($project->category->id==1 || $project->category->parent_id==1)
                                <div class="col-md-6">
                                    <ul class="fa-ul">
                                        <li><span class="fa-li text-brown">{{ number_format($project->number_flat) }}</span><span class="text-brown"> <i class="far fa-building fa-2x"></i></span></li>
                                        <li>flat</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="fa-ul">
                                        <li><span class="fa-li text-brown">{{ number_format($project->number_floor) }}</span><span class="text-brown"> <i class="far fa-building fa-2x"></i></span></li>
                                        <li>floor</li>
                                    </ul>
                                </div>
                                @else
                                    <div class="col-md-6">
                                        <ul class="fa-ul">
                                            <li><span class="fa-li text-brown">{{ number_format($project->number_floor) }}</span><span class="text-brown"> <i class="far fa-building fa-2x"></i></span></li>
                                            <li>floor</li>
                                        </ul>
                                    </div>
                                    @endif

                            </div>

                        </div>
                    </div>
                    <br>
                    {!! Theme::partial('share', ['title' => __('Share this project'), 'description' => $project->name]) !!}

                </div>
            <!-- <section class="boxright">
                    <div class="row">
                        <div class="col-md-6">
                            <h2>$129,000</h2>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li><a href="#" class="text-brown heart add-to-wishlist" data-id="{{ $project->id }}" title="{{ __('I care about this property!!!') }}"><i class="far fa-heart"></i></a></li>
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

                                        <!--Template Description -->
                                        @if($project->category->name!="Plots" || $project->category->parent_id==2)
                                            <p>This project features {{$project->number_flat}} Flats with {{$project->number_floor}} floors.</p>
                                        @else
                                            <p> {!! clean($project->content, 'youtube') !!};</p>
                                        @endif
                                        <h3 class="primary-head">{{ __('Project Summary') }}</h3>
                                        <div class="row pt-3">
                                            <div class="col-md-3">
                                                <b class="">{{ __('Category ') }}</b><p>{{ $project->category->name }}</p>
                                            </div>
                                            <div class="col-md-3"> <b class="">{{ __('No. of blocks') }}</b><p> {{ $project->number_block }}</p></div>

                                            <div class="col-md-3"><b class="">{{ __('Built in') }}</b><p> 2005</p></div><!-- later add year built in-->

                                            <div class="col-md-3"><b class="">{{ __('Added At') }}</b><p>{{ $project->created_at }}</p></div><!-- later add parting type-->

                                        </div>
                                        @if ($project->features->count())
                                            <h3 class="primary-head">{{ __('Features') }}</h3>
                                            <div class="row">
                                                @foreach($project->features as $feature)
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
                                        @if ($project->facilities->count())
                                            <div class="row pt-5">
                                                <div class="col-sm-12">
                                                    <h5 class="primary-head">{{ __('Distance key between facilities') }}</h5>
                                                    <div class="row">
                                                        @foreach($project->facilities as $facility)
                                                            <div class="col-sm-4">
                                                                <p><i class="@if ($facility->icon) {{ $facility->icon }} @else fas fa-check @endif text-brown text0i"></i>  {{ $facility->name }} - {{ $facility->pivot->distance }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                   @if(isset($property))
    {!! Theme::partial('mortage_calculator', compact('property')) !!}
@endif
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
                                        <div class="col-md-6"><label>Total</label><p>{{ number_format($project->number_bathroom) }}</p></div>
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



                @if ($project->latitude)

                    <div class="mapouter">
                        <div class="gmap_canvas">
                            <iframe id="gmap_canvas" width="100%" height="500"
                                    src="https://maps.google.com/maps?q={{$project->latitude }},{{$project->longitude }}%20&t=&z=13&ie=UTF8&iwloc=&output=embed"
                                    frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                        </div>
                    </div>
                @endif

                {{--                </div>--}}
            </div>
            <div class="col-md-3">
                @if ($project->author->id)
                    <div class="boxright">
                        <div class="head">
                            {{ __('Contact Agent') }}
                        </div>

                        <div class="row rowm10 itemagent">
                            <div class="col-sm-3 colm10">
                                @if ($project->author->username)
                                    <a href="{{ route('public.agent', $project->author->username) }}">
                                        <img src="{{ $project->author->avatar_url }}" alt="{{ $project->author->getFullName() }}" class="img-thumbnail">
                                    </a>
                                @else
                                    <img src="{{ $project->author->avatar_url }}" alt="{{ $project->author->getFullName() }}" class="img-thumbnail">
                                @endif
                            </div>
                            <div class="col-sm-8 colm10">
                                <div class="info">
                                    <p>
                                        <strong>
                                            @if ($project->author->username)
                                                <a href="{{ route('public.agent', $project->author->username) }}">{{ $project->author->getFullName() }}</a>
                                            @else
                                                {{ $project->author->getFullName() }}
                                            @endif
                                        </strong>
                                    </p>
                                    <button type="button" data-id="{{ $project->author->id}}" class="showContact btn btn-info pt-1 pb-1">Show Contact <i class="fa fa-spinner d-none" aria-hidden="true"></i></button>


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
                        @if ($project->author->username)
                            <p class="mt-2"><span class="fas fa-arrow-circle-right"></span> <a href="{{ route('public.agent', $project->author->username) }}">{{ __('More properties by this agent') }}</a></p>
                        @endif
                    </div>
                @endif
                <div class="boxright">
                    {!! Theme::partial('consult-form', ['type' => 'project', 'data' => $project]) !!}
                </div>

            </div>
        </div>
        <br>
        <br>
        <br>

    </div>
</main>
