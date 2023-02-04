<?php
/**
 * Durst - project - TourStep.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.03.19
 * Time: 13:08
 */

namespace Pyz\Zed\DataImport\Business\Model\Tour;


use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlot;
use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlotQuery;
use Orm\Zed\Tour\Persistence\DstAbstractTour;
use Orm\Zed\Tour\Persistence\DstAbstractTourQuery;
use Orm\Zed\Tour\Persistence\DstAbstractTourToAbstractTimeSlotQuery;
use Orm\Zed\Tour\Persistence\Map\DstAbstractTourTableMap;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class TourStep implements DataImportStepInterface
{
    protected const COL_NAME = 'name';
    protected const COL_FK_BRANCH = 'fk_branch';
    protected const COL_TIME_SLOTS = 'time_slots';
    protected const COL_STATUS = 'status';
    protected const COL_WEEKDAY = 'weekday';

    protected const TIME_SLOT_DELIMITER = ',';

    protected const WEEKDAY_MAP = [
        'monday' => DstAbstractTourTableMap::COL_WEEKDAY_MONDAY,
        'tuesday' => DstAbstractTourTableMap::COL_WEEKDAY_TUESDAY,
        'wednesday' => DstAbstractTourTableMap::COL_WEEKDAY_WEDNESDAY,
        'thursday' => DstAbstractTourTableMap::COL_WEEKDAY_THURSDAY,
        'friday' => DstAbstractTourTableMap::COL_WEEKDAY_FRIDAY,
        'saturday' => DstAbstractTourTableMap::COL_WEEKDAY_SATURDAY,
    ];

    protected const STATUS_MAP = [
        'active' => DstAbstractTourTableMap::COL_STATUS_ACTIVE,
        'deactivated' => DstAbstractTourTableMap::COL_STATUS_DEACTIVATED,
        'deleted' => DstAbstractTourTableMap::COL_STATUS_DELETED,
        'planned' => DstAbstractTourTableMap::COL_STATUS_PLANNED,
    ];

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet)
    {
        $abstractTourEntity = DstAbstractTourQuery::create()
            ->filterByFkBranch($dataSet[static::COL_FK_BRANCH])
            ->filterByName($dataSet[static::COL_NAME])
            ->findOneOrCreate();

        $abstractTourEntity->fromArray($dataSet->getArrayCopy());

        $abstractTourEntity->setStatus(
            static::STATUS_MAP[$dataSet[static::COL_STATUS]]
        );

        $abstractTourEntity->setWeekday(
            static::WEEKDAY_MAP[$dataSet[static::COL_WEEKDAY]]
        );

        if($abstractTourEntity->isModified() || $abstractTourEntity->isNew()){
            $abstractTourEntity->save();
        }

        $this->addTimeSlots($abstractTourEntity, $dataSet);
    }

    /**
     * @param \Orm\Zed\Tour\Persistence\DstAbstractTour $abstractTour
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     */
    protected function addTimeSlots(DstAbstractTour $abstractTour, DataSetInterface $dataSet): void
    {
        if($dataSet[static::COL_TIME_SLOTS] === null) {
            return;
        }

        foreach ($this->getTimeSlotIdArrayFromString($dataSet[static::COL_TIME_SLOTS]) as $idTimeSlot) {
            $timeSlotEntity = $this
                ->getTimeSlotAbstractById($idTimeSlot);

            $tourToTimeSlotEntity = DstAbstractTourToAbstractTimeSlotQuery::create()
                ->filterByDstAbstractTour($abstractTour)
                ->filterBySpyTimeSlot($timeSlotEntity)
                ->findOneOrCreate();

            if($tourToTimeSlotEntity->isNew() || $tourToTimeSlotEntity->isModified()){
                $tourToTimeSlotEntity->save();
            }
        }
    }

    /**
     * @param int $idTimeSlotAbstract
     * @return \Orm\Zed\DeliveryArea\Persistence\SpyTimeSlot
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getTimeSlotAbstractById(int $idTimeSlotAbstract): SpyTimeSlot
    {
        return SpyTimeSlotQuery::create()
            ->filterByIdTimeSlot($idTimeSlotAbstract)
            ->findOne();
    }

    /**
     * @param string $timeSlotIds
     * @return array
     */
    protected function getTimeSlotIdArrayFromString(string $timeSlotIds) : array
    {
        return explode(static::TIME_SLOT_DELIMITER, $timeSlotIds);
    }
}