<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\RealEstate\Repositories\Interfaces\MemberActivityLogInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class MemberActivityLogRepository extends RepositoriesAbstract implements MemberActivityLogInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllLogs($accountId, $paginate = 10)
    {
        return $this->model
            ->where('member_id', $accountId)
            ->latest('created_at')
            ->paginate($paginate);
    }
}
