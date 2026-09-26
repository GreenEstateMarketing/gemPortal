<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\SpokenLanguage;
use Botble\RealEstate\Repositories\Interfaces\SpokenLanguageInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Throwable;
use Yajra\DataTables\DataTables;

class SpokenLanguageTable extends TableAbstract
{
    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        SpokenLanguageInterface $spokenLanguageRepository
    ) {
        $this->repository = $spokenLanguageRepository;
        $this->setOption('id', 'plugins-real-estate-spoken-languages');
        parent::__construct($table, $urlGenerator);
    }

    /**
     * @return JsonResponse
     * @throws Throwable
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                return Html::link(route('agent_spoken_language.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('agent_spoken_language.edit', 'agent_spoken_language.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * @return \Illuminate\Database\Query\Builder|Builder
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            're_spoken_languages.id',
            're_spoken_languages.name',
            're_spoken_languages.order',
            're_spoken_languages.status',
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
                'name' => 're_spoken_languages.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name' => 're_spoken_languages.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'order' => [
                'name' => 're_spoken_languages.order',
                'title' => trans('core/base::forms.order'),
                'width' => '80px',
            ],
            'status' => [
                'name' => 're_spoken_languages.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * @return array
     * @throws Throwable
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('agent_spoken_language.create'), 'agent_spoken_language.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, SpokenLanguage::class);
    }

    /**
     * @return array
     * @throws Throwable
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('agent_spoken_language.deletes'), 'agent_spoken_language.destroy',
            parent::bulkActions());
    }

    /**
     * @return array
     */
    public function getBulkChanges(): array
    {
        return [
            're_spoken_languages.name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
        ];
    }
}
