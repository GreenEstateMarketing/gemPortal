@php
    use Illuminate\Support\Str;
@endphp

<div style="background-color: #f3a54a;p;padding: 10px;border: #f3a54a;font-weight: bold;">
    <select class="select form-control" id="mode-status-select"
        style="background: #f3a54a;color: #fff;border: 2px solid #fff;">
        @foreach ($moderationStatuses as $moderationStatus)
            @if($selectedModerationStatus == Str::lower($moderationStatus))
                <option value="{{ Str::lower($moderationStatus) }}" selected>{{ $moderationStatus }}</option>
            @else
                <option value="{{ Str::lower($moderationStatus) }}">{{ $moderationStatus }}</option>
            @endif
        @endforeach
    </select>
</div>