<?php

namespace App\Models;

use Botble\RealEstate\Models\Member;
use Botble\RealEstate\Models\Property;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $fillable = [
        'comment','user_id','property_id','agent_id','rating',
    ];

    public function user()
    {
        return $this->belongsTo(Member::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
