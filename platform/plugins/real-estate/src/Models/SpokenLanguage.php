<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SpokenLanguage extends BaseModel
{
    use EnumCastable;

    /**
     * @var string
     */
    protected $table = 're_spoken_languages';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'order',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    /**
     * @return BelongsToMany
     */
    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 're_account_spoken_languages', 'spoken_language_id', 'account_id');
    }
}
