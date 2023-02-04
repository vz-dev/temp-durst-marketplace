<?php

namespace Pyz\Zed\DeliveryArea\Persistence;

use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery;
use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery;
use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Pyz\Zed\DeliveryArea\DeliveryAreaConfig getConfig()
 * @method \Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainer getQueryContainer()
 */
class DeliveryAreaPersistenceFactory extends AbstractPersistenceFactory
{

    /**
     * @return SpyDeliveryAreaQuery
     */
    public function createDeliveryAreaQuery()
    {
        //TODO use create() instead of instantiating the object manually
        return new SpyDeliveryAreaQuery();
    }

    /**
     * @return SpyTimeSlotQuery
     */
    public function createTimeSlotQuery()
    {
        //TODO use create() instead of instantiating the object manually
        return new SpyTimeSlotQuery();
    }

    public function createConcreteTimeSlotQuery()
    {
        return new SpyConcreteTimeSlotQuery();
    }
}
