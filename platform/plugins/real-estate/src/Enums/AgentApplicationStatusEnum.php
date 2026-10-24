<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;
use Html;

/**
 * @method static AgentApplicationStatusEnum PENDING()
 * @method static AgentApplicationStatusEnum REVIEWED()
 */
class AgentApplicationStatusEnum extends Enum
{
    public const PENDING = 'pending';
    public const REVIEWED = 'reviewed';

    /**
     * @var string
     */
    public static $langPath = 'plugins/real-estate::agent-application.statuses';

    /**
     * @return string|null
     */
    public function toHtml()
    {
        switch ($this->value) {
            case self::PENDING:
                return Html::tag('span', self::PENDING()->label(), ['class' => 'label-warning status-label'])
                    ->toHtml();
            case self::REVIEWED:
                return Html::tag('span', self::REVIEWED()->label(), ['class' => 'label-success status-label'])
                    ->toHtml();
            default:
                return null;
        }
    }
}
