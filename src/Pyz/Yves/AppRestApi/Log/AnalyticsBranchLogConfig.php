<?php
/**
 * Durst - project - AnalyticsBranchLogConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.07.18
 * Time: 11:23
 */

namespace Pyz\Yves\AppRestApi\Log;

use Spryker\Shared\Log\Config\LoggerConfigInterface;

class AnalyticsBranchLogConfig extends AbstractAnalyticsLogConfig implements LoggerConfigInterface
{
    const CHANNEL_NAME = 'webservice_log_analytics_branch';
}