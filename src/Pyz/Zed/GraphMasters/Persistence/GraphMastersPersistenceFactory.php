<?php

namespace Pyz\Zed\GraphMasters\Persistence;

use Orm\Zed\GraphMasters\Persistence\DstGraphmastersCommissioningTimeQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersDeliveryAreaCategoryQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersOpeningTimeQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersOrder;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersOrderQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersSettingsQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTimeSlotQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTourQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Pyz\Zed\GraphMasters\GraphMastersConfig getConfig()
 * @method \Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainer getQueryContainer()
 */
class GraphMastersPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return DstGraphmastersSettingsQuery
     */
    public function createGraphmastersSettingsQuery() : DstGraphmastersSettingsQuery
    {
        return DstGraphmastersSettingsQuery::create();
    }

    /**
     * @return DstGraphmastersDeliveryAreaCategoryQuery
     */
    public function createGraphmasterDeliveryAreaCategoryQuery() : DstGraphmastersDeliveryAreaCategoryQuery
    {
        return DstGraphmastersDeliveryAreaCategoryQuery::create();
    }

    /**
     * @return DstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery
     */
    public function createGraphmasterCategoryToDeliveryAreaQuery() : DstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery
    {
        return DstGraphmastersDeliveryAreaCategoryToDeliveryAreaQuery::create();
    }

    /**
     * @return DstGraphmastersTimeSlotQuery
     */
    public function createGraphmastersTimeSlotQuery() : DstGraphmastersTimeSlotQuery
    {
        return DstGraphmastersTimeSlotQuery::create();
    }

    /**
     * @return DstGraphmastersOpeningTimeQuery
     */
    public function createGraphmastersOpeningTimeQuery(): DstGraphmastersOpeningTimeQuery
    {
        return DstGraphmastersOpeningTimeQuery::create();
    }

    /**
     * @return DstGraphmastersCommissioningTimeQuery
     */
    public function createGraphmastersCommissioningTimeQuery(): DstGraphmastersCommissioningTimeQuery
    {
        return DstGraphmastersCommissioningTimeQuery::create();
    }

    /**
     * @return DstGraphmastersTourQuery
     */
    public function createGraphmastersTourQuery(): DstGraphmastersTourQuery
    {
        return DstGraphmastersTourQuery::create();
    }

    /**
     * @return DstGraphmastersOrderQuery
     */
    public function createGraphmastersOrderQuery(): DstGraphmastersOrderQuery
    {
        return DstGraphmastersOrderQuery::create();
    }
}
