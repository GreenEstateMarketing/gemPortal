<?php

namespace Botble\RealEstate\Providers;

use BaseHelper;
use Botble\RealEstate\Models\Category;
use Html;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Botble\RealEstate\Repositories\Interfaces\ConsultInterface;
use Menu;
use Throwable;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @var Collection
     */
    protected $unreadConsults = [];

    /**
     * @throws Throwable
     */
    public function boot()
    {
        add_filter(BASE_FILTER_TOP_HEADER_LAYOUT, [$this, 'registerTopHeaderNotification'], 130);
        add_filter(BASE_FILTER_APPEND_MENU_NAME, [$this, 'getUnReadCount'], 130, 2);

        add_filter(IS_IN_ADMIN_FILTER, [$this, 'setInAdmin'], 20, 0);

        if (defined('MENU_ACTION_SIDEBAR_OPTIONS')) {
            Menu::addMenuOptionModel(Category::class);
            add_action(MENU_ACTION_SIDEBAR_OPTIONS, [$this, 'registerMenuOptions'], 13);
        }
    }

    /**
     * @return bool
     */
    public function setInAdmin(): bool
    {
        return in_array(request()->segment(1), ['account', BaseHelper::getAdminPrefix()]);
    }

    /**
     * @param string $options
     * @return string
     *
     * @throws Throwable
     */
    public function registerTopHeaderNotification($options)
    {
        if (Auth::user()->hasPermission('consults.edit')) {
            $consults = $this->setUnreadConsults();

            if ($consults->count() == 0) {
                return null;
            }

            return $options . view('plugins/real-estate::notification', compact('consults'))->render();
        }

        return $options;
    }

    /**
     * @param int $number
     * @param string $menuId
     * @return string
     * @throws BindingResolutionException
     */
    public function getUnReadCount($number, $menuId)
    {
        if ($menuId == 'cms-plugins-consult') {
            $unread = count($this->setUnreadConsults());
            if ($unread > 0) {
                return Html::tag('span', (string)$unread, ['class' => 'badge badge-success'])->toHtml();
            }
        }

        return $number;
    }

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function setUnreadConsults(): Collection
    {
        if (!$this->unreadConsults) {
            $this->unreadConsults = $this->app->make(ConsultInterface::class)
                ->getUnread(['id', 'name', 'email', 'phone', 'created_at']);
        }

        return $this->unreadConsults;
    }

    /**
     * Register sidebar options in menu
     * @throws Throwable
     */
    public function registerMenuOptions()
    {
        if (Auth::user()->hasPermission('property_category.index')) {
            Menu::registerMenuOptions(Category::class, trans('plugins/real-estate::category.menu'));
        }
    }
}
