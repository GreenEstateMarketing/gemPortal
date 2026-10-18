<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

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
        'parent_id',
        'for_sale',
        'for_rent',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
        'for_sale' => 'boolean',
        'for_rent' => 'boolean',
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

    public function scopeForType(Builder $query, string $type): Builder
    {
        return $query->where($type === PropertyTypeEnum::RENT ? 'for_rent' : 'for_sale', true);
    }

    protected static function boot()
    {
        parent::boot();

        // The wizard caches the full category list (including for_sale/
        // for_rent) for 30 minutes - without this, an admin toggling a
        // category's type flags wouldn't be reflected in the wizard until
        // that TTL happened to expire.
        static::saved(function () {
            Cache::forget('wizard_ref_categories_v2');
        });

        static::deleted(function () {
            Cache::forget('wizard_ref_categories_v2');
        });
    }
}
