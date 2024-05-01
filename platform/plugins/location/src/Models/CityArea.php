<?php

namespace Botble\Location\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class CityArea extends BaseModel
{
    use EnumCastable;
    use SpatialTrait;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'city_area';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'city_id',
        'parent_id',
        'city_area_name',
        'city_area_location',
    ];

    /**
     * The attributes that are spatial fields.
     *
     * @var array
     */
    protected $spatialFields = [
        'city_area_location'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

}
