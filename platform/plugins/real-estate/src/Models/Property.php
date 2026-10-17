<?php

namespace Botble\RealEstate\Models;

use App\Models\description_template;
use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\Location\Models\City;
use Botble\Location\Models\CityArea;
use Botble\Location\Models\Country;
use Botble\Location\Models\State;
use Botble\RealEstate\Enums\PropertyPeriodEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use App\Models\Rating;

class Property extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 're_properties';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'content',
        'location',
        'images',
        'project_id',
        'number_bedroom',
        'number_bathroom',
        'number_floor',
        'square',
        'price',
        'status',
        'is_featured',
        'currency_id',
        'country_id', 
        'state_id',
        'city_id',
        'city_area_id',
        'period',
        'author_id',
        'author_type',
        'category_id',
        'expire_date',
        'auto_renew',
        'latitude',
        'longitude',
        'member_id',
        'documents',
        'gem_listing_number',
        'reject_reason',
        'built_in',
        'verified',
        'verified_by_admin',
        'contract_signed_by_member',
        'contract_signed_by_agent',
        'contract_signed_by_admin',
        'contract_finalized_at',
        'price_unit',
        'wizard_step',
        'submission_status',
        'wizard_role',
        'last_wizard_activity_at',
        'date_published',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => PropertyStatusEnum::class,
        'moderation_status' => ModerationStatusEnum::class,
        'type' => PropertyTypeEnum::class,
        'period' => PropertyPeriodEnum::class,
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'expire_date',
        'last_wizard_activity_at',
        'contract_finalized_at',
        'date_published',
    ];

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id')->withDefault();
    }

    /**
     * @return BelongsToMany
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 're_property_features', 'property_id', 'feature_id');
    }

    /**
     * @return BelongsToMany
     */
    public function facilities(): BelongsToMany
    {
        return $this->morphToMany(Facility::class, 'reference', 're_facilities_distances')->withPivot('distance');
    }

    /**
     * @param string $value
     * @return array
     */
    public function getImagesAttribute($value)
    {
        try {
            if ($value === '[null]') {
                return [];
            }

            return json_decode($value) ?: [];
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * @return string|null
     */
    public function getImageAttribute(): ?string
    {
        return Arr::first($this->images) ?? null;
    }

    /**
     * @return string
     */
    public function getSquareTextAttribute()
    {
        return getDefaultAreaByUnit($this->square, setting('real_estate_square_unit', 'm²')) . ' ' . setting('real_estate_square_unit', 'm²');
    }

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class)->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class)->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class)->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function cityArea(): BelongsTo
    {
        return $this->belongsTo(CityArea::class)->withDefault();
    }

    /**
     * @return MorphTo
     */
    public function author(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id')->withDefault();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->where('expire_date', '>=', now()->toDateTimeString())
                ->orWhere('never_expired', true);
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($query) {
            $query->where('expire_date', '<', now()->toDateTimeString())
                ->where('never_expired', false);
        });
    }

    /**
     * How long a listing stays publicly visible after the payment that
     * published (or republished) it, before it needs paying for again.
     */
    const PUBLISH_WINDOW_MONTHS = 3;

    /**
     * The earliest date_published a listing can have and still be
     * considered currently published. Shared by scopePublished() and by
     * every raw-condition-array query elsewhere that can't use the scope
     * directly (e.g. repository/theme code building ['field', 'op', value]
     * triples rather than chaining the query builder).
     */
    public static function publishWindowCutoff(): \Illuminate\Support\Carbon
    {
        return now()->subMonths(self::PUBLISH_WINDOW_MONTHS);
    }

    /**
     * Whether this listing is currently within its 3-month publishing
     * window - i.e. actually visible on /properties and in search right
     * now, not merely "approved at some point in the past".
     */
    public function isPublished(): bool
    {
        return (string) $this->moderation_status === ModerationStatusEnum::APPROVED
            && $this->date_published
            && $this->date_published->gte(self::publishWindowCutoff());
    }

    /**
     * Approved, but the 3-month publishing window has lapsed - the listing
     * is currently hidden from the public and needs another 1-credit
     * payment to come back. Distinct from "never approved"/"rejected",
     * which this deliberately excludes.
     */
    public function needsRepublish(): bool
    {
        return (string) $this->moderation_status === ModerationStatusEnum::APPROVED
            && $this->date_published
            && $this->date_published->lt(self::publishWindowCutoff());
    }

    /**
     * The date this listing's current publishing window actually ends -
     * i.e. date_published + 3 months, not date_published itself. Only
     * meaningful once date_published is set.
     */
    public function publishWindowEndsAt(): \Illuminate\Support\Carbon
    {
        return $this->date_published->copy()->addMonths(self::PUBLISH_WINDOW_MONTHS);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('moderation_status', ModerationStatusEnum::APPROVED)
            ->where('date_published', '>=', self::publishWindowCutoff());
    }

    public function user()
    {
        return $this->belongsTo(Account::class, 'author_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function getTemplate()
    {
        return description_template::where('status', 1)->first();
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function contracts()
    {
        return $this->hasMany(PropertyContract::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(PropertyStatusLog::class)->latest();
    }

    public function buyer()
    {
        return $this->hasOne(Buyer::class, 'property_id');
    }

    /**
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->submission_status === 'draft';
    }

    /**
     * @return bool
     */
    public function isSubmitted(): bool
    {
        return $this->submission_status === 'submitted';
    }
}
