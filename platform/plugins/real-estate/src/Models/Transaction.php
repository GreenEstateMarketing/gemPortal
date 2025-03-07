<?php

namespace Botble\RealEstate\Models;

use Botble\ACL\Models\User;
use Botble\Payment\Models\Payment;
use Eloquent;
use Html;

class Transaction extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * @var array
     */
    protected $fillable = [
        'credits',
        'description',
        'user_id',
        'account_id',
        'payment_id',
        'user_type'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class)->withDefault();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        $time = Html::tag('span', $this->created_at->diffForHumans(), ['class' => 'small italic']);

        $creditsLabel = $this->credits > 1 ? 'credits' : 'credit';

        $description = 'You have purchased ' . $this->credits . ' ' . $creditsLabel;
        $paymentChannel = $this->payment->payment_channel == 'credit_card' ? 'Credit Card' : $this->payment->payment_channel;
        if ($this->payment_id) {
            $description .= ' via ' . $paymentChannel . ' ' . $time .
                ': ' . number_format($this->payment->amount, 2, '.', ',') . ' ' . $this->payment->currency;
        }

        return $description;
    }
}
