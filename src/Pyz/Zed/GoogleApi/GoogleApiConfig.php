<?php
/**
 * Durst - project - GoogleApiConfig.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-07
 * Time: 12:58
 */

namespace Pyz\Zed\GoogleApi;


use Pyz\Shared\GoogleApi\GoogleApiConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class GoogleApiConfig extends AbstractBundleConfig
{
    public const DEFAULT_PROJECT_TIME_ZONE = 'Europe/Berlin';

    /**
     * @return string
     */
    public function getGoogleApiGeocodingKey(): string
    {
        return $this
            ->get(GoogleApiConstants::GOOGLE_API_GEOCODING_KEY);
    }

    /**
     * @return string
     */
    public function getProjectTimeZone(): string
    {
        return $this
            ->get(
                ApplicationConstants::PROJECT_TIMEZONE,
                static::DEFAULT_PROJECT_TIME_ZONE
            );
    }
}
