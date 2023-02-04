<?php
/**
 * Durst - project - LogConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.03.20
 * Time: 15:39
 */

namespace Pyz\Zed\Log;

use Pyz\Shared\Log\LogConstants;
use Spryker\Zed\Log\LogConfig as SprykerLogConfig;

class LogConfig extends SprykerLogConfig
{
    /**
     * @return array
     */
    public function getLogMailRecipients(): array
    {
        return $this
            ->get(
                LogConstants::LOG_MAIL_RECIPIENTS
            );
    }

    /**
     * @return array
     */
    public function getLogSentryHandlerEnabledForEnvironments(): array
    {
        return $this
            ->get(
                LogConstants::LOG_SENTRY_HANDLER_ENABLED_FOR_ENVIRONMENTS
            );
    }
}
