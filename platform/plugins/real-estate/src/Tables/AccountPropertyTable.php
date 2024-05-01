<?php

namespace Botble\RealEstate\Tables;

use Botble\RealEstate\Models\Account;
use Html;
use Illuminate\Support\Arr;
use RvMedia;

class AccountPropertyTable extends PropertyTable
{
    /**
     * @var bool
     */
    public $hasActions = false;

    /**
     * @var bool
     */
    public $hasCheckbox = false;

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
            ->editColumn('name', function ($item) {
                return Html::link(route('public.account.properties.edit', $item->id), $item->name);
            })
            ->editColumn('image', function ($item) {
                return Html::image(RvMedia::getImageUrl($item->image, 'thumb', false, RvMedia::getDefaultImage()),
                    $item->name, ['width' => 50]);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return \BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('expire_date', function ($item) {
                if ($item->never_expired) {
                    return __('Never expired');
                }

                if ($item->expire_date->isPast()) {
                    return Html::tag('span', $item->expire_date->toDateString(), ['class' => 'text-danger'])->toHtml();
                }

                if (now()->diffInDays($item->expire_date) < 3) {
                    return Html::tag('span', $item->expire_date->toDateString(), ['class' => 'text-warning'])->toHtml();
                }

                return $item->expire_date->toDateString();
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            })
            ->editColumn('moderation_status', function ($item) {
                return $item->moderation_status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                $edit = 'public.account.properties.edit';
                $delete = 'public.account.properties.destroy';

                return view('plugins/real-estate::account.table.actions', compact('edit', 'delete', 'item'))->render();
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            're_properties.id',
            're_properties.name',
            're_properties.images',
            're_properties.created_at',
            're_properties.status',
            're_properties.moderation_status',
            're_properties.expire_date',
        ];

        $query = $model
            ->select($select)
            ->where([
                're_properties.author_id'   => auth('account')->user()->getAuthIdentifier(),
                're_properties.author_type' => Account::class,
            ]);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritdoc}
     */
    public function buttons()
    {
        $buttons = [];
        if (auth('account')->user()->canPost()) {
            $buttons = $this->addCreateButton(route('public.account.properties.create'), null);
        }

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Account::class);
    }

    /**
     * @return array
     */
    public function columns()
    {
        $columns = parent::columns();
        Arr::forget($columns, 'author_id');

        $columns['expire_date'] = [
            'name'  => 're_properties.expire_date',
            'title' => __('Expire date'),
            'width' => '150px',
        ];

        return $columns;
    }

    /**
     * @return array
     */
    public function getDefaultButtons(): array
    {
        return ['reload'];
    }
}
