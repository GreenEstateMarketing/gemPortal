<?php

namespace Botble\Location\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Location\Repositories\Interfaces\CountryInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Location\Models\Country;

class CountryTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * CountryTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param CountryInterface $countryRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, CountryInterface $countryRepository)
    {
        $this->repository = $countryRepository;
        $this->setOption('id', 'table-plugins-country');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['country.edit', 'country.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('country.edit')) {
                    return $item->name;
                }
                return Html::link(route('country.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('country.edit', 'country.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'countries.id',
            'countries.name',
            'countries.nationality',
            'countries.created_at',
            'countries.status',
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'          => [
                'name'  => 'countries.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name'        => [
                'name'  => 'countries.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'nationality' => [
                'name'  => 'countries.nationality',
                'title' => trans('plugins/location::country.nationality'),
                'class' => 'text-left',
            ],
            'created_at'  => [
                'name'  => 'countries.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status'      => [
                'name'  => 'countries.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('country.create'), 'country.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Country::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('country.deletes'), 'country.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'countries.name'        => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'countries.nationality' => [
                'title'    => trans('plugins/location::country.nationality'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'countries.status'      => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'countries.created_at'  => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }
}
