<?php

namespace Pyz\Zed\MerchantPrice;

use Pyz\Shared\MerchantPrice\MerchantPriceConstants;
use Pyz\Shared\Sales\SalesConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class MerchantPriceConfig extends AbstractBundleConfig
{
    const DEFAULT_COUNTRY_ISO_CODE = 'DEU';
    const COUNT_SOLD_ITEMS = 'COUNT_COLD_ITEMS';

    /**
     * @return string
     */
    public function getDefaultCountryIsoCode() : string
    {
        return $this
            ->get(MerchantPriceConstants::DEFAULT_COUNTRY_ISO_3_CODE, self::DEFAULT_COUNTRY_ISO_CODE);
    }


    /**
     * @return string
     */
    public function getCountSoldItemsPeriod() : string
    {
        return $this
            ->get(MerchantPriceConstants::COUNT_SOLD_ITEMS, self::COUNT_SOLD_ITEMS);
    }
}
