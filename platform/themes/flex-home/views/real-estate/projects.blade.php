<section class="main-homes">
    <div class="bgheadproject hidden-xs">
        <div class="description">
            <div class="container-fluid w90">
                <h1 class="text-center">{{ __('Discover our projects') }}</h1>
                <p class="text-center">{{ theme_option('home_project_description') }}</p>
                {!! Theme::partial('breadcrumb') !!}
            </div>
        </div>
    </div>
    <div class="container-fluid w90 padtop30">
        <div class="projecthome">
            <form action="{{ route('public.projects') }}" method="get">
                <div class="row rowm10">
                    <div class="col-md-3 col-sm-6">
                        @include(Theme::getThemeNamespace() . '::views.real-estate.includes.search-box', ['type' => 'project', 'categories' => $categories])
                    </div>
                    <div class="col-md-9 col-sm-6">
                        @if ($projects->count())
                            @include(Theme::getThemeNamespace() . '::views.real-estate.includes.filters')
                            <div class="row">
                                @foreach ($projects as $project)
                                    <div class="col-6 col-sm-6 col-md-4 colm10">
                                        <div class="item">
                                            <div class="blii">
                                                <div class="img"><img class="thumb" data-src="{{ RvMedia::getImageUrl($project->image, 'small', false, RvMedia::getDefaultImage()) }}" src="{{ RvMedia::getImageUrl($project->image, 'small', false, RvMedia::getDefaultImage()) }}" alt="{{ $project->name }}">
                                                </div>
                                                <a href="{{ $project->url }}" class="linkdetail"></a>
                                                <ul class="item-price-wrap hide-on-list"><li class="h-type"><span>{{ $project->category->name }}</span></li></ul>
                                            </div>

                                            <div class="description">
                                                <a href="{{ $project->url }}"><h5>{{ $project->name }}</h5>
                                                    <p class="dia_chi"><i class="fas fa-map-marker-alt"></i> {{ $project->city->name }}, {{ $project->city->state->name }}</p>
                                                    @if ($project->price_from || $project->price_to)
                                                        <p class="bold500">{{ __('Price') }}: @if ($project->price_from) <span class="from">{{ __('From') }}</span> {{ format_price($project->price_from, $project->currency) }} @endif @if ($project->price_to) - {{ format_price($project->price_to, $project->currency) }} @endif</p>
                                                    @endif
                                                </a>
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
        {!! $projects->withQueryString()->links() !!}
    </nav>
</div>
<br>
<br>
