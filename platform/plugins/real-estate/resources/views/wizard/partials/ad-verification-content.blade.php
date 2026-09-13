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

    <div class="wizard-panel">
        @if ($role === 'member')
            @if ($isFullyVerified)
                <div class="wizard-verify-status wizard-verify-status--success">
                    <i class="fas fa-check-circle"></i>
                    {{ __('Your property has been verified by both your agent and our admin team.') }}
                </div>
            @elseif ($isVerifiedByAgent)
                <div class="wizard-verify-status wizard-verify-status--pending">
                    <i class="fas fa-hourglass-half"></i>
                    {{ __('Your agent has verified this property. It\'s now waiting for admin verification.') }}
                </div>
            @else
                <div class="wizard-verify-status wizard-verify-status--pending">
                    <i class="fas fa-hourglass-half"></i>
                    {{ __('Your property is under verification by your agent.') }}
                </div>
            @endif
        @elseif ($role === 'agent')
            @if ($isFullyVerified)
                <div class="wizard-verify-status wizard-verify-status--success">
                    <i class="fas fa-check-circle"></i>
                    {{ __('This property has been verified by you and by our admin team.') }}
                </div>
            @elseif ($isVerifiedByAgent)
                <div class="wizard-verify-status wizard-verify-status--pending">
                    <i class="fas fa-hourglass-half"></i>
                    {{ __('You\'ve verified this property. It\'s now waiting for admin verification before you can continue.') }}
                </div>
            @else
                <div class="wizard-verify-status wizard-verify-status--pending">
                    <i class="fas fa-clipboard-check"></i>
                    {{ __('Please review this listing, then mark it as verified.') }}
                </div>
            @endif
        @else
            @if ($isFullyVerified)
                <div class="wizard-verify-status wizard-verify-status--success">
                    <i class="fas fa-check-circle"></i>
                    {{ __('You have verified this property.') }}
                </div>
            @elseif ($isVerifiedByAgent)
                <div class="wizard-verify-status wizard-verify-status--pending">
                    <i class="fas fa-clipboard-check"></i>
                    {{ __('The agent has verified this property. You can verify it now.') }}
                </div>
            @else
                <div class="wizard-verify-status wizard-verify-status--pending">
                    <i class="fas fa-hourglass-half"></i>
                    {{ __('This property is still under verification by the agent.') }}
                </div>
            @endif
        @endif

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
