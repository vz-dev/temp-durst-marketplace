<?php

namespace Pyz\Zed\DepositPickup\Persistence;

use Orm\Zed\DepositPickup\Persistence\DstDepositPickupInquiryQuery;
use Pyz\Zed\DepositPickup\DepositPickupConfig;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method DepositPickupConfig getConfig()
 * @method DepositPickupQueryContainer getQueryContainer()
 */
class DepositPickupPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return DstDepositPickupInquiryQuery
     */
    public function createDepositPickupInquiryQuery(): DstDepositPickupInquiryQuery
    {
        return DstDepositPickupInquiryQuery::create();
    }
}
