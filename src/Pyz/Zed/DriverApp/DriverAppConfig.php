<?php

namespace Pyz\Zed\DriverApp;

use Pyz\Shared\DriverApp\DriverAppConfig as DriverAppDriverAppConfig;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class DriverAppConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getUploadPath(): string
    {
        return $this
            ->get(DriverAppDriverAppConfig::DRIVER_APP_UPLOAD_PATH);
    }
}
