<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\Member;
use Botble\RealEstate\Repositories\Interfaces\MemberInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Html;

class MemberTable extends TableAbstract
{

    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, MemberInterface $memberRepository)
    {
        $this->repository = $memberRepository;
        $this->setOption('id', 'table-members');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['member.edit', 'member.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('full_name', function ($item) {
                if (!Auth::user()->hasPermission('member.edit')) {
                    return $item->full_name;
                }

                return Html::link(route('member.edit', $item->id), $item->full_name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return \BaseHelper::formatDate($item->created_at);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {

                $propertiesUrl = route('property.index', [
                    'filter_table_id' => 'plugins-real-estate-properties',
                    'class' => 'Botble\RealEstate\Tables\PropertyTable',
                    'filter_columns[]' => 're_properties.member_id',
                    'filter_operators[]' => '=',
                    'filter_values[]' => $item->id,
                ]);

                $propertiesButton = '<a href="' . $propertiesUrl . '" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="View Properties">
                                <i class="fa fa-building"></i> 
                             </a>';

                return $this->getOperations('member.edit', null, $item, $propertiesButton);
            })
            ->escapeColumns([])
            ->make(true);


    }

    public function query()
    {
        $model = app(MemberInterface::class)->getModel();
        $select = [
            'members.id',
            'members.full_name',
            'members.email',
            'members.created_at',
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    public function columns()
    {
        return [
            'id'         => [
                'name'  => 'members.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'full_name' => [
                'name'  => 'members.full_name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'email'      => [
                'name'  => 'members.email',
                'title' => trans('core/base::tables.email'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'members.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
        ];
    }

    public function buttons()
    {
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, [], Member::class);
    }

    public function bulkActions(): array
    {
        return [];
    }

    public function getBulkChanges(): array
    {
        return [
            'members.full_name' => [
                'title'    =>'Full Name',
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'members.email'      => [
                'title'    => trans('core/base::tables.email'),
                'type'     => 'text',
                'validate' => 'required|max:120|email',
            ],
        ];
    }

    public function getDefaultButtons(): array
    {
        return [
            'reload',
        ];
    }

}