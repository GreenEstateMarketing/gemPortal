@php
    use Illuminate\Support\Str;
@endphp

<div style="padding: 10px;border: #f3a54a;font-weight: bold;border-radius: 17px;">
    <select class="select form-control" id="mode-status-select"
        style="background: grey;color: #fff;border: 2px solid #fff;border-radius: 17px;height: 1%;padding-left: 2%;"
        disabled="disabled">
        @foreach ($moderationStatuses as $moderationStatus)
            @if($selectedModerationStatus == Str::lower($moderationStatus))
                <option value="{{ Str::lower($moderationStatus) }}" selected>{{ $moderationStatus }}</option>
            @else
                <option value="{{ Str::lower($moderationStatus) }}">{{ $moderationStatus }}</option>
            @endif
        @endforeach
    </select>
</div>