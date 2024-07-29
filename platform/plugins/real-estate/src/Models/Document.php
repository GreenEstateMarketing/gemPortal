<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Document extends BaseModel
{
    protected $table = 'documents';

    protected $fillable = [
        'name',
        'type'
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_documents', 'document_id', 'category_id');
    }
}