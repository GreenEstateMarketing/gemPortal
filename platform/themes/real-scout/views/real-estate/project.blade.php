<main class="detailproject project-detail" style="background: #fff;">
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
                <div class="boxright project-detail__header">
                    @if ($project->price_from || $project->price_to)
                        <div class="project-detail__price-pill">
                            <span>{{ __('Price') }}:</span>
                            <strong>@if ($project->price_from) {{ __('From') }} {{ format_price($project->price_from, $project->currency, false)  }} @endif @if ($project->price_to) - {{ format_price($project->price_to, $project->currency) }} @endif</strong>
                        </div>
                    @endif

                    <div class="project-detail__header-body">
                        <div class="project-detail__header-main">
                            <h2 class="titlehouse property_name project-detail__title" id="house-40396">
                                {{ $project->name }}
                                @if ($project->status)
                                    @php
                                        $statusBadgeClass = [
                                            'not_available' => 'badge-secondary',
                                            'pre_sale' => 'badge-warning',
                                            'selling' => 'badge-success',
                                            'sold' => 'badge-danger',
                                            'building' => 'badge-info',
                                            'upcoming' => 'badge-warning',
                                            'under_construction' => 'badge-info',
                                            'completed' => 'badge-success',
                                        ][$project->status->getValue()] ?? 'badge-secondary';
                                    @endphp
                                    <span class="badge {{ $statusBadgeClass }} align-middle ml-2">{{ $project->status->label() }}</span>
                                @endif
                                @if ($project->is_verified)
                                    <span class="badge badge-success align-middle ml-1"><i class="fas fa-check-circle"></i> {{ __('Verified') }}</span>
                                @endif
                            </h2>
                            <p class="addresshouse project-detail__location"><i class="fas fa-map-marker-alt"></i> {{ $project->location }}</p>
                            @if ($project->investor && $project->investor->name)
                                <p class="project-detail__developed-by">{{ __('Developed by') }} <strong>{{ $project->investor->name }}</strong></p>
                            @endif

                            <div class="project-detail__share">
                                {!! Theme::partial('share', ['title' => __('Share this project'), 'description' => $project->name]) !!}
                            </div>
                        </div>

                        <div class="project-detail__header-side">
                            <a href="#" class="text-brown heart add-to-wishlist project-detail__favorite" data-id="{{ $project->id }}" title="{{ __('I care about this property!!!') }}">
                                <i class="far fa-heart"></i>
                            </a>
                            <div class="project-detail__side-stats">
                                @if($project->category->id==1 || $project->category->parent_id==1)
                                    @if ($project->number_flat)
                                        <div class="project-detail__side-stat">
                                            <i class="far fa-building"></i>
                                            <strong>{{ number_format($project->number_flat) }}</strong>
                                            {{ \Illuminate\Support\Str::plural(__('Flat'), $project->number_flat) }}
                                        </div>
                                    @endif
                                @endif
                                @if ($project->number_floor)
                                    <div class="project-detail__side-stat">
                                        <i class="far fa-building"></i>
                                        <strong>{{ number_format($project->number_floor) }}</strong>
                                        {{ \Illuminate\Support\Str::plural(__('Floor'), $project->number_floor) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if ($project->investor && $project->investor->name)
                    <div class="project-detail__developer">
                        @if ($project->investor->image)
                            <div class="project-detail__developer-bg" style="background-image: url('{{ RvMedia::getImageUrl($project->investor->image, null, false, RvMedia::getDefaultImage()) }}')"></div>
                        @endif
                        <div class="project-detail__developer-content">
                            <span class="project-detail__developer-eyebrow">{{ __('Developer') }}</span>
                            <h3>{{ $project->investor->name }}</h3>
                            @if ($project->investor->description)
                                <p>{{ $project->investor->description }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="boxright project-detail__tabs-card">
                    <div class="row">
                        <div class="col property_tab">
                            <div class="tab" role="tabpanel">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs project-detail__tab-nav" role="tablist">
                                    <li role="presentation" class="active"><a href="#Section1" aria-controls="home" class="showGrid mr-3 ml-3 active" data-tab-id="1" role="tab" data-toggle="tab"><i class="fa fa-star"></i> {{ __('Highlights') }}</a></li>
                                    <li role="presentation"><a href="#Section2" aria-controls="profile" role="tab" class="showGrid mr-3 ml-3" data-tab-id="2" data-toggle="tab"><i class="fa fa-home"></i> {{ __('Neighbourhood') }}</a></li>
                                    @if ($project->map_layout_image)
                                        <li role="presentation"><a href="#SectionMapLayout" role="tab" class="showGrid mr-3 ml-3" data-tab-id="3" data-toggle="tab"><i class="fas fa-map"></i> {{ __('Map Layout') }}</a></li>
                                    @endif
                                    <li role="presentation"><a href="#Section4" aria-controls="messages" role="tab" class="showGrid mr-3 ml-3" data-tab-id="4" data-toggle="tab"><i class="fas fa-chart-bar"></i> {{ __('Statistics') }}</a></li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content tabs">
                                    <div role="tabpanel" class="tab-pane fade in active show project-detail__tab-panel" id="Section1">
                                        <h3 class="pt-3 primary-head">{{ __('Description') }}</h3>

                                        @if ($project->number_flat || $project->number_floor)
                                            <p class="project-detail__description">{{ __('This project features :flats Flats with :floors floors.', ['flats' => $project->number_flat, 'floors' => $project->number_floor]) }}</p>
                                        @endif
                                        @if ($project->content)
                                            <div class="project-description-content project-detail__description">
                                                {!! clean($project->content, 'youtube') !!}
                                            </div>
                                        @endif
                                        <h3 class="primary-head">{{ __('Project Summary') }}</h3>
                                        <div class="project-detail__summary-grid">
                                            <div class="project-detail__summary-item">
                                                <b>{{ __('Category') }}</b><p>{{ __($project->category->name) }}</p>
                                            </div>
                                            <div class="project-detail__summary-item">
                                                <b>{{ __('No. of blocks') }}</b><p>{{ $project->number_block }}</p>
                                            </div>
                                            <div class="project-detail__summary-item">
                                                <b>{{ __('Possession Date') }}</b><p>{{ $project->date_finish ? \Carbon\Carbon::parse($project->date_finish)->format('d M, Y') : __('To be announced') }}</p>
                                            </div>
                                            <div class="project-detail__summary-item">
                                                <b>{{ __('Added At') }}</b><p>{{ $project->created_at }}</p>
                                            </div>
                                        </div>
                                        @if ($project->features->count())
                                            <h3 class="primary-head">{{ __('Features') }}</h3>
                                            <div class="project-detail__features-grid">
                                                @foreach($project->features as $feature)
                                                    <div class="project-detail__feature">
                                                        <i class="fas fa-check"></i>
                                                        {{ $feature->name }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <br>

                                    </div>
                                    <div role="tabpanel" class="tab-pane fade project-detail__tab-panel project-detail__neighbourhood" id="Section2">

                                        {!! Theme::partial('neighbourhood') !!}
                                        @if ($project->facilities->count())
                                            <div class="row pt-5">
                                                <div class="col-sm-12">
                                                    <h5 class="primary-head">{{ __('Distance key between facilities') }}</h5>
                                                    <div class="project-detail__facilities-grid">
                                                        @foreach($project->facilities as $facility)
                                                            @if ($facility->pivot->distance)
                                                                <div class="project-detail__feature">
                                                                    <i class="@if ($facility->icon) {{ $facility->icon }} @else fas fa-check @endif"></i>
                                                                    {{ $facility->name }} &mdash; {{ $facility->pivot->distance }}
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($project->map_layout_image)
                                        <div role="tabpanel" class="tab-pane fade project-detail__tab-panel project-detail__map-layout" id="SectionMapLayout">
                                            <h3 class="primary-head">{{ __('Map Layout') }}</h3>
                                            <img src="{{ RvMedia::getImageUrl($project->map_layout_image) }}" alt="{{ __('Map Layout') }}">
                                        </div>
                                    @endif
                                    <div role="tabpanel" class="tab-pane fade project-detail__tab-panel" id="Section4">
                                        <div class="project-detail__empty-state">
                                            <i class="fas fa-chart-bar"></i>
                                            <h4>{{ __('Statistics coming soon') }}</h4>
                                            <p>{{ __("We're building detailed market and pricing insights for this project. Check back soon.") }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                @if ($project->latitude)
                    <div class="mapouter project-detail__map-card">
                        <div class="gmap_canvas">
                            <iframe id="gmap_canvas" width="100%" height="500"
                                    src="https://maps.google.com/maps?q={{$project->latitude }},{{$project->longitude }}%20&t=&z=13&ie=UTF8&iwloc=&output=embed"
                                    frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                        </div>
                    </div>
                @endif

            </div>
            <div class="col-md-3">
                @php $salesWhatsapp = preg_replace('/\D/', '', (string) theme_option('hotline')); @endphp
                <div class="boxright project-detail__sales-card">
                    <div class="project-detail__sales-head">
                        <i class="fas fa-headset"></i>
                        <div>
                            <strong>{{ __('Contact GEMListing Sales') }}</strong>
                        </div>
                    </div>
                    <div class="project-detail__sales-body">
                        @if (theme_option('hotline'))
                            <p><a href="tel:{{ theme_option('hotline') }}"><i class="fa fa-phone mr-1"></i> {{ theme_option('hotline') }}</a></p>
                        @endif
                        @if ($salesWhatsapp)
                            <p><a href="https://wa.me/{{ $salesWhatsapp }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp mr-1"></i> {{ __('Chat on WhatsApp') }}</a></p>
                        @endif
                        @if (theme_option('email'))
                            <p><a href="mailto:{{ theme_option('email') }}"><i class="fa fa-envelope mr-1"></i> {{ theme_option('email') }}</a></p>
                        @endif
                        <button type="button" class="btn btn-orange btn-block mt-2" data-toggle="modal" data-target="#scheduleVisitModal">{{ __('Schedule a Site Visit') }}</button>
                    </div>
                </div>
                @if ($project->author->id)
                    <div class="boxright project-detail__agent-card">
                        <div class="project-detail__agent-head">
                            <i class="fas fa-user-tie"></i>
                            <div>
                                <strong>{{ __('Contact Agent') }}</strong>
                            </div>
                        </div>
                        <div class="project-detail__agent-body">
                            <div class="project-detail__agent-row">
                                @if ($project->author->username)
                                    <a href="{{ route('public.agent', $project->author->username) }}" class="project-detail__agent-avatar-link">
                                        <img src="{{ $project->author->avatar_url }}" alt="{{ $project->author->getFullName() }}" class="project-detail__agent-avatar">
                                    </a>
                                @else
                                    <img src="{{ $project->author->avatar_url }}" alt="{{ $project->author->getFullName() }}" class="project-detail__agent-avatar">
                                @endif

                                <div class="project-detail__agent-details">
                                    @if ($project->author->username)
                                        <a href="{{ route('public.agent', $project->author->username) }}" class="project-detail__agent-name">{{ $project->author->getFullName() }}</a>
                                    @else
                                        <span class="project-detail__agent-name">{{ $project->author->getFullName() }}</span>
                                    @endif

                                    <div class="project-detail__agent-info">
                                        <button type="button" data-id="{{ $project->author->id}}" class="showContact btn project-detail__agent-contact-btn btn-info pt-1 pb-1">{{ __('Show Contact') }} <i class="fa fa-spinner d-none" aria-hidden="true"></i></button>
                                    </div>

                                    <div class="showinfo contactInfo d-none project-detail__agent-info">
                                        <p class="mobile mobile-p d-none"><i class="fa fa-phone" aria-hidden="true"></i><span id="mobile_text"></span></p>
                                        <p><i class="fa fa-envelope" aria-hidden="true"></i><span id="email_text"></span></p>
                                    </div>

                                    @if ($project->author->username)
                                        <p class="project-detail__agent-more"><i class="fas fa-arrow-circle-right"></i> <a href="{{ route('public.agent', $project->author->username) }}">{{ __('More properties by this agent') }}</a></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="boxright project-detail__consult">
                    {!! Theme::partial('consult-form', ['type' => 'project', 'data' => $project]) !!}
                </div>

            </div>
        </div>
        <br>
        <br>
        <br>

    </div>
    {!! Theme::partial('schedule-visit-modal', ['data' => $project]) !!}
</main>
