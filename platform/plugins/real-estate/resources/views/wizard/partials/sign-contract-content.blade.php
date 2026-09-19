@php
    $parties = [];

    // A property with no member (an agent's own listing) has nobody to sign
    // in that role - only the agent and admin need to sign it.
    if ($requiresMember) {
        $parties['member'] = [
            'label' => __('Member'),
            'name' => $property->member ? $property->member->full_name : __('Property Owner'),
            'signed' => $signedByMember,
        ];
    }

    $parties['agent'] = [
        'label' => __('Agent'),
        'name' => $property->author_type === \Botble\RealEstate\Models\Account::class && $property->author
            ? $property->author->getFullName()
            : __('Agent'),
        'signed' => $signedByAgent,
    ];

    $parties['admin'] = [
        'label' => __('Admin'),
        'name' => __('GEM Listing Admin'),
        'signed' => $signedByAdmin,
    ];
@endphp

<link rel="stylesheet" href="{{ asset('themes/real-scout/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ Theme::asset()->url('css/wizard/property-wizard.css') }}">

<div class="property-wizard">
    <div class="property-wizard__intro">
        <span class="property-wizard__eyebrow">{{ $role === 'agent' ? __('Step 3 of 5') : __('Step 4 of 6') }}</span>
        <h1 class="property-wizard__title">{{ __('Sign Contract') }}</h1>
    </div>

    @include('plugins/real-estate::wizard.partials.global-header', ['currentGlobalStep' => 4])

    <div class="wizard-panel">
        @include('plugins/real-estate::wizard.partials.sign-contract-document', ['property' => $property])

        <div class="wizard-signature-list">
            @foreach ($parties as $key => $party)
                <div class="wizard-signature-row wizard-signature-row--{{ $party['signed'] ? 'signed' : 'pending' }}">
                    <div class="wizard-signature-row__who">
                        <i class="fas {{ $party['signed'] ? 'fa-circle-check' : 'fa-circle-notch' }}"></i>
                        <span class="wizard-signature-row__label">{{ $party['label'] }}</span>
                        <span class="wizard-signature-row__name">{{ $party['name'] }}</span>
                    </div>
                    @if ($party['signed'])
                        <span class="wizard-signature-row__status">{{ __('Signed') }}</span>
                    @elseif ($key === $role)
                        <form method="post" action="{{ $signUrl }}" class="wizard-signature-row__sign-form">
                            @csrf
                            <button type="submit" class="wizard-btn wizard-btn--primary wizard-btn--small">
                                {{ __('Sign Contract') }} <i class="fas fa-signature"></i>
                            </button>
                        </form>
                    @else
                        <span class="wizard-signature-row__status">{{ __('Pending signature') }}</span>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($allSigned)
            <div class="wizard-verify-status wizard-verify-status--success">
                <i class="fas fa-check-circle"></i>
                {{ $requiresMember ? __('The contract has been signed by all three parties.') : __('The contract has been signed by both parties.') }}
            </div>
        @elseif ($signedByRole)
            <div class="wizard-verify-status wizard-verify-status--pending">
                <i class="fas fa-hourglass-half"></i>
                {{ __('You\'ve signed the contract. Waiting for the others to sign.') }}
            </div>
        @else
            <div class="wizard-verify-status wizard-verify-status--pending">
                <i class="fas fa-file-signature"></i>
                {{ __('Please review the contract above, then sign using the button next to your name.') }}
            </div>
        @endif

        <div class="wizard-panel__actions">
            <a href="{{ $adVerificationUrl }}" class="wizard-btn wizard-btn--ghost"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>

            @if ($allSigned)
                <a href="{{ $listingPaymentUrl }}" class="wizard-btn wizard-btn--primary">
                    {{ __('Save & Continue') }} <i class="fas fa-arrow-right"></i>
                </a>
            @else
                <span></span>
            @endif
        </div>
    </div>
</div>
