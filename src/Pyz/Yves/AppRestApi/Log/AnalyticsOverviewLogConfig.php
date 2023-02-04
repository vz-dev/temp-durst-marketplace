<?php
/**
 * Durst - project - AnalyticsOverviewLogConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-11-05
 * Time: 14:00
 */

namespace Pyz\Yves\AppRestApi\Log;


use Spryker\Shared\Log\Config\LoggerConfigInterface;

class AnalyticsOverviewLogConfig extends AbstractAnalyticsLogConfig implements LoggerConfigInterface
{
    public const CHANNEL_NAME = 'webservice_log_analytics_overview';
}
