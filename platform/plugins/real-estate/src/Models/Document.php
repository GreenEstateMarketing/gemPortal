<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;

class Document extends BaseModel
{
    protected $table = 'documents';

    protected $fillable = [
        'name',
        'type'
    ];
}