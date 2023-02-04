<?php

namespace Pyz\Zed\DeliveryArea\Persistence;

use DateTime;
use Generated\Shared\Transfer\AbstractTourTransfer;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyTimeSlotTableMap;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery;
use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery;
use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\Tour\Persistence\Base\DstAbstractTour;
use Orm\Zed\Tour\Persistence\Base\DstAbstractTourToAbstractTimeSlot;
use Orm\Zed\Tour\Persistence\Map\DstAbstractTourTableMap;
use Orm\Zed\Tour\Persistence\Map\DstAbstractTourToAbstractTimeSlotTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * @method DeliveryAreaPersistenceFactory getFactory()
 */
class DeliveryAreaQueryContainer extends AbstractQueryContainer implements DeliveryAreaQueryContainerInterface
{
    /**
     * {@inheritdoc}
     *
     * @return SpyDeliveryAreaQuery
     */
    public function queryDeliveryArea()
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $id
     * @return SpyDeliveryAreaQuery
     */
    public function queryDeliveryAreaById($id)
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaQuery()
            ->filterByIdDeliveryArea($id);
    }

    /**
     * {@inheritdoc}
     *
     * @return SpyTimeSlotQuery
     */
    public function queryTimeSlot()
    {
        return $this
            ->getFactory()
            ->createTimeSlotQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $id
     * @return SpyTimeSlotQuery
     */
    public function queryTimeSlotById($id)
    {
        return $this
            ->getFactory()
            ->createTimeSlotQuery()
            ->filterByIdTimeSlot($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return SpyTimeSlotQuery
     */
    public function queryTimeSlotByIdBranch($idBranch)
    {
        return $this
            ->getFactory()
            ->createTimeSlotQuery()
            ->filterByFkBranch($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param string $weekday
     * @return SpyTimeSlotQuery
     * @throws AmbiguousComparisonException
     */
    public function queryActiveTimeSlotByIdBranchAndWeekday(int $idBranch, string $weekday): SpyTimeSlotQuery
    {
        $filterWeekDayMethod = 'filterBy' . ucfirst($weekday);

        return $this
            ->queryTimeSlotByIdBranch($idBranch)
            ->filterByIsActive(true)
            ->filterByStatus(SpyTimeSlotTableMap::COL_STATUS_ACTIVE)
            ->$filterWeekDayMethod(true);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param string $weekday
     * @param int $idAbstractTour
     * @return SpyTimeSlotQuery
     * @throws AmbiguousComparisonException
     */
    public function queryActiveTimeSlotByIdBranchWeekdayAndAbstractTour(int $idBranch, string $weekday, int $idAbstractTour): SpyTimeSlotQuery
    {
        $filterWeekDayMethod = 'filterBy' . ucfirst($weekday);

        return $this
            ->queryTimeSlotByIdBranch($idBranch)
            ->useDstAbstractTourToAbstractTimeSlotQuery()
                ->filterByFkAbstractTour($idAbstractTour)
            ->endUse()
            ->filterByIsActive(true)
            ->filterByStatus(SpyTimeSlotTableMap::COL_STATUS_ACTIVE)
            ->$filterWeekDayMethod(true);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return SpyDeliveryAreaQuery
     */
    public function queryDeliveryAreaByIdBranch($idBranch)
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaQuery()
            ->useSpyTimeSlotQuery()
                ->filterByFkBranch($idBranch)
            ->endUse()
            ->groupByIdDeliveryArea();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param int $idDeliveryArea
     * @return SpyTimeSlotQuery
     */
    public function queryTimeSlotByIdBranchAndIdDeliveryArea($idBranch, $idDeliveryArea)
    {
        return $this
            ->getFactory()
            ->createTimeSlotQuery()
            ->filterByFkBranch($idBranch)
            ->filterByFkDeliveryArea($idDeliveryArea);
    }

    /**
     * {@inheritdoc}
     *
     * @param $zip
     * @param $name
     * @return $this|SpyDeliveryAreaQuery
     */
    public function queryDeliveryAreaByZipAndName($zip, $name)
    {
        return $this
            ->getFactory()
            ->createDeliveryAreaQuery()
            ->filterByName($name)
            ->filterByZipCode($zip);
    }

    /**
     * {@inheritdoc}
     *
     * @param $idConcreteTimeSlot
     * @return $this|mixed|SpyConcreteTimeSlotQuery
     */
    public function queryConcreteTimeSlotById($idConcreteTimeSlot): SpyConcreteTimeSlotQuery
    {

        return $this
            ->getFactory()
            ->createConcreteTimeSlotQuery()
            ->filterByIdConcreteTimeSlot($idConcreteTimeSlot);
    }

    /**
     * {@inheritdoc}
     *
     * @return SpyConcreteTimeSlotQuery
     */
    public function queryConcreteTimeSlot(): SpyConcreteTimeSlotQuery
    {
        return $this
            ->getFactory()
            ->createConcreteTimeSlotQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param DateTime $start
     * @param DateTime $end
     * @return SpyConcreteTimeSlotQuery
     */
    public function queryConcreteTimeSlotForBranchByStartAndEnd(int $idBranch, DateTime $start, DateTime $end): SpyConcreteTimeSlotQuery
    {
        return $this
            ->queryConcreteTimeSlot()
            ->useSpyTimeSlotQuery()
                ->filterByFkBranch($idBranch)
            ->endUse()
            ->filterByStartTime($start)
            ->filterByEndTime($end);
    }

    /**
     * {@inheritdoc}
     *
     * @return SpyTimeSlotQuery
     */
    public function queryTimeSlotsForActiveBranches(): SpyTimeSlotQuery
    {
        return $this
            ->queryTimeSlot()
            ->filterByStatus(SpyTimeSlotTableMap::COL_STATUS_ACTIVE)
            ->joinWithSpyBranch()
            ->useSpyBranchQuery()
                ->filterByStatus(SpyBranchTableMap::COL_STATUS_ACTIVE)
            ->endUse()
            ->filterByIsActive(true);
    }

    /**
     * {@inheritdoc}
     *
     * @return SpyConcreteTimeSlotQuery
     */
    public function queryPassedConcreteTimeSlots(): SpyConcreteTimeSlotQuery
    {
        return $this
            ->queryConcreteTimeSlot()
            ->filterByStartTime(Criteria::CURRENT_TIMESTAMP, Criteria::LESS_THAN);
    }

    /**
     * {@inherit}
     *
     * @param int $idConcreteTour
     * @return SpyConcreteTimeSlotQuery
     * @throws AmbiguousComparisonException
     */
    public function queryConcreteTimeSlotsByConcreteTourId(int $idConcreteTour): SpyConcreteTimeSlotQuery
    {
        return $this
            ->queryConcreteTimeSlot()
            ->filterByFkConcreteTour($idConcreteTour);
    }

    /**
     * @param int $timeSlotId
     * @return SpyConcreteTimeSlotQuery
     * @throws AmbiguousComparisonException
     */
    public function queryConcreteTimeSlotsWithoutOrdersByAbstractTimeSlotId(int $timeSlotId): SpyConcreteTimeSlotQuery
    {
        return $this
            ->queryConcreteTimeSlot()
            ->useSpySalesOrderQuery()
                ->filterByIdSalesOrder(null, Criteria::EQUAL)
            ->endUse()
            ->filterByFkTimeSlot($timeSlotId);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $zipCode
     * @param string $branchCode
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryDeliveryAreaByZipAndBranchCode(string $zipCode, string $branchCode): SpyDeliveryAreaQuery
    {
        return $this
            ->queryDeliveryArea()
            ->filterByZipCode($zipCode)
            ->useSpyTimeSlotQuery()
                ->useSpyBranchQuery()
                    ->filterByCode($branchCode)
                ->endUse()
                ->filterByIsActive(true)
            ->endUse();
    }
}
