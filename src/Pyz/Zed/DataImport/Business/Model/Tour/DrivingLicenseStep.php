<?php
/**
 * Durst - project - DrivingLicenceStep.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-20
 * Time: 13:54
 */

namespace Pyz\Zed\DataImport\Business\Model\Tour;

use Orm\Zed\Tour\Persistence\DstDrivingLicenceQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class DrivingLicenseStep implements DataImportStepInterface
{
    protected const COL_CODE = 'code';

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet)
    {
        $entity = DstDrivingLicenceQuery::create()
            ->filterByCode($dataSet[self::COL_CODE])
            ->findOneOrCreate();

        $entity->fromArray($dataSet->getArrayCopy());

        if ($entity->isNew() || $entity->isModified()) {
            $entity->save();
        }
    }
}
