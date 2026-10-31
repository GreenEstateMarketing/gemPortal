<main class="detailproject property-detail" style="background: #fff;">
    <div class="boxsliderdetail">
        <div class="slidetop">
            <div class="owl-carousel" id="listcarousel">
                @foreach ($property->images as $image)
                    <div class="item"><img src="{{ RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage()) }}"
                            class="showfullimg" rel="{{ $loop->index }}" alt="{{ $property->name }}"></div>
                @endforeach
            </div>
        </div>
        <!-- author id -->
        <input type="hidden" id="property_id" name="property_id" value="{{ $property->author->id }}" />
        <div class="slidebot">
            <div style="max-width: 800px; margin: 0 auto;">
                <div class="owl-carousel" id="listcarouselthumb">
                    @foreach($property->images as $image)
                        <div class="item cthumb" rel="{{ $loop->index }}"><img
                                src="{{ RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage()) }}"
                                class="showfullimg" rel="{{ $loop->index }}" alt="{{ $property->name }}"></div>
                    @endforeach
                </div>
                <i class="fas fa-chevron-right ar-next"></i>
                <i class="fas fa-chevron-left ar-prev"></i>
            </div>
        </div>
    </div>
    <div id="gallery" data-images="{{ json_encode($images) }}"></div>
    <input type="hidden" id="latitude" name="latitude" value="{{$property->latitude}}" />
    <input type="hidden" id="longitude" name="longitude" value="{{$property->longitude}}" />

    <div class="container-fluid w90 padtop20">
        <div class="row">
            <div class="col-md-9">
                <div class="boxright property-detail__header">
                    <div class="property-detail__price-row">
                        <div class="property-detail__price-group">
                            <h1 class="property-price property-detail__price">{{ format_price($property->price, $property->currency) }}</h1>
                            <span class="property-detail__status">{!! $property->status->toHtml() !!}</span>
                        </div>

                        <a href="#" class="text-brown heart add-to-wishlist property-detail__favorite" data-id="{{ $property->id }}"
                            data-is-favourite="{{ $isFavourite ? 'true' : 'false' }}"
                            title="{{ __('I care about this property!!!') }}">
                            <i class="{{ $isFavourite ? 'fas fa-heart' : 'far fa-heart' }}"></i>
                        </a>
                    </div>

                    <h2 class="titlehouse property_name property-detail__title" id="house-40396">{{ $property->name }}</h2>
                    <p class="addresshouse property-detail__location"><i class="fas fa-map-marker-alt"></i> {{ $property->location }}</p>

                    <div class="property-detail__share">
                        {!! Theme::partial('share', ['title' => __('Share this property'), 'description' => $property->name]) !!}
                    </div>

                    <div class="property-detail__stats">
                        @if($property->category->id == 1 || $property->category->parent_id == 1)
                            <div class="property-detail__stat">
                                <span class="property-detail__stat-icon"><i class="far fa-bed"></i></span>
                                <span class="property-detail__stat-value">{{ number_format($property->number_bedroom) }}</span>
                                <span class="property-detail__stat-label">{{ __('Bedroom') }}</span>
                            </div>
                            <div class="property-detail__stat">
                                <span class="property-detail__stat-icon"><i class="far fa-bath"></i></span>
                                <span class="property-detail__stat-value">{{ number_format($property->number_bathroom) }}</span>
                                <span class="property-detail__stat-label">{{ __('Bathroom') }}</span>
                            </div>
                        @endif
                        <div class="property-detail__stat">
                            <span class="property-detail__stat-icon"><i class="fas fa-ruler-combined"></i></span>
                            <span class="property-detail__stat-value">{{ $property->square_text }}</span>
                            <span class="property-detail__stat-label">{{ __('Area') }}</span>
                        </div>
                        <div class="property-detail__stat">
                            <span class="property-detail__stat-icon"><i class="far fa-calendar-alt"></i></span>
                            <span class="property-detail__stat-value">{{ $property->built_in ?: __('N/A') }}</span>
                            <span class="property-detail__stat-label">{{ __('Built In') }}</span>
                        </div>
                        <div class="property-detail__stat">
                            <span class="property-detail__stat-icon"><i class="fas fa-star"></i></span>
                            @if ($propertyRatingCount)
                                <span class="property-detail__stat-value">{{ number_format($propertyRatingAvg, 1) }}</span>
                                <span class="property-detail__stat-label">{{ trans_choice(':count Review|:count Reviews', $propertyRatingCount, ['count' => $propertyRatingCount]) }}</span>
                            @else
                                <span class="property-detail__stat-value">&mdash;</span>
                                <span class="property-detail__stat-label">{{ __('No reviews yet') }}</span>
                            @endif
                        </div>
                    </div>
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

                <div class="boxright property-detail__tabs-card">
                    <div class="row">
                        <div class="col property_tab">
                            <div class="tab" role="tabpanel">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs property-detail__tab-nav" role="tablist">
                                    <li role="presentation" class="active"><a href="#Section1" aria-controls="home"
                                            class="showGrid mr-3 ml-3 active" data-tab-id="1" role="tab" data-toggle="tab"><i
                                                class="fa fa-star"></i> Highlights</a></li>
                                    <li role="presentation"><a href="#Section2" aria-controls="profile" role="tab"
                                            class="showGrid mr-3 ml-3" data-tab-id="2" data-toggle="tab"><i
                                                class="fa fa-home"></i>
                                            Neighbourhood</a></li>
                                    <li role="presentation"><a href="#Section3" aria-controls="messages" role="tab"
                                            class="showGrid mr-3 ml-3" data-tab-id="3" data-toggle="tab"><i
                                                class="fa fa-calculator"></i>
                                            Calculator</a></li>
                                    <li role="presentation"><a href="#Section4" aria-controls="messages" role="tab"
                                            class="showGrid mr-3 ml-3" data-tab-id="4" data-toggle="tab"><i
                                                class="fas fa-chart-bar"></i>
                                            Statistics</a></li>

                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content tabs">
                                    <div role="tabpanel" class="tab-pane fade in active show property-detail__tab-panel" id="Section1">
                                        <h3 class="pt-3 primary-head property-detail__section-title">{{ __('Description') }}</h3>

                                        <div class="property-detail__quote">
                                            <i class="fas fa-home"></i>
                                            <p>{{ __('A perfect blend of comfort, style and convenience in a prime location!') }}</p>
                                        </div>

                                        <p class="property-detail__description"> {!! clean($property->description, 'youtube') !!}</p>

                                        <h3 class="primary-head property-detail__section-title">{{ __('Property Summary') }}</h3>
                                        <div class="property-detail__summary-grid">
                                            <div class="property-detail__summary-item">
                                                <b><i class="fas fa-home"></i> {{ __('Property Type') }}</b>
                                                <p>{{ $property->category->name }}</p>
                                            </div>
                                            <div class="property-detail__summary-item">
                                                <b><i class="fas fa-ruler-combined"></i> {{ __('Land Size') }}</b>
                                                <div class="infoTitle d-none"></div>
                                                <input type="hidden" id="area_units" name="area_units"
                                                    value="{{setting('real_estate_square_unit') }}" /><input
                                                    type="hidden" id="square" name="square"
                                                    value="{{ $property->square }}" />
                                                <p class="showInfoArea" style="cursor: pointer">
                                                    {{ $property->square_text }}
                                                </p>
                                            </div>

                                            <div class="property-detail__summary-item">
                                                <b><i class="far fa-calendar-alt"></i> {{ __('Built in') }}</b>
                                                <p>{{ $property->built_in ? $property->built_in : __('Not Available') }}
                                                </p>
                                            </div>

                                            <div class="property-detail__summary-item">
                                                <b><i class="far fa-clock"></i> {{ __('Added At') }}</b>
                                                <p>{{ $property->created_at }}</p>
                                            </div><!-- later add parting type-->

                                        </div>
                                        @if ($property->features->count())
                                            <h3 class="primary-head property-detail__section-title">{{ __('Features') }}</h3>
                                            <div class="property-detail__features-grid">
                                                @foreach($property->features as $feature)
                                                    <div class="property-detail__feature">
                                                        <i class="@if ($feature->icon) {{ $feature->icon }} @else fas fa-check @endif"></i>
                                                        {{ $feature->name }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <br>

                                    </div>
                                    <div role="tabpanel" class="tab-pane fade property-detail__tab-panel property-detail__neighbourhood" id="Section2">

                                        {!! Theme::partial('neighbourhood') !!}
                                        @if ($property->facilities->count())
                                            <div class="row pt-5">
                                                <div class="col-sm-12">
                                                    <h5 class="primary-head property-detail__section-title">{{ __('Distance key between facilities') }}
                                                    </h5>
                                                    <div class="property-detail__facilities-grid">
                                                        @foreach($property->facilities as $facility)
                                                            @if($facility->pivot->distance)
                                                                <div class="property-detail__feature">
                                                                    <i
                                                                        class="@if ($facility->icon) {{ $facility->icon }} @else fas fa-check @endif"></i>
                                                                    {{ $facility->name }}
                                                                    &mdash; {{ $facility->pivot->distance }}
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade property-detail__tab-panel" id="Section3">
                                        <div class="property-detail__calculator">
                                            {!! Theme::partial('mortage_calculator', ['property' => $property]) !!}
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade property-detail__tab-panel" id="Section4">
                                        <div class="property-detail__empty-state">
                                            <i class="fas fa-chart-bar"></i>
                                            <h4>{{ __('Statistics coming soon') }}</h4>
                                            <p>{{ __("We're building detailed market and pricing insights for this property. Check back soon.") }}</p>
                                        </div>
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
                {{-- <div class="boxright">--}}



                    @if ($property->latitude)

                        <div class="mapouter property-detail__map-card">
                            <div class="gmap_canvas">
                                <iframe id="gmap_canvas" width="100%" height="500"
                                    src="https://maps.google.com/maps?q={{$property->latitude }},{{$property->longitude }}%20&t=&z=13&ie=UTF8&iwloc=&output=embed"
                                    frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                            </div>
                        </div>
                    @endif

                    {{--
                </div>--}}
            </div>
            <div class="col-md-3">
                @if ($property->author->id)
                    <div class="boxright property-detail__agent-card">
                        <div class="property-detail__agent-head">
                            <i class="fas fa-user-tie"></i>
                            <div>
                                <strong>{{ __('Contact Agent') }}</strong>
                                <span>{{ __('Get in touch for more details') }}</span>
                            </div>
                        </div>

                        @php
                            // image_path (a real uploaded photo) takes priority over
                            // avatar_url, which only auto-generates an initials avatar
                            // when no real photo/avatar MediaFile is set (Account::
                            // getAvatarUrlAttribute()) - same precedence agent.blade.php
                            // already uses, so the agent's actual photo shows instead of
                            // a generic placeholder whenever one exists.
                            $agentPhotoUrl = $property->author->image_path
                                ? Storage::url($property->author->image_path)
                                : $property->author->avatar_url;
                        @endphp
                        <div class="property-detail__agent-body">
                            <div class="property-detail__agent-row">
                                @if ($property->author->username)
                                    <a href="{{ route('public.agent', $property->author->username) }}" class="property-detail__agent-avatar-link">
                                        <img src="{{ $agentPhotoUrl }}"
                                            alt="{{ $property->author->getFullName() }}" class="property-detail__agent-avatar">
                                    </a>
                                @else
                                    <img src="{{ $agentPhotoUrl }}" alt="{{ $property->author->getFullName() }}"
                                        class="property-detail__agent-avatar">
                                @endif

                                <div class="property-detail__agent-details">
                                    @if ($property->author->username)
                                        <a href="{{ route('public.agent', $property->author->username) }}"
                                            class="property-detail__agent-name">{{ $property->author->getFullName() }}</a>
                                    @else
                                        <span class="property-detail__agent-name">{{ $property->author->getFullName() }}</span>
                                    @endif
                                    @if ($property->author->confirmed_at)
                                        <span class="property-detail__agent-badge"><i class="fas fa-shield-alt"></i> {{ __('Trusted Agent') }}</span>
                                    @endif

                                    <div class="property-detail__agent-info">
                                        @if ($property->author->phone)
                                            <p><i class="fa fa-phone" aria-hidden="true"></i><span>{{ $property->author->phone }}</span></p>
                                        @endif
                                        @if ($property->author->email)
                                            <p><i class="fa fa-envelope" aria-hidden="true"></i><span>{{ $property->author->email }}</span></p>
                                        @endif
                                    </div>

                                    @if ($property->author->username)
                                        <p class="property-detail__agent-more"><i class="fas fa-arrow-circle-right"></i> <a
                                                href="{{ route('public.agent', $property->author->username) }}">{{ __('More properties by this agent') }}</a>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="boxright property-detail__consult">
                    {!! Theme::partial('consult-form', ['type' => 'property', 'data' => $property]) !!}
                </div>

                <div class="property-detail__dream-cta"
                    style="background-image: url('{{ Theme::asset()->url('images/property-detail-dream-home-bg.png') }}')">
                    <div class="property-detail__dream-cta-overlay"></div>
                    <div class="property-detail__dream-cta-inner">
                        <h3>{{ __('Your Dream Home') }} <span>{{ __('Awaits!') }}</span></h3>
                        <p>{{ __('Find the perfect property with GEM.') }}</p>
                        <a href="{{ route('public.properties') }}" class="property-detail__dream-cta-btn">
                            {{ __('Browse More Properties') }} <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
        <div class="property-detail__related-header">
            <h5 class="headifhouse">{{ __('Related properties') }}</h5>
            <a href="{{ route('public.properties') }}">{{ __('View All Properties') }} <i class="fas fa-arrow-right"></i></a>
        </div>
        <related type="related" url="{{ route('public.ajax.properties') }}" property_id="{{ $property->id }}"
            category_id="{{ $property->category_id }}" city_id="{{ $property->city_id }}"></related>
        <br>
        <br>
        <br>

    </div>
</main>


<script>
    $(document).on('click', '.add-to-wishlist', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        console.log('MY FAVOURITE HANDLER');

        const $button = $(this);
        const itemId = $button.data('id');

        // Prevent multiple requests while one is already running
        if ($button.data('loading')) {
            return;
        }

        // Get current favourite state
        const currentState = $button.attr('data-is-favourite') === 'true';

        // Calculate the state we want after this click
        const newState = !currentState;

        const url = "/ajax/toggle-favourite-property/" + itemId;

        // Mark request as in progress
        $button.data('loading', true);

        $.ajax({
            url: url,
            type: 'GET',
            data: {
                is_favourite: newState
            },

            success: function (response) {

                // User is not authenticated
                if (
                    response.error &&
                    response.message === 'UserNotAuthenticated'
                ) {
                    window.location.href = '/member-login';
                    return;
                }

                // Other backend errors
                if (response.error) {
                    console.error(
                        'Unable to update favourite:',
                        response.message
                    );
                    return;
                }

                const $icon = $button.find('i');

                // Update heart icon based on the new state
                if (newState) {
                    $icon
                        .removeClass('far')
                        .addClass('fas');
                } else {
                    $icon
                        .removeClass('fas')
                        .addClass('far');
                }

                // Store the new state on the button
                $button.attr(
                    'data-is-favourite',
                    newState ? 'true' : 'false'
                );
            },

            error: function (xhr) {
                console.error(
                    'Unable to toggle favourite property.',
                    xhr
                );
            },

            complete: function () {
                // Allow clicking again after request finishes
                $button.data('loading', false);
            }
        });
    });
</script>