<?php

namespace Botble\Contact\Models;

use Botble\Base\Models\BaseModel;

class ContactReply extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'contact_replies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message',
        'contact_id',
    ];
}
