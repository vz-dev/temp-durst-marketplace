<?php
/**
 * Durst - project - GraphhopperConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.11.19
 * Time: 12:00
 */

namespace Pyz\Zed\Graphhopper;


use Pyz\Shared\Graphhopper\GraphhopperConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class GraphhopperConfig extends AbstractBundleConfig
{
    public const DEFAULT_PROJECT_TIME_ZONE = 'Europe/Berlin';

    protected const GRAPHHOPPER_LOCALE = 'de';
    protected const GRAPHHOPPER_GEOCODING_RESULT_LIMIT = 1;
    protected const GRAPHHOPPER_GEOCODING_DEFAULT_PROVIDER = 'default';

    /**
     * @return string
     */
    public function getGraphhopperApiKey(): string
    {
        return $this
            ->get(GraphhopperConstants::GRAPHHOPPER_API_KEY);
    }

    /**
     * @return string‚
     */
    public function getGraphhopperLocale(): string
    {
        return $this
            ->get(
                GraphhopperConstants::GRAPHHOPPER_LOCALE,
                self::GRAPHHOPPER_LOCALE
            );
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

    /**
     * @return int
     */
    public function getGeocoderResultLimit(): int
    {
        return $this
            ->get(
                GraphhopperConstants::GRAPHHOPPER_GEOCODING_RESULT_LIMIT,
                static::GRAPHHOPPER_GEOCODING_RESULT_LIMIT
            );
    }

    /**
     * @return string
     */
    public function getGeocoderProvider(): string
    {
        return $this
            ->get(GraphhopperConstants::GRAPHHOPPER_GEOCODING_PROVIDER, static::GRAPHHOPPER_GEOCODING_DEFAULT_PROVIDER);
    }
}
