<?php

namespace Botble\RealEstate\Tables;

use Auth;
use Botble\RealEstate\Models\Template;
use Botble\RealEstate\Repositories\Interfaces\TemplateInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class TemplateTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlDevTool, TemplateInterface $templateRepo)
    {
        $this->repository = $templateRepo;
        $this->setOption('id', 'table-plugins-template');
        parent::__construct($table, $urlDevTool);

        if (!Auth::user()->hasAnyPermission(['template.edit', 'template.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('template.edit')) {
                    return $item->name;
                }
                return Html::link(route('template.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('detail', function ($item) {
                return $item->detail;
            })
            ->editColumn('status', function ($item) {
                return $item->status;
            })
            ->editColumn('category_id', function ($item) {
                return $item->category;
            })
            ->editColumn('created_at', function ($item) {
                return \BaseHelper::formatDate($item->created_at);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('template.edit', 'template.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'description_template.id',
            'description_template.name',
            'description_template.detail',
            'description_template.type',
            'description_template.status',
            'description_template.category_id',
            'description_template.created_at'
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    public function columns()
    {
        return [
            'id' => [
                'name' => 'description_template.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name' => 'description_template.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'detail' => [
                'name' => 'description_template.detail',
                'title' => 'detail',
                'class' => 'text-left',
            ],
            'type' => [
                'name' => 'description_template.type',
                'title' => 'type',
                'class' => 'text-left',
            ],
            'status' => [
                'name' => 'description_template.status',
                'title' => 'status',
                'class' => 'text-left',
            ],
            'category_id' => [
                'name' => 'description_template.category_id',
                'title' => 'Category',
                'class' => 'text-left',
            ],
            'created_at' => [
                'name' => 'description_template.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ]
        ];
    }

    public function buttons()
    {
        $buttons = $this->addCreateButton(route('template.create'), 'template.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Template::class);
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('template.deletes'), 'template.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'template.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'template.detail' => [
                'title'    => 'Detail',
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'template.type' => [
                'title'    => 'Type',
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'template.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'template.category_id' => [
                'title'    => 'Category',
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'template.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }
}