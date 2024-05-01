<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Html;

class MemberActivityLog extends Model
{
    use HasFactory;
    protected $table = 'member_activity_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'action',
        'user_agent',
        'reference_url',
        'reference_name',
        'ip_address',
        'member_id',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->user_agent = $model->user_agent ? $model->user_agent : request()->userAgent();
            $model->ip_address = $model->ip_address ? $model->ip_address : request()->ip();
            $model->member_id = $model->member_id ? $model->member_id : auth('member')->user()->getAuthIdentifier();
            $model->reference_url = str_replace(url('/'), '', $model->reference_url);
        });
    }
    public function getDescription()
    {
        $name = $this->reference_name;
        if ($this->reference_name && $this->reference_url) {
            $name = Html::link($this->reference_url, $this->reference_name, ['style' => 'color: #1d9977']);
        }

        $time = Html::tag('span', $this->created_at->diffForHumans(), ['class' => 'small italic']);

        return trans('plugins/real-estate::dashboard.actions.' . $this->action, ['name' => $name]) . ' . ' . $time;
    }

}
