<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\Document;
use Botble\RealEstate\Repositories\Interfaces\CategoryDocumentInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Log;
use Yajra\DataTables\DataTables;
use Auth;
use Html;

class CategoryDocumentTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlDevTool, CategoryDocumentInterface $categoryDocumentRepo)
    {
        $this->repository = $categoryDocumentRepo;
        $this->setOption('id', 'table-plugins-category-document');
        parent::__construct($table, $urlDevTool);

        if (!Auth::user()->hasAnyPermission(['category-document.edit', 'category-document.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('category_id', function ($item) {
                if ($item->category) {
                    return $item->category->name;
                }

                return '';

            })
            ->editColumn('document_id', function ($item) {
                return $item->document->name;
            })
            ->editColumn('required', function ($item) {
                return $item->required;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return \BaseHelper::formatDate($item->created_at);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('category-document.edit', 'category-document.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'category_documents.id',
            'category_documents.category_id',
            'category_documents.document_id',
            'category_documents.required',
            'category_documents.created_at'
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    public function columns()
    {
        return [
            'id' => [
                'name' => 'category_documents.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'category_id' => [
                'name' => 'category_documents.category_id',
                'title' => 'Category',
                'class' => 'text-left',
            ],
            'document_id' => [
                'name' => 'category_documents.document_id',
                'title' => 'Document',
                'width' => '100px',
            ],
            'required' => [
                'name' => 'category_documents.required',
                'title' => 'Required',
                'width' => '100px',
            ],
            'created_at' => [
                'name' => 'category_documents.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ]
        ];
    }

    public function buttons()
    {
        $buttons = $this->addCreateButton(route('category-document.create'), 'category-document.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Document::class);
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('category-document.deletes'), 'category-document.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'category_documents.category_id' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'documents.document_id' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'category_documents.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'date',
            ],
        ];
    }
}