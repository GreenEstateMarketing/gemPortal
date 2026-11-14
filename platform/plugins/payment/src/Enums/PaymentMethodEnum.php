<?php

namespace Botble\Payment\Enums;

use Botble\Base\Supports\Enum;

/**
 * @method static PaymentMethodEnum STRIPE()
 * @method static PaymentMethodEnum COD()
 * @method static PaymentMethodEnum BANK_TRANSFER()
 * @method static PaymentMethodEnum CREDIT_CARD()
 */
class PaymentMethodEnum extends Enum
{
    public const STRIPE = 'stripe';
    public const COD = 'cod';
    public const BANK_TRANSFER = 'bank_transfer';
    public const CREDIT_CARD = 'credit_card';

    /**
     * @var string
     */
    public static $langPath = 'plugins/payment::payment.methods';
}
