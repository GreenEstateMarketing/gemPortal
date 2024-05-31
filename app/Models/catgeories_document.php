<?php

namespace App\Models;

use Botble\RealEstate\Models\Property;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catgeories_document extends Model
{
    use HasFactory;
    protected $table = 'category_documents';
    protected $fillable = [
        'category_id',
        'document_id',
        'required'
    ];
    public function documents()
    {
        return $this->hasOne(document::class, 'id', 'document_id');
    }

}
