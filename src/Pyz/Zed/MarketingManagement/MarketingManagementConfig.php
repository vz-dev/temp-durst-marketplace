<?php

namespace Pyz\Zed\MarketingManagement;

use Pyz\Shared\AppRestApi\AppRestApiConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class MarketingManagementConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getMediaHostUrl(): string
    {
        return $this
            ->get(
                AppRestApiConstants::MEDIA_SERVER_HOST
            );
    }
}
