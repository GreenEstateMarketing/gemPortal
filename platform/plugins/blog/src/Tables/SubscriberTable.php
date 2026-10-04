<?php

namespace Botble\Blog\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Blog\Repositories\Interfaces\SubscriberInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class SubscriberTable extends TableAbstract
{
    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, SubscriberInterface $subscriberRepository)
    {
        $this->repository = $subscriberRepository;
        $this->setOption('id', 'table-subscribers');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasPermission('subscribers.destroy')) {
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
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                if ($this->request()->input('action') === 'excel') {
                    return $item->status->getValue();
                }

                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations(null, 'subscribers.destroy', $item);
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
            'blog_subscribers.id',
            'blog_subscribers.email',
            'blog_subscribers.status',
            'blog_subscribers.created_at',
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
            'id'         => [
                'name'  => 'blog_subscribers.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'email'      => [
                'name'  => 'blog_subscribers.email',
                'title' => trans('plugins/blog::subscribers.name'),
                'class' => 'text-left',
            ],
            'status'     => [
                'name'  => 'blog_subscribers.status',
                'title' => trans('plugins/blog::subscribers.status'),
                'width' => '120px',
            ],
            'created_at' => [
                'name'  => 'blog_subscribers.created_at',
                'title' => trans('plugins/blog::subscribers.created_at'),
                'width' => '150px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, [], SubscriberInterface::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('subscribers.deletes'), 'subscribers.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'blog_subscribers.status' => [
                'title'    => trans('plugins/blog::subscribers.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
        ];
    }
}
