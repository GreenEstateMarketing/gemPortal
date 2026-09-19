@php
    $globalSteps = [
        1 => ['label' => __('Submit Ad'), 'icon' => 'fa-file-medical'],
        2 => ['label' => __('Choose Agent'), 'icon' => 'fa-user-check'],
        3 => ['label' => __('Ad Verification'), 'icon' => 'fa-clipboard-check'],
        4 => ['label' => __('Sign Contract'), 'icon' => 'fa-file-signature'],
        5 => ['label' => __('Listing Payment'), 'icon' => 'fa-credit-card'],
        6 => ['label' => __('Ad Listing'), 'icon' => 'fa-clipboard-list'],
    ];

    // An agent submitting their own listing is already its assigned agent -
    // there's nobody to choose, so that step never applies to them and
    // shouldn't take up a slot in their journey (or its progress math).
    if (($role ?? null) === 'agent') {
        unset($globalSteps[2]);
    }
    // Which macro-step's own page this partial is being rendered on -
    // that one never gets a link, same as "you are here" everywhere else
    // in the wizard. Defaults to 1 (the Submit Ad sub-wizard); the Choose
    // Agent and Ad Verification placeholders pass 2/3 explicitly.
    $currentGlobalStep = $currentGlobalStep ?? 1;
    $hasAgent = $property->author_type === \Botble\RealEstate\Models\Account::class && $property->author_id;
    $isFullyVerified = (bool) $property->verified && (bool) $property->verified_by_admin;
    // A property with no member (an agent's own listing) has nobody to sign
    // in that role, so only the agent and admin need to sign it.
    $isContractFullySigned = $property->member_id
        ? ((bool) $property->contract_signed_by_member
            && (bool) $property->contract_signed_by_agent
            && (bool) $property->contract_signed_by_admin)
        : ((bool) $property->contract_signed_by_agent && (bool) $property->contract_signed_by_admin);
    $isPaymentComplete = (string) $property->moderation_status === 'approved';

    // Resolved in one pass first (rather than inline in the @foreach below)
    // so the progress bar above the step icons can be computed from the
    // same states before they're rendered.
    $stepStates = [];

    foreach ($globalSteps as $num => $step) {
        $state = 'disabled';
        $link = null;

        if ($num === 1) {
            $state = $property->isSubmitted() ? 'completed' : 'current';
            if ($num !== $currentGlobalStep && $property->isSubmitted() && isset($showBaseUrl)) {
                $link = $showBaseUrl . '?step=1';
            }
        } elseif ($num === 2 && $property->isSubmitted()) {
            $state = $hasAgent ? 'completed' : 'current';
            if ($num !== $currentGlobalStep) {
                $link = $chooseAgentUrl;
            }
        } elseif ($num === 3 && $hasAgent && $property->isSubmitted()) {
            $state = $isFullyVerified ? 'completed' : 'current';
            if ($num !== $currentGlobalStep && isset($adVerificationUrl)) {
                $link = $adVerificationUrl;
            }
        } elseif ($num === 4 && $isFullyVerified) {
            $state = $isContractFullySigned ? 'completed' : 'current';
            if ($num !== $currentGlobalStep && isset($signContractUrl)) {
                $link = $signContractUrl;
            }
        } elseif ($num === 5 && $isContractFullySigned) {
            $state = $isPaymentComplete ? 'completed' : 'current';
            if ($num !== $currentGlobalStep && isset($listingPaymentUrl)) {
                $link = $listingPaymentUrl;
            }
        } elseif ($num === 6 && $isPaymentComplete) {
            $state = 'current';
            if ($num !== $currentGlobalStep && isset($adListingUrl)) {
                $link = $adListingUrl;
            }
        }

        $stepStates[$num] = ['state' => $state, 'link' => $link];
    }

    // Completed steps count fully. A step that's merely reached but not yet
    // actioned counts for nothing - landing on a step is a side effect of
    // the previous step's Save & Continue, not an action of its own, so a
    // brand new draft should read 0%, not some baseline bump just for
    // opening the wizard.
    $completedCount = collect($stepStates)->where('state', 'completed')->count();

    // Step 1 (Submit Ad) is the one exception: it has real sub-steps of its
    // own (Basics/Location/Media/Review), each incrementing wizard_step on
    // its own Save & Continue, so it can get genuine partial credit for
    // those rather than an all-or-nothing jump.
    $step1PartialCredit = 0;
    if (($stepStates[1]['state'] ?? null) === 'current') {
        $step1PartialCredit = min(4, max(0, (int) $property->wizard_step)) / 4;
    }

    // Step 6 has no further step after it to ever flip it to "completed" -
    // reaching it at all only happens once payment is done, meaning the
    // whole journey is actually finished, so it should count fully.
    $step6FullCredit = (($stepStates[6]['state'] ?? null) === 'current') ? 1 : 0;

    $progressPercent = (int) round((($completedCount + $step1PartialCredit + $step6FullCredit) / count($globalSteps)) * 100);
@endphp

<div class="wizard-progress">
    <div class="wizard-progress__header">
        <span class="wizard-progress__label">{{ __('Application Progress') }}</span>
        <span class="wizard-progress__percent">{{ $progressPercent }}%</span>
    </div>
    <div class="wizard-progress__track">
        <div class="wizard-progress__fill" style="width: {{ $progressPercent }}%"></div>
    </div>
</div>

<div class="wizard-global-steps">
    @foreach ($globalSteps as $num => $step)
        @php
            $state = $stepStates[$num]['state'];
            $link = $stepStates[$num]['link'];
        @endphp
        <div class="wizard-global-step wizard-global-step--{{ $state }}">
            @if ($link)
                <a href="{{ $link }}" class="wizard-global-step__link">
            @endif
                <span class="wizard-global-step__icon"><i class="fas {{ $step['icon'] }}"></i></span>
                <span class="wizard-global-step__label">{{ $step['label'] }}</span>
            @if ($link)
                </a>
            @endif
        </div>
    @endforeach
</div>
