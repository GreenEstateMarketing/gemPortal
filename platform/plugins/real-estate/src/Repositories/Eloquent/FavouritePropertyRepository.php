<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\RealEstate\Repositories\Interfaces\FavouritePropertyInterface;

class FavouritePropertyRepository extends RepositoriesAbstract implements FavouritePropertyInterface
{
    public function getFavouritePropertiesByUser($userId, $userType)
    {
        return $this->model->where('user_id', $userId)->where('user_type', $userType)->get();
    }

    public function isFavourite($userId, $propertyId, $userType)
    {
        return $this->model->where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->where('user_type', $userType)
            ->exists();
    }
}