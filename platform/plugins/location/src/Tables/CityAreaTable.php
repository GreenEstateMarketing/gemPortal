<?php

namespace Botble\Location\Tables;

use Auth;
use BaseHelper;
use Botble\Location\Repositories\Interfaces\CityAreaInterface;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Location\Models\City;

class CityAreaTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    protected $cityRepository;

    public function __construct(
        DataTables        $table,
        UrlGenerator      $urlGenerator,
        CityAreaInterface $cityAreaRepository,
        CityInterface     $cityRepository
    )
    {
        $this->repository = $cityAreaRepository;
        $this->cityRepository = $cityRepository;
        $this->setOption('id', 'table-plugins-cityarea');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['cityarea.edit', 'cityarea.destroy'])) {
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
                if (!Auth::user()->hasPermission('cityarea.edit')) {
                    return $item->name;
                }
                return Html::link(route('cityarea.edit', $item->id), $item->city_area_name);
            })
            ->editColumn('city', function ($item) {
                if (!$item->city_id && $item->city->name) {
                    return null;
                }
                return Html::link(route('city.edit', $item->city_id), $item->city->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('cityarea.edit', 'cityarea.destroy', $item);
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
            'city_area.id',
            'city_area.city_area_name',
            'city_area.city_id',
            'city_area.created_at',
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
            'id' => [
                'name' => 'city_area.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name' => 'city_area.city_area_name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'city' => [
                'name' => 'city_area.city_id',
                'title' => trans('plugins/location::city.name'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name' => 'city_area.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('cityarea.create'), 'cityarea.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, City::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('cityarea.deletes'), 'cityarea.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [];
    }

    public function getFilters(): array
    {
        return [
            'cityarea.city_area_name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
            ]
        ];
    }
}
