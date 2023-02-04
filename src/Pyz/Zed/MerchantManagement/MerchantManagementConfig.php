<?php

namespace Pyz\Zed\MerchantManagement;

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

/**
 * Class MerchantManagementConfig
 * @package Pyz\Zed\MerchantManagement
 */
class MerchantManagementConfig extends AbstractBundleConfig
{
    ### Parameters
    const PARAM_ID_DEPOSIT = 'id-deposit';
    const PARAM_ID_DELIVERY_AREA = 'id-delivery-area';
    ###

    ### URLs
    //Deposit
    const UPDATE_DEPOSIT_URL = '/merchant-management/deposit/update';
    const DELETE_DEPOSIT_URL = '/merchant-management/deposit/delete';
    const DEPOSIT_LISTING_URL = '/merchant-management/deposit';

    //Delivery areas
    const UPDATE_DELIVERY_AREA_URL = '/merchant-management/delivery-area/update';
    const DELETE_DELIVERY_AREA_URL = '/merchant-management/delivery-area/delete';
    const DELIVERY_AREA_LISTING_URL = '/merchant-management/delivery-area';
    ###

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
