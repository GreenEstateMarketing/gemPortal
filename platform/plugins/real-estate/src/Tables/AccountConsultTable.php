<?php

namespace Botble\RealEstate\Tables;

use Auth;
use BaseHelper;
use Botble\RealEstate\Enums\ConsultStatusEnum;
use Botble\RealEstate\Models\Consult;
use Botble\RealEstate\Repositories\Interfaces\ConsultInterface;
use Botble\Support\Http\Requests\Request;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Throwable;
use Yajra\DataTables\DataTables;

class AccountConsultTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;
    public $hasCheckbox = false;
    /**
     * @var bool
     */
    protected $hasFilter = false;

    /**
     * ConsultTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlDevTool
     * @param ConsultInterface $consultRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlDevTool, ConsultInterface $consultRepository)
    {
        $this->repository = $consultRepository;
        $this->setOption('id', 'plugins-real-estate-consult');
        $this->setOption('class', 'table table-striped table-hover vertical-middle dataTable no-footer dtr-inline');
        $this->table = $table;
        $this->ajaxUrl = $urlDevTool->current();

       // parent::__construct($table, $urlDevTool);

       /* if (!Auth::user()->hasAnyPermission(['consult.edit', 'consult.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }*/
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
                //if (!Auth::user()->hasPermission('consult.edit')) {

                //}
                return Html::link(route('public.account.consult.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
          /*  ->addColumn('operations', function ($item) {
                return $this->getOperations('public.account.consult.edit', 'public.account.consult.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
          */
        ->addColumn('operations', function ($item) {
        $edit = 'public.account.consult.edit';
        $delete = 'public.account.consult.destroy';

        return view('plugins/real-estate::account.table.consults-actions', compact('edit', 'delete','item'))->render();
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
        //echo $model;exit;
        $select = [
            're_consults.id',
            're_consults.name',
            're_consults.phone',
            're_consults.email',
            're_consults.created_at',
            're_consults.status',
            're_properties.name  AS tag_name',
        ];
        $query=$model->select($select)->Join('re_properties', 're_consults.property_id', '=', 're_properties.id')->where('re_properties.author_id',auth('account')->user()->id);

        if($this->property_id!="")
        {
            $query->where('property_id',$this->property_id);
        }
        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * @return array
     * @since 2.1
     */
    public function columns()
    {
        return [
            'id'         => [
                'name'  => 're_consults.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name'       => [
                'name'  => 're_consults.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],

            'tag_name'       => [
                'name'  => 're_properties.name',
                'title' => trans('plugins/real-estate::consult.property'),
                'class' => 'text-left',
            ],
            'email'      => [
                'name'  => 're_consults.email',
                'title' => trans('plugins/real-estate::consult.email.header'),
                'class' => 'text-left',
            ],
            'phone'      => [
                'name'  => 're_consults.phone',
                'title' => trans('plugins/real-estate::consult.phone'),
            ],
            'created_at' => [
                'name'  => 're_consults.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status'     => [
                'name'  => 're_consults.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * @return array
     * @throws Throwable
     * @since 2.1
     */
  /*  public function buttons()
    {
     //   return apply_filters(BASE_FILTER_TABLE_BUTTONS, [], Consult::class);
    }*/

    /**
     * @return array
     * @throws Throwable
     */
   /* public function bulkActions(): array
    {
        return $this->addDeleteAction(route('public.account.consult.deletes'), 'public.account.consult.destroy', parent::bulkActions());
    }*/

    /**
     * @return array
     */
 /*   public function getBulkChanges(): array
    {
        return [
            're_consults.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            're_consults.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => ConsultStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', ConsultStatusEnum::values()),
            ],
            're_consults.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }*/
}
