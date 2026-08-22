<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 're_categories';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'order',
        'is_default',
        'parent_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    /**
     * @return HasMany
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'category_documents', 'category_id', 'document_id');
    }

    public function template()
    {
        return $this->hasOne(Template::class, 'category_id');
    }
}
