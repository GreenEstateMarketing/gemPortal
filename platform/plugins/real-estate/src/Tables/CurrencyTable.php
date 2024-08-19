<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\Document;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Auth;
use Html;

class CurrencyTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlDevTool, CurrencyInterface $currencyRepo)
    {
        $this->repository = $currencyRepo;
        $this->setOption('id', 'table-plugins-currencies');
        parent::__construct($table, $urlDevTool);

        if (!Auth::user()->hasAnyPermission(['currencies.edit', 'currencies.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('title', function ($item) {
                return $item->title;
            })
            ->editColumn('symbol', function ($item) {
                return $item->symbol;
            })
            ->editColumn('is_prefix_symbol', function ($item) {
                return $item->is_prefix_symbol;
            })
            ->editColumn('decimals', function ($item) {
                return $item->decimals;
            })
            ->editColumn('is_default', function ($item) {
                return $item->is_default;
            })
            ->editColumn('exchange_rate', function ($item) {
                return $item->exchange_rate;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return \BaseHelper::formatDate($item->created_at);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('currencies.edit', 'currencies.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            're_currencies.id',
            're_currencies.title',
            're_currencies.symbol',
            're_currencies.is_prefix_symbol',
            're_currencies.decimals',
            're_currencies.is_default',
            're_currencies.exchange_rate',
            're_currencies.created_at'
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    public function columns()
    {
        return [
            'id' => [
                'name' => 're_currencies.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'title' => [
                'name' => 're_currencies.title',
                'title' => trans('plugins/real-estate::currency.name'),
                'class' => 'text-left',
            ],
            'symbol' => [
                'name' => 're_currencies.symbol',
                'title' => trans('plugins/real-estate::currency.symbol'),
                'width' => '100px',
            ],
            'is_prefix_symbol' => [
                'name' => 're_currencies.is_prefix_symbol',
                'title' => trans('plugins/real-estate::currency.is_prefix_symbol'),
                'width' => '100px',
            ],
            'decimals' => [
                'name' => 're_currencies.decimals',
                'title' => trans('plugins/real-estate::currency.decimals'),
                'width' => '100px',
            ],
            'is_default' => [
                'name' => 're_currencies.is_default',
                'title' => trans('plugins/real-estate::currency.is_default'),
                'width' => '100px',
            ],
            'exchange_rate' => [
                'name' => 're_currencies.exchange_rate',
                'title' => trans('plugins/real-estate::currency.exchange_rate'),
                'width' => '100px',
            ],
            'created_at' => [
                'name' => 're_currencies.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ]
        ];
    }

    public function buttons()
    {
        $buttons = $this->addCreateButton(route('currencies.create'), 'currencies.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Document::class);
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('currencies.deletes'), 'currencies.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            're_currencies.is_prefix_symbol' => [
                'title'    => 'Prefix Symbol',
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            're_currencies.decimals' => [
                'title'    => 'Decimals',
                'type'     => 'text',
                'validate' => 'required|max:120',
            ]
        ];
    }

    public function getFilters(): array
    {
        return [
            're_currencies.title' => [
                'title' => 'Title',
                'type' => 'text',
            ],
            're_currencies.symbol' => [
                'title' => 'Symbol',
                'type' => 'text',
            ],
            're_currencies.is_prefix_symbol' => [
                'title' => 'Prefix Symbol',
                'type' => 'select',
                'choices' => [
                    '0' => 'NO',
                    '1' => 'YES'
                ],
            ],
            're_currencies.decimals' => [
                'title' => 'Decimals',
                'type' => 'text',
            ],
            're_currencies.exchange_rate' => [
                'title' => 'Exchange Rate',
                'type' => 'text',
            ],
        ];
    }
}