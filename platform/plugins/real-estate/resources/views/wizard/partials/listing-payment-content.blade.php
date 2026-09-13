<link rel="stylesheet" href="{{ asset('themes/real-scout/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ Theme::asset()->url('css/wizard/property-wizard.css') }}">

<div class="property-wizard">
    <div class="property-wizard__intro">
        <span class="property-wizard__eyebrow">{{ __('Step 5 of 6') }}</span>
        <h1 class="property-wizard__title">{{ __('Listing Payment') }}</h1>
    </div>

    @include('plugins/real-estate::wizard.partials.global-header', ['currentGlobalStep' => 5])

    <div class="wizard-panel">
        @if ($isPaid)
            <div class="wizard-verify-status wizard-verify-status--success">
                <i class="fas fa-check-circle"></i>
                {{ __('Payment has been completed by :name. This property is now listed.', ['name' => $memberName]) }}
            </div>
        @elseif ($role === 'member')
            @if ($hasCredits)
                <div class="wizard-verify-status wizard-verify-status--pending">
                    <i class="fas fa-credit-card"></i>
                    {{ __('Confirming will deduct 1 credit from your account to publish this listing.') }}
                </div>
            @else
                <div class="wizard-verify-status wizard-verify-status--pending">
                    <i class="fas fa-triangle-exclamation"></i>
                    {{ __('You don\'t have any credits available. Please buy some credits, then come back here to complete this step.') }}
                </div>
            @endif
        @else
            <div class="wizard-verify-status wizard-verify-status--pending">
                <i class="fas fa-hourglass-half"></i>
                {{ __('A payment is pending from :name.', ['name' => $memberName]) }}
            </div>
        @endif

        <div class="wizard-panel__actions">
            <a href="{{ $signContractUrl }}" class="wizard-btn wizard-btn--ghost"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>

            @if ($isPaid)
                <a href="{{ $adListingUrl }}" class="wizard-btn wizard-btn--primary">
                    {{ __('Continue') }} <i class="fas fa-arrow-right"></i>
                </a>
            @elseif ($role === 'member' && $hasCredits)
                <form method="post" action="{{ $confirmPaymentUrl }}">
                    @csrf
                    <button type="submit" class="wizard-btn wizard-btn--primary">
                        {{ __('Confirm Payment') }} <i class="fas fa-credit-card"></i>
                    </button>
                </form>
            @elseif ($role === 'member' && ! $hasCredits)
                <a href="{{ $buyCreditsUrl }}" class="wizard-btn wizard-btn--primary">
                    {{ __('Buy Credits') }} <i class="fas fa-arrow-right"></i>
                </a>
            @else
                <span></span>
            @endif
        </div>
    </div>
</div>
