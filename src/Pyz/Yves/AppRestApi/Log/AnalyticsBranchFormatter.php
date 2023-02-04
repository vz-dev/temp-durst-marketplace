<?php
/**
 * Durst - project - AnalyticsFormatter.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.07.18
 * Time: 12:04
 */

namespace Pyz\Yves\AppRestApi\Log;


use Monolog\Formatter\LineFormatter;

class AnalyticsBranchFormatter extends LineFormatter
{
    const FORMAT = "%datetime%;%context." . self::CONTEXT_ZIP_CODE .
        "%;%context." . self::CONTEXT_MERCHANT_ID . "%\n";
    const CONTEXT_ZIP_CODE = 'zip_code';
    const CONTEXT_MERCHANT_ID = 'merchant_id';

    public function __construct(?string $format = null, ?string $dateFormat = null, bool $allowInlineLineBreaks = false, bool $ignoreEmptyContextAndExtra = false)
    {
        parent::__construct(self::FORMAT, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
    }
}