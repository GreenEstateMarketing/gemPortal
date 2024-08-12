<?php

namespace Botble\RealEstate\Tables;

use Auth;
use BaseHelper;
use Botble\RealEstate\Repositories\Interfaces\WantedInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Throwable;
use Yajra\DataTables\DataTables;

class WantedTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlDevTool, WantedInterface $wantedRepository)
    {
        $this->repository = $wantedRepository;
        $this->setOption('id', 'plugins-wanted');
        parent::__construct($table, $urlDevTool);

        if (!Auth::user()->hasAnyPermission(['wanted.edit', 'wanted.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    /**
     * Display ajax response.
     *
     * @return JsonResponse
     * @since 2.1
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('wanted.edit')) {
                    return $item->name;
                }
                return Html::link(route('wanted.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations(null, 'wanted.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Get the query object to be processed by table.
     *
     * @return \Illuminate\Database\Query\Builder|Builder
     * @since 2.1
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'wanted.id',
            'wanted.name',
            'wanted.email',
            'wanted.mobile_no',
            'wanted.created_at',
            'wanted.status',
            'wanted.type'
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * @return array
     * @since 2.1
     */
    public function columns()
    {
        return [
            'id' => [
                'name' => 'wanted.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name' => 'wanted.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'type' => [
                'name' => 'wanted.type',
                'title' => trans('core/base::tables.type'),
                'class' => 'text-left',
            ],
            'email' => [
                'name' => 'wanted.email',
                'title' => trans('core/base::tables.email'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name' => 'wanted.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-left',
            ],
        ];
    }

    /**
     * @return array
     * @throws Throwable
     * @since 2.1
     */
    public function buttons()
    {
        // $buttons = $this->addCreateButton(route('wanted.create'), 'wanted.create');

        // return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Wanted::class);
    }

    /**
     * @return array
     * @throws Throwable
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('wanted.deletes'), 'wanted.destroy', parent::bulkActions());
    }

    public function getFilters(): array
    {
        return [
            'wanted.name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
            ],
            'wanted.email' => [
                'title' => trans('core/base::tables.email'),
                'type' => 'text',
            ],
            'wanted.type' => [
                'title' => trans('core/base::tables.type'),
                'type' => 'select',
                'choices' => [
                    'buy' => 'Buy',
                    'rent' => 'Rent'
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getBulkChanges(): array
    {
        return [];
    }

   
}
