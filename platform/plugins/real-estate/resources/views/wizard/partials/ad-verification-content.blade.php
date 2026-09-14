@php
    $isVerifiedByAgent = $verified;
    $isVerifiedByAdmin = $verifiedByAdmin;
    $isFullyVerified = $isVerifiedByAgent && $isVerifiedByAdmin;
@endphp

<link rel="stylesheet" href="{{ asset('themes/real-scout/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ Theme::asset()->url('css/wizard/property-wizard.css') }}">

<div class="property-wizard">
    <div class="property-wizard__intro">
        <span class="property-wizard__eyebrow">{{ __('Step 3 of 6') }}</span>
        <h1 class="property-wizard__title">{{ __('Ad Verification') }}</h1>
    </div>

    @include('plugins/real-estate::wizard.partials.global-header', ['currentGlobalStep' => 3])

    <div class="wizard-chat-jump-row">
        <a href="#wizard-chat" class="wizard-chat-jump">
            <i class="fas fa-comments"></i>
            {{ __('Go to Chat') }}
            @if ($comments->count())
                <span class="wizard-chat-jump__count">{{ $comments->count() }}</span>
            @endif
        </a>
    </div>

    <div class="wizard-panel">
        @include('plugins/real-estate::wizard.partials.property-document', [
            'property' => $property,
            'categoryDocuments' => $categoryDocuments,
            'verifiedByAgent' => $isVerifiedByAgent,
            'verifiedByAdmin' => $isVerifiedByAdmin,
        ])

        <div class="wizard-panel__actions">
            <a href="{{ $chooseAgentUrl }}" class="wizard-btn wizard-btn--ghost"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>

            @if ($role === 'agent' && ! $isVerifiedByAgent)
                <form method="post" action="{{ $verifyAgentUrl }}">
                    @csrf
                    <button type="submit" class="wizard-btn wizard-btn--primary">
                        {{ __('Mark Property Verify') }} <i class="fas fa-check"></i>
                    </button>
                </form>
            @elseif ($role === 'admin' && ! $isVerifiedByAdmin)
                @if ($isVerifiedByAgent)
                    <form method="post" action="{{ $verifyAdminUrl }}">
                        @csrf
                        <button type="submit" class="wizard-btn wizard-btn--primary">
                            {{ __('Verify Property') }} <i class="fas fa-check"></i>
                        </button>
                    </form>
                @else
                    <button type="button" class="wizard-btn wizard-btn--primary" disabled title="{{ __('Waiting for the agent to verify this property first.') }}">
                        {{ __('Verify Property') }} <i class="fas fa-check"></i>
                    </button>
                @endif
            @elseif ($isFullyVerified)
                <a href="{{ $signContractUrl }}" class="wizard-btn wizard-btn--primary">
                    {{ __('Save & Continue') }} <i class="fas fa-arrow-right"></i>
                </a>
            @else
                <span></span>
            @endif
        </div>
    </div>

    @include('plugins/real-estate::wizard.partials.ad-verification-chat', [
        'comments' => $comments,
        'role' => $role,
        'commentStoreUrl' => $commentStoreUrl,
    ])
</div>
