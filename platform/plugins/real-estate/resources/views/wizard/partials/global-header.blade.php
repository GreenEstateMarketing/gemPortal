@php
    $globalSteps = [
        1 => ['label' => __('Submit Ad'), 'icon' => 'fa-file-medical'],
        2 => ['label' => __('Choose Agent'), 'icon' => 'fa-user-check'],
        3 => ['label' => __('Ad Verification'), 'icon' => 'fa-clipboard-check'],
        4 => ['label' => __('Sign Contract'), 'icon' => 'fa-file-signature'],
        5 => ['label' => __('Listing Payment'), 'icon' => 'fa-credit-card'],
        6 => ['label' => __('Ad Listing'), 'icon' => 'fa-clipboard-list'],
    ];
    // Which macro-step's own page this partial is being rendered on -
    // that one never gets a link, same as "you are here" everywhere else
    // in the wizard. Defaults to 1 (the Submit Ad sub-wizard); the Choose
    // Agent and Ad Verification placeholders pass 2/3 explicitly.
    $currentGlobalStep = $currentGlobalStep ?? 1;
    $hasAgent = $property->author_type === \Botble\RealEstate\Models\Account::class && $property->author_id;
@endphp

<div class="wizard-global-steps">
    @foreach ($globalSteps as $num => $step)
        @php
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
            } elseif ($num === 3 && $hasAgent) {
                $state = 'current';
                if ($num !== $currentGlobalStep && isset($adVerificationUrl)) {
                    $link = $adVerificationUrl;
                }
            }
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
