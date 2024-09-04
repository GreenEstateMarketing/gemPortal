<?php

namespace Botble\RealEstate\Models;

use App\Models\Rating;
use Botble\Base\Supports\Avatar;
use Botble\Media\Models\MediaFile;
use Botble\RealEstate\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Storage;

/**
 * @mixin \Eloquent
 */
class Account extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;

    /**
     * @var string
     */
    protected $table = 're_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'avatar_id',
        'dob',
        'phone',
        'description',
        'gender',
        'credits',
        'agent_area',
        'confirmed_at',
        'image_path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'dob',
        'package_start_date',
        'package_end_date',
    ];

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avatar()
    {
        return $this->belongsTo(MediaFile::class)->withDefault();
    }

    /**
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar->url ? Storage::url($this->avatar->url) : (new Avatar)->create($this->getFullName())->toBase64();
    }

    /**
     * Always capitalize the first name when we retrieve it
     * @param string $value
     * @return string
     */
    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Always capitalize the last name when we retrieve it
     * @param string $value
     * @return string
     */
    public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function properties()
    {
        return $this->morphMany(Property::class, 'author');
    }

    /**
     * @return bool
     */
    public function canPost(): bool
    {
        return true;
    }
    public function getConsults($property_id = '')
    {
        $select = [
            're_consults.id',
            're_consults.name',
            're_consults.phone',
            're_consults.email',
            're_consults.created_at',
            're_consults.status',
        ];

        $query = Consult::select($select)->Join('re_properties', 're_consults.property_id', '=', 're_properties.id')->where('re_properties.author_id', auth('account')->user()->id)->where('re_consults.status', 'unread');
        if ($property_id != '') {
            $query->where('property_id', $property_id);
        }
        $count = $query->count();
        if ($count <= 0)
            $count = '';
        return $count; //need to implement query here // account properties to where with consults table count
    }

    /**
     * @param int $value
     * @return int
     */
    public function getCreditsAttribute($value)
    {
        return $value ? $value : 0;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    /**
     * @return BelongsToMany
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 're_account_packages', 'account_id', 'package_id');
    }
    public function posts()
    {

        return $this->hasMany(Property::class, 'author_id');

    }
    public function getPolygon()
    {
        $res = $this->selectRaw('ST_AsGeoJson(agent_area) as poly_coord')->where('id', '=', auth('account')->user()->id)->get();
        $swapped = $this->swapCoordinates($res[0]->poly_coord);
        return $swapped;

    }
    public function no_of_listings($id)
    {
        return $this->morphMany(Property::class, 'author')->count('id');
    }

    private function swapCoordinates($geoJson)
    {
        $data = json_decode($geoJson, true);
        if ($data) {
            if ($data['type'] === 'Polygon' || $data['type'] === 'MultiPolygon') {
                foreach ($data['coordinates'] as &$polygon) {
                    foreach ($polygon as &$ring) {
                        $ring = array_reverse($ring);
                    }
                }
            }
            return json_encode($data);
        }
    }
}
