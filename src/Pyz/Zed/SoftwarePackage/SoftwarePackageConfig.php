<?php
/**
 * Durst - project - SoftwarePackageConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 12:32
 */

namespace Pyz\Zed\SoftwarePackage;


use Pyz\Shared\SoftwarePackage\SoftwarePackageConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class SoftwarePackageConfig extends AbstractBundleConfig
{
    public const DEFAULT_DATE_TIME_FORMAT = 'd.m.y H:i';
    public const DEFAULT_DATE_FORMAT = 'd.m.y';
    public const DEFAULT_DATABASE_DATE_FORMAT = 'Y-m-d';
    public const DEFAULT_PROJECT_TIME_ZONE = 'Europe/Berlin';

    /**
     * @return string
     */
    public function getProjectTimeZone() : string
    {
        return $this
            ->get(ApplicationConstants::PROJECT_TIMEZONE, self::DEFAULT_PROJECT_TIME_ZONE);
    }

    /**
     * @return string
     */
    public function getLicenseKeyKey(): string
    {
        return $this
            ->get(SoftwarePackageConstants::LICENSE_KEY_KEY);
    }

    /**
     * @return string
     */
    public function getLicenseKeyIV(): string
    {
        return $this
            ->get(SoftwarePackageConstants::LICENSE_KEY_VI);
    }

    /**
     * @return string
     */
    public function getLicenseKeyMethod(): string
    {
        return $this
            ->get(SoftwarePackageConstants::LICENSE_KEY_METHOD);
    }
}