<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;

class Template extends BaseModel
{
    protected $table = 'description_template';

    protected $fillable = [
        'name',
        'detail',
        'type',
        'status',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}