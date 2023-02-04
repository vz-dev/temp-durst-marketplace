<?php
/**
 * Durst - project - AnalyticsMerchantTimeSlotLogConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-21
 * Time: 13:06
 */

namespace Pyz\Yves\AppRestApi\Log;


use Spryker\Shared\Log\Config\LoggerConfigInterface;

class AnalyticsMerchantTimeSlotLogConfig extends AbstractAnalyticsLogConfig implements LoggerConfigInterface
{
    public const CHANNEL_NAME = 'webservice_log_analytics_merchant_time_slot';
}
