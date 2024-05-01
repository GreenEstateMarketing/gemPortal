<?php

namespace Botble\RealEstate\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wanted extends Model
{
    use HasFactory;
    protected $table ='wanted';
    protected $fillable = [
        'name',
        'email',
        'city_id',
        'type',
        'city_area_id',
        'mobile_no',
        'comments',
        'category_id'
    ];
}
