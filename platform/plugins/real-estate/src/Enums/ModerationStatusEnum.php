<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;
use Html;

/**
 * @method static ModerationStatusEnum PENDING()
 * @method static ModerationStatusEnum APPROVED()
 * @method static ModerationStatusEnum REJECTED()
 * @method static ModerationStatusEnum CLOSED()
 */
class ModerationStatusEnum extends Enum
{
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';
    public const CLOSED = 'closed';

    /**
     * @var string
     */
    public static $langPath = 'plugins/real-estate::property.moderation-statuses';

    /**
     * @return string
     */
    public function toHtml()
    {
        switch ($this->value) {
            case self::APPROVED:
                return Html::tag('span', self::APPROVED()->label(), ['class' => 'label-info status-label'])
                    ->toHtml();
            case self::PENDING:
                return Html::tag('span', self::PENDING()->label(), ['class' => 'label-warning status-label'])
                    ->toHtml();
            case self::REJECTED:
                return Html::tag('span', self::REJECTED()->label(), ['class' => 'label-danger status-label'])
                    ->toHtml();
            case self::CLOSED:
                return Html::tag('span', self::CLOSED()->label(), ['class' => 'label-secondary status-label'])
                    ->toHtml();
            default:
                return null;
        }
    }
}
