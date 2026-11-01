<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\AgentApplication;
use Botble\RealEstate\Repositories\Eloquent\AgentApplicationRepository;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class AgentApplicationTable extends TableAbstract
{
    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * AgentApplicationTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator)
    {
        $this->repository = new AgentApplicationRepository(new AgentApplication());
        $this->setOption('id', 'table-agent-applications');
        parent::__construct($table, $urlGenerator);

        if (! Auth::user()->hasAnyPermission(['agent-application.edit', 'agent-application.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('name', function ($item) {
                if (! Auth::user()->hasPermission('agent-application.edit')) {
                    return $item->name;
                }

                return Html::link(route('agent-application.edit', $item->id), $item->name);
            })
            ->editColumn('years_of_experience', function ($item) {
                return $item->years_of_experience;
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            })
            ->editColumn('created_at', function ($item) {
                return \BaseHelper::formatDate($item->created_at);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                $extra = '';

                if (Auth::user()->hasPermission('agent-application.edit')) {
                    $extra .= '<a href="' . route('agent-application.edit', $item->id) . '" class="btn btn-icon btn-sm btn-primary" data-toggle="tooltip" data-original-title="' . __('View Application') . '">
                                <i class="fa fa-eye"></i>
                             </a>';
                }

                $extra .= '<a href="' . route('agent-application.resume', $item->id) . '" target="_blank" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="' . __('View Resume') . '">
                                <i class="fa fa-file-alt"></i>
                             </a>';

                return $this->getOperations(null, 'agent-application.destroy', $item, $extra);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            're_agent_applications.id',
            're_agent_applications.name',
            're_agent_applications.email',
            're_agent_applications.phone',
            're_agent_applications.education_level',
            're_agent_applications.years_of_experience',
            're_agent_applications.status',
            're_agent_applications.created_at',
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * @return array
     */
    public function columns()
    {
        return [
            'id' => [
                'name' => 're_agent_applications.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name' => 're_agent_applications.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'email' => [
                'name' => 're_agent_applications.email',
                'title' => trans('core/base::tables.email'),
                'class' => 'text-left',
            ],
            'phone' => [
                'name' => 're_agent_applications.phone',
                'title' => trans('plugins/real-estate::agent-application.phone'),
                'class' => 'text-left',
            ],
            'education_level' => [
                'name' => 're_agent_applications.education_level',
                'title' => trans('plugins/real-estate::agent-application.education_level'),
                'class' => 'text-left',
            ],
            'years_of_experience' => [
                'name' => 're_agent_applications.years_of_experience',
                'title' => trans('plugins/real-estate::agent-application.years_of_experience'),
                'width' => '100px',
            ],
            'status' => [
                'name' => 're_agent_applications.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
            'created_at' => [
                'name' => 're_agent_applications.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * @return array
     */
    public function buttons()
    {
        return [];
    }

    /**
     * @return array
     */
    public function bulkActions(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getBulkChanges(): array
    {
        return [];
    }
}
