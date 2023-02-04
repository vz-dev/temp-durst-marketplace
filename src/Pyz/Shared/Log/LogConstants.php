<?php
/**
 * Durst - project - LogConstants.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.03.20
 * Time: 15:41
 */

namespace Pyz\Shared\Log;

use Spryker\Shared\Log\LogConstants as SprykerLogConstants;

interface LogConstants extends SprykerLogConstants
{
    public const LOG_MAIL_RECIPIENTS = 'LOG_MAIL_RECIPIENTS';
    public const LOG_SENTRY_HANDLER_ENABLED_FOR_ENVIRONMENTS = 'LOG_SENTRY_HANDLER_ENABLED_FOR_ENVIRONMENTS';
}
