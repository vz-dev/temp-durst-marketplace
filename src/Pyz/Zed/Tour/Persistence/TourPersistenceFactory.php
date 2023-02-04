<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 04.09.18
 * Time: 10:35
 */

namespace Pyz\Zed\Tour\Persistence;

use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery;
use Orm\Zed\Oms\Persistence\SpyOmsOrderItemStateQuery;
use Orm\Zed\Oms\Persistence\SpyOmsOrderProcessQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrderItemQuery;
use Orm\Zed\Tour\Persistence\Base\DstVehicleCategory;
use Orm\Zed\Tour\Persistence\DstAbstractTourQuery;
use Orm\Zed\Tour\Persistence\DstAbstractTourToAbstractTimeSlotQuery;
use Orm\Zed\Tour\Persistence\DstConcreteTourExportQuery;
use Orm\Zed\Tour\Persistence\DstConcreteTourQuery;
use Orm\Zed\Tour\Persistence\DstDrivingLicenceQuery;
use Orm\Zed\Tour\Persistence\DstVehicleCategoryQuery;
use Orm\Zed\Tour\Persistence\DstVehicleQuery;
use Orm\Zed\Tour\Persistence\DstVehicleTypeQuery;
use Pyz\Zed\Product\Persistence\ProductQueryContainerInterface;
use Pyz\Zed\Tour\TourDependencyProvider;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Pyz\Zed\Tour\Persistence\TourQueryContainer getQueryContainer()
 * @method \Pyz\Zed\Tour\TourConfig getConfig()
 */
class TourPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\Tour\Persistence\DstVehicleTypeQuery
     */
    public function createVehicleTypeQuery() : DstVehicleTypeQuery
    {
        return DstVehicleTypeQuery::create();
    }

    /**
     * @return \Orm\Zed\Tour\Persistence\DstVehicleQuery
     */
    public function createVehicleQuery() : DstVehicleQuery
    {
        return DstVehicleQuery::create();
    }

    /**
     * @return \Orm\Zed\Tour\Persistence\DstVehicleQuery
     */
    public function createVehicleCategoryQuery() : DstVehicleCategoryQuery
    {
        return DstVehicleCategoryQuery::create();
    }

    /**
     * @return \Orm\Zed\Tour\Persistence\DstDrivingLicenceQuery
     */
    public function createDrivingLicenceQuery() : DstDrivingLicenceQuery
    {
        return DstDrivingLicenceQuery::create();
    }

    /**
     * @return \Orm\Zed\Tour\Persistence\DstAbstractTourQuery
     */
    public function createAbstractTourQuery() : DstAbstractTourQuery
    {
        return DstAbstractTourQuery::create();
    }

    /**
     * @return \Orm\Zed\Tour\Persistence\DstConcreteTourQuery
     */
    public function createConcreteTourQuery() : DstConcreteTourQuery
    {
        return DstConcreteTourQuery::create();
    }

    /**
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery
     */
    public function createConcreteTimeSlotQuery() : SpyConcreteTimeSlotQuery
    {
        return SpyConcreteTimeSlotQuery::create();
    }

    /**
     * @return \Orm\Zed\Tour\Persistence\DstAbstractTourToAbstractTimeSlotQuery
     */
    public function createAbstractTourToAbstractTimeSlotQuery() : DstAbstractTourToAbstractTimeSlotQuery
    {
        return DstAbstractTourToAbstractTimeSlotQuery::create();
    }

    /**
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderItemQuery
     */
    public function createSalesOrderItemQuery() : SpySalesOrderItemQuery
    {
        return SpySalesOrderItemQuery::create();
    }

    /**
     * @return \Orm\Zed\Oms\Persistence\SpyOmsOrderItemStateQuery
     */
    public function createOmsOrderItemStateQuery() : SpyOmsOrderItemStateQuery
    {
        return SpyOmsOrderItemStateQuery::create();
    }

    /**
     * @return \Orm\Zed\Oms\Persistence\SpyOmsOrderProcessQuery
     */
    public function createOmsOrderProcessQuery() : SpyOmsOrderProcessQuery
    {
        return SpyOmsOrderProcessQuery::create();
    }

    /**
     * @return \Orm\Zed\Tour\Persistence\DstConcreteTourExportQuery
     */
    public function createConcreteTourExportQuery(): DstConcreteTourExportQuery
    {
        return DstConcreteTourExportQuery::create();
    }

    /**
     * @return \Pyz\Zed\Product\Persistence\ProductQueryContainerInterface
     */
    public function getProductQueryContainer(): ProductQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                TourDependencyProvider::QUERY_CONTAINER_PRODUCT
            );
    }
}
