<div class="dropdown dropdown-hover">
    <a href="javascript:;">{{ trans('core/table::table.bulk_change') }}
        <i class="fa fa-angle-right"></i>
    </a>
    <div class="dropdown-content">
        @foreach ($bulk_changes as $key => $bulk_change)
            @if($bulk_change['title'] == "Moderation status")
                @continue;
            @endif
            <a href="#" data-key="{{ $key }}" data-class-item="{{ $class }}" data-save-url="{{ $url }}"
               class="bulk-change-item">{{ $bulk_change['title'] }}</a>
        @endforeach
    </div>
</div>
