<div class="table-actions">
    @if (!empty($edit))
        <a href="{{ route($edit, $item->id) }}" class="btn btn-icon btn-sm btn-primary"
           data-original-title="{{ trans('core/base::tables.edit') }}"><i class="fa fa-edit"></i></a>
    @endif

    @if (!empty($delete))
        <a href="#" class="btn btn-icon btn-sm btn-danger deleteDialog"
           data-section="{{ route($delete, $item->id) }}" role="button"
           data-original-title="{{ trans('core/base::tables.delete_entry') }}">
            <i class="fa fa-trash"></i>
        </a>
    @endif
        @if ($item->moderation_status == 'approved')
            @if($rating)
                <label type="button" class="rate_modal pt-1 pb-1" data-property-id="{{ $item->id }}"
                       data-author_id="{{ $item->author_id }}">
                </label>
                <div class="rating-stars" data-toggle="tooltip" data-original-title="{{ $rating->comment  }}">
                    @php
                        $maxStars = 5;
                        $ratingValue = $rating->rating;
                    @endphp

                    @for ($i = 1; $i <= $maxStars; $i++)
                        @if ($i <= $ratingValue)
                            <i class="fa fa-star text-warning"></i>
                        @else
                            <i class="fa fa-star text-secondary"></i>
                        @endif
                    @endfor
                </div>
            @else
                <label type="button" class="rate_modal pt-1 pb-1" data-property-id="{{ $item->id }}"
                       data-author_id="{{ $item->author_id }}">
                    <i class="Rate-icon fa fa-star" aria-hidden="true"></i> Rate Agent
                </label>
            @endif
        @endif

    <!-- <a href="#" class="btn btn-icon btn-sm btn-info button-renew" data-section="{{ route('public.account.properties.renew', $item->id) }}" role="button" data-original-title="{{ __('Renew') }}" >
        <i class="fas fa-sync-alt"></i>
    </a>-->
</div>
