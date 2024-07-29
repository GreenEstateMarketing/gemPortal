<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\Document;
use Botble\RealEstate\Repositories\Interfaces\DocumentInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Html;
use Auth;

class DocumentTable extends TableAbstract
{

    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlDevTool, DocumentInterface $propertyDocumentRepo)
    {
        $this->repository = $propertyDocumentRepo;
        $this->setOption('id', 'table-plugins-document');
        parent::__construct($table, $urlDevTool);

        if (!Auth::user()->hasAnyPermission(['document.edit', 'document.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('document.edit')) {
                    return $item->name;
                }
                return Html::link(route('document.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return \BaseHelper::formatDate($item->created_at);
            });
//            ->editColumn('type', function ($item) {
//                return $item->type;
//            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('document.edit', 'document.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'documents.id',
            'documents.name',
            'documents.created_at'
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    public function columns()
    {
        return [
            'id' => [
                'name' => 'documents.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name' => 'documents.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
//            'type' => [
//                'name' => 'documents.type',
//                'title' => trans('core/base::tables.type'),
//                'width' => '100px',
//            ],
            'created_at' => [
                'name' => 'documents.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ]
        ];
    }

    public function buttons()
    {
        $buttons = $this->addCreateButton(route('document.create'), 'document.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Document::class);
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('document.destroy'), 'document.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'documents.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
//            'documents.type' => [
//                'title'    => trans('core/base::tables.status'),
//                'type'     => 'text',
//                'validate' => 'required|max:120',
//            ],
            'documents.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }
}