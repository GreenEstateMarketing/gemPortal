<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;

class CurrencyRepository extends RepositoriesAbstract implements CurrencyInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllCurrencies()
    {
        $data = $this->model
            ->orderBy('order', 'ASC')
            ->get();

        $this->resetModel();

        return $data;
    }

    public function markRestAsNotDefault($id)
    {
        $this->model->where('id', '!=', $id)->update(['is_default' => '0']);
    }
}
