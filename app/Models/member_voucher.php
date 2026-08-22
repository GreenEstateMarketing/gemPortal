<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class member_voucher extends Model
{
    use HasFactory;
    protected $table = 'member_voucher';
    protected $fillable = [
        'id',
        'member_id ',
        'voucher_id '

    ];
}
