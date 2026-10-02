{{--
    Static 3-property teaser strip. Deliberately NOT tied to the map's
    search filters (independent section) and NOT using the existing
    is_featured flag - no property in this database has ever been marked
    featured, so $randomProperties (passed from
    PublicController::getProperties()) is a plain random-3 query instead.
    Reloading the page shows a different 3 each time.
--}}
<section class="properties-highlights">
    <div class="properties-highlights__inner">
        <h2 class="properties-highlights__heading">{{ __('Explore More Properties') }}</h2>
        <p class="properties-highlights__text">{{ __('A few more listings worth a look.') }}</p>

        @if ($randomProperties->count() > 0)
            <div class="properties-highlights__grid">
                @foreach ($randomProperties as $property)
                    <a href="{{ $property->url }}" title="{{ $property->name }}" class="properties-highlights__card">
                        <div class="properties-highlights__card-image-wrap">
                            <img
                                src="{{ RvMedia::getImageUrl($property->image, 'medium', false, RvMedia::getDefaultImage()) }}"
                                alt="{{ $property->name }}" class="properties-highlights__card-image" loading="lazy">
                            <span class="properties-highlights__card-badge">{{ $property->type->label() }}</span>
                        </div>
                        <div class="properties-highlights__card-body">
                            <p class="properties-highlights__card-price">{{ format_price($property->price, $property->currency) }}</p>
                            <h3 class="properties-highlights__card-title">{{ $property->name }}</h3>
                            @if ($property->city && $property->city->name)
                                <p class="properties-highlights__card-location">
                                    <i class="fas fa-map-marker-alt"></i> {{ $property->city->name }}
                                </p>
                            @endif
                            <div class="properties-highlights__card-meta">
                                @if ($property->number_bedroom)
                                    <span><i class="fas fa-bed"></i> {{ $property->number_bedroom }} {{ __('Beds') }}</span>
                                @endif
                                @if ($property->number_bathroom)
                                    <span><i class="fas fa-bath"></i> {{ $property->number_bathroom }} {{ __('Baths') }}</span>
                                @endif
                                @if ($property->square)
                                    <span><i class="fas fa-ruler-combined"></i> {{ $property->square_text }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="properties-highlights__empty">{{ __('No properties to show yet.') }}</p>
        @endif
    </div>
</section>
