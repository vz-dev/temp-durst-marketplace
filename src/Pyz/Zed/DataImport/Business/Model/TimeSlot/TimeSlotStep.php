<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 05.02.18
 * Time: 16:45
 */

namespace Pyz\Zed\DataImport\Business\Model\TimeSlot;


use Orm\Zed\DeliveryArea\Persistence\SpyTimeSlot;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class TimeSlotStep implements DataImportStepInterface
{
    const COL_FK_DELIVER_AREA = 'fk_delivery_area';
    const COL_FK_BRANCH = 'fk_branch';
    const COL_ID_TIME_SLOT = 'id_time_slot';

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $timeSlotEntity = new SpyTimeSlot();

        $timeSlotEntity->fromArray($dataSet->getArrayCopy());
        $timeSlotEntity->save();
    }
}