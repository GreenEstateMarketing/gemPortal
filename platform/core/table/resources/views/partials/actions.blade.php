<div class="table-actions">
    {!! $extra !!}
    @if (Route::current()->getName() == 'property.index' && auth()->user()->super_user == 1)
        <a href="{{ route('consult.property.consults', $item->id) }}" class="btn btn-icon btn-sm btn-primary"
            title="Consults"><i class="fas fa-headset"></i><span class="badge badge-info"
                id="consult_count">{{ auth()->user()->getConsults($item->id) }}</span></a>
    @endif
    @if (!empty($edit))
        @if (Auth::user()->hasPermission($edit))
            <a href="{{ route($edit, $item->id) }}" class="btn btn-icon btn-sm btn-primary" data-toggle="tooltip"
                data-original-title="{{ trans('core/base::tables.edit') }}"><i class="fa fa-edit"></i></a>
        @endif
    @endif

    @if (!empty($delete))
        @if (Route::current()->getName() == 'property.index' && auth()->user()->super_user == 1)
            <a href="#" class="btn btn-icon btn-sm btn-danger deleteDialog" data-toggle="tooltip"
                data-section="{{ route($delete, $item->id) }}" role="button"
                data-original-title="{{ trans('core/base::tables.disable_entry') }}">
                <i class="fa fa-eye-slash"></i>
            </a>
        @elseif(Auth::user()->hasPermission($delete))
            <a href="#" class="btn btn-icon btn-sm btn-danger deleteDialog" data-toggle="tooltip"
                data-section="{{ route($delete, $item->id) }}" role="button"
                data-original-title="{{ trans('core/base::tables.delete_entry') }}">
                <i class="fa fa-trash"></i>
            </a>
        @endif
    @endif
</div>
