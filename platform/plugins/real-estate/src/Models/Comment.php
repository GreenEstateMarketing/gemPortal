<?php

namespace Botble\RealEstate\Models;


use Botble\RealEstate\Models\Account;
use Botble\ACL\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(Account::class);
    }
    public function admin()
    {
        return $this->BelongsTo(User::class,'admin_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
