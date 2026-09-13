@php
    $subSteps = [
        1 => __('Basics & Price'),
        2 => __('Location & Details'),
        3 => __('Media & Documents'),
        4 => __('Review'),
    ];
@endphp

<div class="wizard-sub-steps">
    @foreach ($subSteps as $num => $label)
        @php
            $isCurrent = $num === $activeStep;
            $isCompleted = $num < $activeStep || ($num <= $property->wizard_step && !$isCurrent);
            $isReachable = $num <= $furthestReachable;
            $state = $isCurrent ? 'current' : ($isCompleted ? 'completed' : ($isReachable ? '' : 'locked'));
        @endphp
        @if ($isReachable && !$isCurrent)
            <a href="{{ $showBaseUrl }}?step={{ $num }}" class="wizard-sub-step wizard-sub-step--{{ $state }}">
                <span class="wizard-sub-step__num">{{ $isCompleted ? '✓' : $num }}</span>
                <span>{{ $label }}</span>
            </a>
        @elseif ($isReachable)
            {{-- The step you're already on - same look, but not a link. --}}
            <span class="wizard-sub-step wizard-sub-step--{{ $state }}">
                <span class="wizard-sub-step__num">{{ $num }}</span>
                <span>{{ $label }}</span>
            </span>
        @else
            <span class="wizard-sub-step wizard-sub-step--locked">
                <span class="wizard-sub-step__num">{{ $num }}</span>
                <span>{{ $label }}</span>
            </span>
        @endif
    @endforeach
</div>
