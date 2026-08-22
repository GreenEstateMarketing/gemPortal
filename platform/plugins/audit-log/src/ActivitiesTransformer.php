<?php

namespace Botble\AuditLog;

use Botble\AuditLog\Listeners\EncryptionTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivitiesTransformer
{
    use EncryptionTrait;

    public function transform($activities)
    {
        $transformedItems = $activities->getCollection()->map(function ($activity) {
            return $this->transformSingle($activity);
        });
    
        // Create a new paginator instance with the transformed items
        return new LengthAwarePaginator(
            $transformedItems,                    // Transformed items
            $activities->total(),                 // Total items count
            $activities->perPage(),               // Items per page
            $activities->currentPage(),           // Current page
            ['path' => $activities->path()]       // Path for pagination links
        );
    }

    public function transformSingle($activity)
    {

        $activity->user_agent = $this->decryptWithPrivateKey($activity->user_agent);
        $activity->module = $this->decryptWithPrivateKey($activity->module);
        $activity->request = $this->decryptWithPrivateKey($activity->request);
        $activity->action = $this->decryptWithPrivateKey($activity->action);
        $activity->ip_address = $this->decryptWithPrivateKey($activity->ip_address);
        $activity->refernce_user = $this->decryptWithPrivateKey($activity->refernce_user);
        $activity->refernce_id = $this->decryptWithPrivateKey($activity->refernce_id);
        $activity->reference_name = $this->decryptWithPrivateKey($activity->reference_name);
        $activity->type = $this->decryptWithPrivateKey($activity->type);

        return $activity;
    }
}