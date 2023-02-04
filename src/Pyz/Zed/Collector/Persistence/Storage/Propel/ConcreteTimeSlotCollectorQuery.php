<?php
/**
 * Durst - project - ConcreteTimeSlotCollectorQuery.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 16.10.18
 * Time: 22:24
 */

namespace Pyz\Zed\Collector\Persistence\Storage\Propel;


use Orm\Zed\DeliveryArea\Persistence\Map\SpyConcreteTimeSlotTableMap;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyDeliveryAreaTableMap;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyTimeSlotTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\Touch\Persistence\Map\SpyTouchTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Collector\Business\Storage\ConcreteTimeSlotCollector;
use Spryker\Zed\Collector\Persistence\Collector\AbstractPropelCollectorQuery;

class ConcreteTimeSlotCollectorQuery extends AbstractPropelCollectorQuery
{


    /**
     * @return void
     */
    protected function prepareQuery()
    {
        $this->touchQuery->addJoin(
            SpyTouchTableMap::COL_ITEM_ID,
            SpyConcreteTimeSlotTableMap::COL_ID_CONCRETE_TIME_SLOT,
            Criteria::INNER_JOIN
        );
        $this->touchQuery->addJoin(
            SpyConcreteTimeSlotTableMap::COL_FK_TIME_SLOT,
            SpyTimeSlotTableMap::COL_ID_TIME_SLOT,
            Criteria::INNER_JOIN
        );
        $this->touchQuery->addJoin(
            SpyTimeSlotTableMap::COL_FK_BRANCH,
            SpyBranchTableMap::COL_ID_BRANCH,
            Criteria::INNER_JOIN
        );
        $this->touchQuery->addJoin(
            SpyTimeSlotTableMap::COL_FK_DELIVERY_AREA,
            SpyDeliveryAreaTableMap::COL_ID_DELIVERY_AREA,
            Criteria::INNER_JOIN
        );

        $this
            ->touchQuery
            ->withColumn(SpyConcreteTimeSlotTableMap::COL_START_TIME,
                ConcreteTimeSlotCollector::KEY_START_TIME)
            ->withColumn(SpyConcreteTimeSlotTableMap::COL_END_TIME,
                ConcreteTimeSlotCollector::KEY_END_TIME)
            ->withColumn(SpyTimeSlotTableMap::COL_DELIVERY_COSTS,
                ConcreteTimeSlotCollector::KEY_DELIVERY_COSTS)
            ->withColumn(SpyTimeSlotTableMap::COL_MAX_CUSTOMERS,
                ConcreteTimeSlotCollector::KEY_MAX_CUSTOMERS)
            ->withColumn(SpyTimeSlotTableMap::COL_MAX_PRODUCTS,
                ConcreteTimeSlotCollector::KEY_MAX_PRODUCTS)
            ->withColumn(SpyTimeSlotTableMap::COL_MIN_VALUE_FIRST,
                ConcreteTimeSlotCollector::KEY_MIN_VALUE_FIRST)
            ->withColumn(SpyTimeSlotTableMap::COL_MIN_VALUE_FOLLOWING,
                ConcreteTimeSlotCollector::KEY_MIN_VALUE_FOLLOWING)
            ->withColumn(SpyTimeSlotTableMap::COL_MIN_UNITS,
                ConcreteTimeSlotCollector::KEY_MIN_UNITS)
            ->withColumn(SpyTimeSlotTableMap::COL_IS_ACTIVE,
                ConcreteTimeSlotCollector::KEY_IS_ACTIVE)
            ->withColumn(SpyDeliveryAreaTableMap::COL_ZIP_CODE,
                ConcreteTimeSlotCollector::KEY_ZIP_CODE)
            ->withColumn(SpyConcreteTimeSlotTableMap::COL_ID_CONCRETE_TIME_SLOT,
                ConcreteTimeSlotCollector::KEY_ID_CONCRETE_TIME_SLOT)
            ->withColumn(SpyBranchTableMap::COL_ID_BRANCH,
                ConcreteTimeSlotCollector::KEY_ID_BRANCH);
    }
}