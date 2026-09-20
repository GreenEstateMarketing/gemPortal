<?php

namespace Botble\RealEstate\Models;

use App\Models\Rating;
use Botble\Base\Supports\Avatar;
use Botble\Media\Models\MediaFile;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Botble\RealEstate\Notifications\ResetPasswordNotification;
use Storage;

//use BeyondCode\Vouchers\Traits\CanRedeemVouchers;
class Member extends Authenticatable
{
    use Notifiable, CanResetPassword;
    protected $table = 'members';
    protected $guard = 'member';
    use HasFactory;
    protected $fillable = [
        'full_name',
        'email',
        'mobile_no',
        'password',
        'credits',
        'remember_token',
        'verification_token',
        'email_verified',
        'signature',
        'signature_source',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'signature',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class, 'member_id', 'id');
    }
    public function avatar()
    {
        return $this->belongsTo(MediaFile::class)->withDefault();
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar->url ? Storage::url($this->avatar->url) : (new Avatar)->create($this->full_name)->toBase64();
    }

    /**
     * @return bool
     */
    public function hasSignature()
    {
        return ! empty($this->signature);
    }

    /**
     * @return string|null
     */
    public function getSignatureDataUriAttribute()
    {
        if (! $this->hasSignature()) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode($this->signature);
    }
    public function canPost(): bool
    {
        return true;
    }
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 're_account_packages', 'account_id', 'package_id');
    }
    public function getFullName()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

}
