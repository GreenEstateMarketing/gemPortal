<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class document extends Model
{
    use HasFactory;
    protected $table='documents';
    protected $fillable = [
        'name',
        'type',
    ];
    public function category_document()
    {
        return $this->hasOne(catgeories_document::class,'document_id','id');
    }
}
