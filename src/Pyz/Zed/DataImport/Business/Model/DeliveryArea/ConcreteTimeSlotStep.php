<?php
/**
 * Durst - project - ConcreteTimeSlotStep.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-06-05
 * Time: 11:04
 */

namespace Pyz\Zed\DataImport\Business\Model\DeliveryArea;

use DateTime;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class ConcreteTimeSlotStep implements DataImportStepInterface
{
    protected const DATE_TIME_FORMAT = 'd.m.Y H:i';

    protected const COL_START_TIME = 'start_time';
    protected const COL_END_TIME = 'end_time';


    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet)
    {
        $entity = new SpyConcreteTimeSlot();
        $entity->fromArray($dataSet->getArrayCopy());

        $entity->setStartTime(
            $this->getDateTimeFromString(
                $dataSet[self::COL_START_TIME]
            )
        );

        $entity->setEndTime(
            $this->getDateTimeFromString(
                $dataSet[self::COL_END_TIME]
            )
        );

        if($entity->isNew() || $entity->isModified()) {
            $entity->save();
        }
    }

    /**
     * @param string $dateTimeString
     *
     * @return \DateTime
     */
    protected function getDateTimeFromString(string $dateTimeString): DateTime
    {
        return DateTime::createFromFormat(
            self::DATE_TIME_FORMAT,
            $dateTimeString
        );
    }
}
