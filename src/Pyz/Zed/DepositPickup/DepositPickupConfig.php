<?php

namespace Pyz\Zed\DepositPickup;

use Pyz\Shared\Mail\MailConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class DepositPickupConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getMerchantCenterBaseUrl(): string
    {
        return $this->get(MailConstants::MAIL_MERCHANT_CENTER_BASE_URL);
    }

    public function getProjectTimezone(): string
    {
        return $this->get(ApplicationConstants::PROJECT_TIMEZONE);
    }
}
