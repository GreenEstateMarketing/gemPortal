@php
    $isVerifiedByAgent = $verified;
    $isVerifiedByAdmin = $verifiedByAdmin;
    $isFullyVerified = $isVerifiedByAgent && $isVerifiedByAdmin;
@endphp

<link rel="stylesheet" href="{{ asset('themes/real-scout/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ Theme::asset()->url('css/wizard/property-wizard.css') }}">

<div class="property-wizard">
    <div class="property-wizard__intro">
        <span class="property-wizard__eyebrow">{{ $role === 'agent' ? __('Step 2 of 5') : __('Step 3 of 6') }}</span>
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

        @if ($role === 'admin' && ! $isVerifiedByAdmin && ! $isVerifiedByAgent)
            <div class="wizard-verify-status wizard-verify-status--pending">
                <i class="fas fa-hourglass-half"></i>
                {{ __('Waiting for the agent to verify this property before you can verify it.') }}
            </div>
        @endif

        <div class="wizard-panel__actions">
            {{-- An agent has no Choose Agent step to go back to - they're already the assigned agent - so their Back goes to Submit Ad's review instead. --}}
            <a href="{{ $role === 'agent' ? $showBaseUrl . '?step=4' : $chooseAgentUrl }}" class="wizard-btn wizard-btn--ghost"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a>

            @if ($role === 'agent' && ! $isVerifiedByAgent)
                <form method="post" action="{{ $verifyAgentUrl }}" data-disable-on-submit>
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
                    <button type="button" class="wizard-btn wizard-btn--primary" disabled>
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
        'locked' => $locked,
    ])
</div>
