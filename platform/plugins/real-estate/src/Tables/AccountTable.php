<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\Account;
use Html;
use Illuminate\Support\Facades\Auth;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class AccountTable extends TableAbstract
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
     * AccountTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param AccountInterface $accountRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AccountInterface $accountRepository)
    {
        $this->repository = $accountRepository;
        $this->setOption('id', 'table-accounts');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['account.edit', 'account.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @since 2.1
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('first_name', function ($item) {
                if (!Auth::user()->hasPermission('account.edit')) {
                    return $item->getFullName();
                }

                return Html::link(route('account.edit', $item->id), $item->getFullName());
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return \BaseHelper::formatDate($item->created_at);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                $operations = $this->getOperations('account.edit', 'account.destroy', $item);

                // Add a new button for the real estate properties route
                $propertiesUrl = route('property.index', [
                    'filter_table_id' => 'plugins-real-estate-properties',
                    'class' => 'Botble\RealEstate\Tables\PropertyTable',
                    'filter_columns[]' => 're_properties.author_id',
                    'filter_operators[]' => '=',
                    'filter_values[]' => $item->id,
                ]);
                $propertiesButton = '<a href="' . $propertiesUrl . '" class="btn btn-info btn-sm">
                                    <i class="fa fa-building"></i> ' . trans('View Properties') . '
                                 </a>';

                return $operations . ' ' . $propertiesButton;
            })
            ->escapeColumns([])
            ->make(true);
    }


    /**
     * Get the query object to be processed by the table.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     *
     * @since 2.1
     */
    public function query()
    {
        $model = app(AccountInterface::class)->getModel();
        $select = [
            're_accounts.id',
            're_accounts.first_name',
            're_accounts.last_name',
            're_accounts.email',
            're_accounts.created_at',
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * @return array
     *
     * @since 2.1
     */
    public function columns()
    {
        return [
            'id'         => [
                'name'  => 're_accounts.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'first_name' => [
                'name'  => 're_accounts.first_name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'email'      => [
                'name'  => 're_accounts.email',
                'title' => trans('core/base::tables.email'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 're_accounts.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * @return array
     *
     * @since 2.1
     * @throws \Throwable
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('account.create'), 'account.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Account::class);
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('account.deletes'), 'account.destroy', parent::bulkActions());
    }

    /**
     * @return array
     */
    public function getBulkChanges(): array
    {
        return [
            're_accounts.first_name' => [
                'title'    => trans('plugins/real-estate::account.first_name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            're_accounts.last_name' => [
                'title'    => trans('plugins/real-estate::account.last_name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            're_accounts.email'      => [
                'title'    => trans('core/base::tables.email'),
                'type'     => 'text',
                'validate' => 'required|max:120|email',
            ],
            're_accounts.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }
}
