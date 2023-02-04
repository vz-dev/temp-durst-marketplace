<?php
/**
 * Durst - project - ConcreteTourStep.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-06-05
 * Time: 11:04
 */

namespace Pyz\Zed\DataImport\Business\Model\Tour;

use DateTime;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class ConcreteTourStep implements DataImportStepInterface
{
    protected const DATE_FORMAT = 'd.m.Y';

    protected const COL_DATE = 'date';

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet)
    {
        $entity = new DstConcreteTour();
        $entity->fromArray($dataSet->getArrayCopy());

        $entity->setDate($this->getDateTimeFromString($dataSet[self::COL_DATE]));

        if ($entity->isNew() || $entity->isModified()) {
            $entity->save();
        }
    }

    /**
     * @param string $dateString
     *
     * @return \DateTime
     */
    protected function getDateTimeFromString(string $dateString): DateTime
    {
        return DateTime::createFromFormat(
            self::DATE_FORMAT,
            $dateString
        );
    }
}
