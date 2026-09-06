<link rel="stylesheet" href="{{ asset('themes/real-scout/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ Theme::asset()->url('css/wizard/property-wizard.css') }}">

<div class="property-wizard">
    <div class="property-wizard__intro">
        <span class="property-wizard__eyebrow">{{ __('Step 2 of 6') }}</span>
        <h1 class="property-wizard__title">{{ __('Choose Agent') }}</h1>
    </div>

    <div class="wizard-placeholder">
        <i class="fas fa-user-check"></i>
        <h2>{{ __('Your ad ":name" has been submitted!', ['name' => $property->name]) }}</h2>
        <p class="wizard-hint">{{ __('Pick your favorite agent or let GEM choose the right professional for you. This step is coming soon.') }}</p>
    </div>
</div>
