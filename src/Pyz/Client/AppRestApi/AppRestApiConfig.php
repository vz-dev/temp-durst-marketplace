<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 21.02.18
 * Time: 09:58
 */

namespace Pyz\Client\AppRestApi;

use Pyz\Shared\AppRestApi\AppRestApiConstants;
use Spryker\Client\Kernel\AbstractBundleConfig;
use Spryker\Shared\Application\ApplicationConstants;

class AppRestApiConfig extends AbstractBundleConfig
{
    /**
     * Limit of results to be returned by the elasticsearch queries
     */
    public const SEARCH_LIMIT = 10;

    /**
     * @return string
     */
    public function getProjectTimeZone() : string
    {
        return $this
            ->get(ApplicationConstants::PROJECT_TIMEZONE);
    }

    /**
     * @return int
     */
    public function getTimeSlotsDayLimit(): int
    {
        return $this
            ->get(AppRestApiConstants::API_TIME_SLOTS_DAY_LIMIT);
    }

    /**
     * @return int
     */
    public function getGMTimeSlotsDayLimit(): int
    {
        return $this
            ->get(AppRestApiConstants::API_GM_TIME_SLOTS_DAY_LIMIT);
    }
}
