<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;


class FavouriteProperty extends BaseModel
{
    protected $table = 'favourite_properties';

    protected $fillable = [
        'user_id',
        'property_id',
        'user_type',
    ];

    
}