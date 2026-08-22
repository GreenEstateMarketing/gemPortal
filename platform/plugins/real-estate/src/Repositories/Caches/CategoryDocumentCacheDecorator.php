<?php

namespace Botble\RealEstate\Repositories\Caches;

use Botble\RealEstate\Repositories\Interfaces\CategoryDocumentInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class CategoryDocumentCacheDecorator extends CacheAbstractDecorator implements CategoryDocumentInterface
{
    public function getByCategoryId($categoryId)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

}