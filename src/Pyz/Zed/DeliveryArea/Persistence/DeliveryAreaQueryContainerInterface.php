<?php

namespace Pyz\Zed\DeliveryArea\Persistence;

use DateTime;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery;
use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery;
use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery;

interface DeliveryAreaQueryContainerInterface
{
    /**
     * Returns an unfiltered delivery area query.
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery
     */
    public function queryDeliveryArea();

    /**
     * Returns a delivery area query filtered by its primary key. This query will
     * have one result at max as this column is unique.
     *
     * @param int $id
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery
     */
    public function queryDeliveryAreaById($id);

    /**
     * Returns an unfiltered time slot query.
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery
     */
    public function queryTimeSlot();

    /**
     * Returns a time slot query filtered by its primary key. This query will
     * have one result at max as this column is unique.
     *
     * @param int $id
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery
     */
    public function queryTimeSlotById($id);

    /**
     * Returns a time slot query filtered by the branch id. This will return all
     * time slots of a given branch.
     *
     * @param int $idBranch
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery
     */
    public function queryTimeSlotByIdBranch($idBranch);

    /**
     * Returns a time slot query filtered by the branch id and a weekday.
     * This will return all time slots of a given branch valid on the given weeksday.
     *
     * @param int $idBranch
     * @param string $weekday
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery
     */
    public function queryActiveTimeSlotByIdBranchAndWeekday(int $idBranch, string $weekday): SpyTimeSlotQuery;

    /**
     * Returns a time slot query filtered by the branch id, a weekday and a abstractTour id.
     * This will return all time slots of a given branch valid on the given weeksday.
     *
     * @param int $idBranch
     * @param string $weekday
     * @param int $idAbstractTour
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery
     */
    public function queryActiveTimeSlotByIdBranchWeekdayAndAbstractTour(int $idBranch, string $weekday, int $idAbstractTour): SpyTimeSlotQuery;

    /**
     * Returns a time slot query joined with delivery area and filtered by
     * branch id. This will return all delivery areas of a given branch.
     *
     * @param int $idBranch
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery
     */
    public function queryDeliveryAreaByIdBranch($idBranch);

    /**
     * Returns a time slot query filtered by branch id an delivery area id.
     * This will return all time slots of a given branch for a given delivery area.
     *
     * @param int $idBranch
     * @param int $idDeliveryArea
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery
     */
    public function queryTimeSlotByIdBranchAndIdDeliveryArea($idBranch, $idDeliveryArea);

    /**
     * Returns a delivery area query filtered by zip code and name.
     *
     * @param $zip
     * @param $name
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery
     */
    public function queryDeliveryAreaByZipAndName($zip, $name);

    /**
     * Returns a concrete time slot query based on the given concrete time slot id.
     *
     * @param $idConcreteTimeSlot
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery
     */
    public function queryConcreteTimeSlotById($idConcreteTimeSlot): SpyConcreteTimeSlotQuery;

    /**
     * @param int $idBranch
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery
     */
    public function queryConcreteTimeSlotForBranchByStartAndEnd(int $idBranch, DateTime $start, DateTime $end): SpyConcreteTimeSlotQuery;

    /**
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery
     */
    public function queryConcreteTimeSlot(): SpyConcreteTimeSlotQuery;

    /**
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery
     */
    public function queryTimeSlotsForActiveBranches(): SpyTimeSlotQuery;

    /**
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlotQuery
     */
    public function queryPassedConcreteTimeSlots(): SpyConcreteTimeSlotQuery;

    /**
     * Returns a concrete time slot query based on the given concrete tour id.
     *
     * @param int $idConcreteTour
     * @return SpyConcreteTimeSlotQuery
     */
    public function queryConcreteTimeSlotsByConcreteTourId(int $idConcreteTour): SpyConcreteTimeSlotQuery;

    /**
     * @param int $timeSlotId
     * @return SpyConcreteTimeSlotQuery
     */
    public function queryConcreteTimeSlotsWithoutOrdersByAbstractTimeSlotId(int $timeSlotId);

    /**
     * Returns a delivery area query filtered by:
     * - zip code
     * - branch code
     *
     * @param string $zipCode
     * @param string $branchCode
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery
     */
    public function queryDeliveryAreaByZipAndBranchCode(string $zipCode, string $branchCode): SpyDeliveryAreaQuery;
}
