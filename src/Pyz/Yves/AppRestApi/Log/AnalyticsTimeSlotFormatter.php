<?php
/**
 * Durst - project - AnalyticsTimeSlotFormatter.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.07.18
 * Time: 13:33
 */

namespace Pyz\Yves\AppRestApi\Log;


use Monolog\Formatter\LineFormatter;

class AnalyticsTimeSlotFormatter extends LineFormatter
{
    const FORMAT = "%datetime%;%context." . self::CONTEXT_ZIP_CODE .
        "%;%context." . self::CONTEXT_MERCHANT_IDS .
        "%;%context." . self::CONTEXT_CART . "%\n";
    const CONTEXT_ZIP_CODE = 'zip_code';
    const CONTEXT_MERCHANT_IDS = 'merchant_ids';
    const CONTEXT_CART = 'cart';

    /**
     * AnalyticsTimeSlotFormatter constructor.
     * @param null|string $format
     * @param null|string $dateFormat
     * @param bool $allowInlineLineBreaks
     * @param bool $ignoreEmptyContextAndExtra
     */
    public function __construct(?string $format = null, ?string $dateFormat = null, bool $allowInlineLineBreaks = false, bool $ignoreEmptyContextAndExtra = false)
    {
        parent::__construct(self::FORMAT, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
    }
}