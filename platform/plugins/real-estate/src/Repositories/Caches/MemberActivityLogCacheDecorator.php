<?php

namespace Botble\RealEstate\Repositories\Caches;

use Botble\RealEstate\Repositories\Interfaces\MemberActivityLogInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class MemberActivityLogCacheDecorator extends CacheAbstractDecorator implements MemberActivityLogInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllLogs($accountId, $paginate = 10)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
