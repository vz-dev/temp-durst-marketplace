<?php
/**
 * Durst - project - AnalyticsTimeSlotLogConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.07.18
 * Time: 11:26
 */

namespace Pyz\Yves\AppRestApi\Log;

use Spryker\Shared\Log\Config\LoggerConfigInterface;

class AnalyticsTimeSlotLogConfig extends AbstractAnalyticsLogConfig implements LoggerConfigInterface
{
    const CHANNEL_NAME = 'webservice_log_analytics_time_slot';
}