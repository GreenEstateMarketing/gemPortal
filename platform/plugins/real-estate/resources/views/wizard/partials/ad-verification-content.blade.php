<link rel="stylesheet" href="{{ asset('themes/real-scout/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ Theme::asset()->url('css/wizard/property-wizard.css') }}">

<div class="property-wizard">
    <div class="property-wizard__intro">
        <span class="property-wizard__eyebrow">{{ __('Step 3 of 6') }}</span>
        <h1 class="property-wizard__title">{{ __('Ad Verification') }}</h1>
    </div>

    @include('plugins/real-estate::wizard.partials.global-header', ['currentGlobalStep' => 3])

    <div class="wizard-panel">
        <div class="wizard-placeholder wizard-placeholder--empty">
            <i class="fas fa-clipboard-check"></i>
            <h2>{{ __('Ad verification is coming soon') }}</h2>
            <p class="wizard-hint">{{ __('This step isn\'t built yet - check back soon.') }}</p>
        </div>

        <div class="wizard-panel__actions">
            <a href="{{ $chooseAgentUrl }}" class="wizard-btn wizard-btn--ghost"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>
            <span></span>
        </div>
    </div>
</div>
