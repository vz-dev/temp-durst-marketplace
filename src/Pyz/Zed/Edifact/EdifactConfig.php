<?php
/**
 * Durst - project - EdifactConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-22
 * Time: 08:49
 */

namespace Pyz\Zed\Edifact;


use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class EdifactConfig extends AbstractBundleConfig
{
    protected const DEFAULT_PROJECT_TIME_ZONE = 'Europe/Berlin';

    /**
     * @return string
     */
    public function getProjectTimeZone(): string
    {
        return $this
            ->get(
                ApplicationConstants::PROJECT_TIMEZONE
            );
    }
}