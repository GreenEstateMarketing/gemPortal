<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Botble\RealEstate\Enums\AgentApplicationStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AgentApplication extends BaseModel
{
    use EnumCastable;

    /**
     * @var string
     */
    protected $table = 're_agent_applications';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'education_level',
        'years_of_experience',
        'resume_path',
        'resume_original_name',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => AgentApplicationStatusEnum::class,
    ];

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            're_agent_application_categories',
            'agent_application_id',
            'category_id'
        );
    }
}
