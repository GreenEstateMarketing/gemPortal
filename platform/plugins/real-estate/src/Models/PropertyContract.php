<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyContract extends BaseModel
{
    protected $table = 're_property_contracts';

    protected $fillable = [
        'property_id',
        'copy_type',
        'file_path',
        'masked_key',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
