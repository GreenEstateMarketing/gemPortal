<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class description_template extends Model
{
    use HasFactory;
    protected $table='description_template';
    protected $fillable = [
        'name',
        'detail',
        'type',
        'status'

    ];
}
