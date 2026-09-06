@php
    $globalSteps = [
        1 => ['label' => __('Submit Ad'), 'icon' => 'fa-file-medical'],
        2 => ['label' => __('Choose Agent'), 'icon' => 'fa-user-check'],
        3 => ['label' => __('Ad Verification'), 'icon' => 'fa-clipboard-check'],
        4 => ['label' => __('Sign Contract'), 'icon' => 'fa-file-signature'],
        5 => ['label' => __('Listing Payment'), 'icon' => 'fa-credit-card'],
        6 => ['label' => __('Ad Listing'), 'icon' => 'fa-clipboard-list'],
    ];
@endphp

<div class="wizard-global-steps">
    @foreach ($globalSteps as $num => $step)
        @php
            $state = 'disabled';
            $link = null;

            if ($num === 1) {
                $state = $property->isSubmitted() ? 'completed' : 'current';
            } elseif ($num === 2 && $property->isSubmitted()) {
                $state = 'current';
                $link = $chooseAgentUrl;
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
