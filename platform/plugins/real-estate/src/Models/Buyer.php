<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;

class Buyer extends BaseModel
{
    protected $table = 'buyer';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'property_id',
        'seller_id',
        'agent_id',
        'amount',
        'transaction_type'
    ];
}