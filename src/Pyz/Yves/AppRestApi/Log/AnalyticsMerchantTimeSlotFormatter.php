<?php
/**
 * Durst - project - AnalyticsMerchantTimeSlotFormatter.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-21
 * Time: 13:07
 */

namespace Pyz\Yves\AppRestApi\Log;


use Monolog\Formatter\LineFormatter;

class AnalyticsMerchantTimeSlotFormatter extends LineFormatter
{
    public const CONTEXT_MERCHANT_ID = 'merchant_id';
    public const CONTEXT_ZIP_CODE = 'zip_code';
    public const CONTEXT_CART = 'cart';
    public const FORMAT = "%datetime%;%context." . self::CONTEXT_ZIP_CODE .
        "%;%context." . self::CONTEXT_MERCHANT_ID .
        "%;%context." . self::CONTEXT_CART . "%\n";

    /**
     * AnalyticsMerchantTimeSlotFormatter constructor.
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
