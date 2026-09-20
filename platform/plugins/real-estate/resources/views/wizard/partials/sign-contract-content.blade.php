@php
    $parties = [];

    // A property with no member (an agent's own listing) has nobody to sign
    // in that role - only the agent needs to sign it.
    if ($requiresMember) {
        $parties['member'] = [
            'label' => __('Seller'),
            'name' => $property->member ? $property->member->full_name : __('Property Owner'),
            'signed' => $memberSigned,
            'signatureUrl' => $memberSignatureUrl,
        ];
    }

    $parties['agent'] = [
        'label' => __('Agent'),
        'name' => $property->author_type === \Botble\RealEstate\Models\Account::class && $property->author
            ? $property->author->getFullName()
            : __('Agent'),
        'signed' => $agentSigned,
        'signatureUrl' => $agentSignatureUrl,
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
        <div class="wizard-signature-list">
            @foreach ($parties as $key => $party)
                <div class="wizard-signature-row wizard-signature-row--{{ $party['signed'] ? 'signed' : 'pending' }}">
                    <div class="wizard-signature-row__who">
                        <i class="fas {{ $party['signed'] ? 'fa-circle-check' : 'fa-circle-notch' }}"></i>
                        <span class="wizard-signature-row__label">{{ $party['label'] }}</span>
                        <span class="wizard-signature-row__name">{{ $party['name'] }}</span>
                    </div>
                    @if ($party['signed'])
                        <span class="wizard-signature-row__status">{{ __('Signature on file') }}</span>
                    @elseif ($key === $role)
                        <a href="{{ $party['signatureUrl'] }}" class="wizard-btn wizard-btn--primary wizard-btn--small">
                            {{ __('Add your signature') }} <i class="fas fa-signature"></i>
                        </a>
                    @else
                        <span class="wizard-signature-row__status">{{ __('Pending') }}</span>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="wizard-contract-document">
            <div class="wizard-contract-document__header">
                <h3 class="wizard-contract-document__title">{{ __('Property Listing Agreement') }}</h3>
                <p class="wizard-contract-document__ref">
                    {{ $alreadyFinalized ? __('Finalized and emailed - always reflects the latest data.') : __('Live preview - updates as signatures are added.') }}
                </p>
            </div>
            <embed src="{{ $downloadUrl }}" type="application/pdf" class="wizard-contract-pdf">
        </div>

        @if ($readyToSign)
            <div class="wizard-verify-status wizard-verify-status--{{ $alreadyFinalized ? 'success' : 'pending' }}">
                <i class="fas {{ $alreadyFinalized ? 'fa-check-circle' : 'fa-file-signature' }}"></i>
                {{ $alreadyFinalized
                    ? __('The contract has been finalized and emailed to both parties. Save & Continue again anytime to send an updated copy reflecting the latest data.')
                    : __('Both signatures are on file. Save & Continue to finalize the contract and email both parties their copy.') }}
            </div>
        @else
            <div class="wizard-verify-status wizard-verify-status--pending">
                <i class="fas fa-file-signature"></i>
                {{ __('Waiting for the required signatures before the contract can be finalized.') }}
            </div>
        @endif

        <div class="wizard-panel__actions">
            <a href="{{ $adVerificationUrl }}" class="wizard-btn wizard-btn--ghost"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>

            @if ($readyToSign)
                <form method="post" action="{{ $finalizeUrl }}">
                    @csrf
                    <button type="submit" class="wizard-btn wizard-btn--primary">
                        {{ __('Save & Continue') }} <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            @else
                <span></span>
            @endif
        </div>
    </div>
</div>
