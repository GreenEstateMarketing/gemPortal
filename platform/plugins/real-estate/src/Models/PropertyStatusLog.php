<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Botble\ACL\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyStatusLog extends BaseModel
{
    protected $table = 're_property_status_logs';

    protected $fillable = [
        'property_id',
        'admin_id',
        'old_status',
        'new_status',
        'comment',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
