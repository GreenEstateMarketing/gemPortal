<?php

namespace Botble\RealEstate\Repositories\Caches;

use Botble\RealEstate\Repositories\Interfaces\FavouritePropertyInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class FavouritePropertyCacheDecorator extends CacheAbstractDecorator implements FavouritePropertyInterface
{
    public function getFavouritePropertiesByUser($userId, $userType)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function isFavourite($userId, $propertyId, $userType)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}