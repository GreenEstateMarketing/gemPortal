<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;
use Html;

/**
 * @method static PropertyStatusEnum SELLING()
 * @method static PropertyStatusEnum SOLD()
 * @method static PropertyStatusEnum RENTING()
 * @method static PropertyStatusEnum RENTED()
 * @method static PropertyStatusEnum BUILDING()
 * * @method static PropertyStatusEnum CLOSED()
 * @method static PropertyStatusEnum UNPUBLISHED()
 */
class PropertyStatusEnum extends Enum
{
    public const SELLING = 'selling';
    public const SOLD = 'sold';
    public const RENTING = 'renting';
    public const RENTED = 'rented';
    public const BUILDING = 'building';
    public const CLOSED = 'closed';
    public const UNPUBLISHED = 'unpublished';

    /**
     * @var string
     */
    public static $langPath = 'plugins/real-estate::property.statuses';

    /**
     * @return string
     */
    public function toHtml()
    {
        switch ($this->value) {
            case self::SELLING:
                return Html::tag('span', self::SELLING()->label(), ['class' => 'label-success status-label'])
                    ->toHtml();
            case self::SOLD:
                return Html::tag('span', self::SOLD()->label(), ['class' => 'label-danger status-label'])
                    ->toHtml();
            case self::RENTING:
                return Html::tag('span', self::RENTING()->label(), ['class' => 'label-success status-label'])
                    ->toHtml();
            case self::RENTED:
                return Html::tag('span', self::RENTED()->label(), ['class' => 'label-danger status-label'])
                    ->toHtml();
            case self::BUILDING:
                return Html::tag('span', self::BUILDING()->label(), ['class' => 'label-info status-label'])
                    ->toHtml();
            case self::CLOSED:
                return Html::tag('span', self::CLOSED()->label(), ['class' => 'label-secondary status-label'])
                    ->toHtml();
            case self::UNPUBLISHED:
                return Html::tag('span', self::UNPUBLISHED()->label(), ['class' => 'label-default status-label'])
                    ->toHtml();
            default:
                return null;
        }
    }
}
