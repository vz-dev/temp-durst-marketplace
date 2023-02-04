<?php
/**
 * Durst - project - AnalyticsOverviewFormatter.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-11-05
 * Time: 14:02
 */

namespace Pyz\Yves\AppRestApi\Log;


use Monolog\Formatter\LineFormatter;

class AnalyticsOverviewFormatter extends LineFormatter
{
    public const CONTEXT_TIME_SLOT_ID = 'time_slot_id';
    public const CONTEXT_CART = 'cart';

    public const FORMAT = "%datetime%;%context." . self::CONTEXT_TIME_SLOT_ID .
        "%;%context." . self::CONTEXT_CART . "%\n";

    /**
     * AnalyticsOverviewFormatter constructor.
     * @param string|null $format
     * @param string|null $dateFormat
     * @param bool $allowInlineLineBreaks
     * @param bool $ignoreEmptyContextAndExtra
     */
    public function __construct(
        ?string $format = null,
        ?string $dateFormat = null,
        bool $allowInlineLineBreaks = false,
        bool $ignoreEmptyContextAndExtra = false
    )
    {
        parent::__construct(
            self::FORMAT,
            $dateFormat,
            $allowInlineLineBreaks,
            $ignoreEmptyContextAndExtra
        );
    }
}
