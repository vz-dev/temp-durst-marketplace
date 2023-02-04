<?php
/**
 * Durst - project - DebugLogFormatter.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 29.01.19
 * Time: 09:54
 */

namespace Pyz\Zed\HeidelpayRest\Business\Debug;

use Monolog\Formatter\LineFormatter;

class DebugLogFormatter extends LineFormatter
{
    public const FORMAT = "%datetime%;%context." . self::CONTEXT_MESSAGE . "%\n";
    public const CONTEXT_MESSAGE = 'message';

    public function __construct(?string $format = null, ?string $dateFormat = null, bool $allowInlineLineBreaks = false, bool $ignoreEmptyContextAndExtra = false)
    {
        parent::__construct(self::FORMAT, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
    }
}
