<link rel="stylesheet" href="{{ asset('themes/real-scout/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ Theme::asset()->url('css/wizard/property-wizard.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<div class="property-wizard"
     data-role="{{ $role }}"
     data-property-id="{{ $property->id }}"
     data-upload-url="{{ $uploadUrl }}"
     data-finalize-url="{{ $stepUrls['finalize'] }}"
     data-csrf-token="{{ csrf_token() }}">

    <div class="property-wizard__intro">
        <span class="property-wizard__eyebrow">{{ __('List Your Property With Confidence') }}</span>
        <h1 class="property-wizard__title">{{ __('Add Property') }}</h1>
        <p class="property-wizard__subtitle">{{ __('Follow the journey below to get your property listed with GEM.') }}</p>
    </div>

    @include('plugins/real-estate::wizard.partials.global-header')

    @include('plugins/real-estate::wizard.partials.sub-header')

    @if ($activeStep === 1)
        @include('plugins/real-estate::wizard.steps.basics')
    @elseif ($activeStep === 2)
        @include('plugins/real-estate::wizard.steps.location')
    @elseif ($activeStep === 3)
        @include('plugins/real-estate::wizard.steps.media')
    @else
        @include('plugins/real-estate::wizard.steps.review')
    @endif
</div>

<script src="{{ Theme::asset()->url('js/wizard/property-wizard.js') }}"></script>
