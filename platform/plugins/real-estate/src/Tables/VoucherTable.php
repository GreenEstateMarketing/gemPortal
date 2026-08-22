<?php

namespace Botble\RealEstate\Tables;

use Auth;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\RealEstate\Repositories\Interfaces\VoucherInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\RealEstate\Models\Package;

class VoucherTable extends TableAbstract
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
     * PackageTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlDevTool
     * @param PackageInterface $packageRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlDevTool, VoucherInterface $voucherRepository)
    {
        $this->repository = $voucherRepository;
        $this->setOption('id', 'table-plugins-package');
        parent::__construct($table, $urlDevTool);

        if (!Auth::user()->hasAnyPermission(['voucher.edit', 'voucher.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     * @since 2.1
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('code', function ($item) {
                if (!Auth::user()->hasPermission('voucher.edit')) {
                    return $item->code;
                }
                return Html::link(route('voucher.edit', $item->id), $item->code);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return \BaseHelper::formatDate($item->created_at);
            });
            /*->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });*/

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('voucher.edit', 'voucher.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Get the query object to be processed by table.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     * @since 2.1
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'vouchers.id',
            'vouchers.code',
            'vouchers.expires_at',
            'vouchers.created_at'
           /* 'vouchers.status',*/
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
                'name'  => 'vouchers.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'code' => [
                'name'  => 'vouchers.code',
                'title' => trans('core/base::tables.code'),
                'class' => 'text-left',
            ],

            'expires_at' => [
                'name'  => 'vouchers.expires_at',
                'title' => trans('core/base::tables.expires_at'),
                'width' => '100px',
            ],
            'created_at' => [
                'name'  => 'vouchers.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ]
            /*'status' => [
                'name'  => 're_packages.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],*/
        ];
    }

    /**
     * @return array
     * @since 2.1
     * @throws \Throwable
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('voucher.create'), 'voucher.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Package::class);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('voucher.deletes'), 'voucher.destroy', parent::bulkActions());
    }

    /**
     * @return array
     */
    public function getBulkChanges(): array
    {
        return [
            'vouchers.code' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ]

        ];
    }
}
