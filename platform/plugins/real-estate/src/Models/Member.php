<?php

namespace Botble\RealEstate\Models;

use App\Models\Rating;
use Botble\Media\Models\MediaFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use BeyondCode\Vouchers\Traits\CanRedeemVouchers;
class Member extends Authenticatable
{
    use Notifiable;
//    use CanRedeemVouchers;
    protected $table='members';
    protected $guard = 'member';
    use HasFactory;
    protected $fillable = [
        'full_name',
        'email',
        'mobile_no',
        'password',
        'credits'
    ];
    public function properties()
    {
       // return $this->morphMany(Property::class, 'member_id');
       return  $this->hasMany(Property::class, 'member_id', 'id');
    }
    public function avatar()
    {
        return $this->belongsTo(MediaFile::class)->withDefault();
    }
    public function canPost(): bool
    {
        return $this->credits > 0;
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

}
