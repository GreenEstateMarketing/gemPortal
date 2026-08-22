<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;

class CategoryDocument extends BaseModel
{
    protected $table = 'category_documents';

    protected $fillable = [
        'category_id',
        'document_id',
        'required'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Define the relationship to Document
    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
}