<?php

namespace Botble\RealEstate\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface FavouritePropertyInterface extends RepositoryInterface
{

    public function getFavouritePropertiesByUser($userId, $userType);

    public function isFavourite($userId, $propertyId, $userType);
}