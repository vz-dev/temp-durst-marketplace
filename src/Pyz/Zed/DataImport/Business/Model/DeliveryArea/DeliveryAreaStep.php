<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 05.02.18
 * Time: 15:44
 */

namespace Pyz\Zed\DataImport\Business\Model\DeliveryArea;


use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class DeliveryAreaStep implements DataImportStepInterface
{
    const COL_ZIP = 'zip';
    const COL_NAME = 'name';

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $deliveryAreaEntity = SpyDeliveryAreaQuery::create()
            ->filterByZipCode($dataSet[self::COL_ZIP])
            ->filterByName($dataSet[self::COL_NAME])
            ->findOneOrCreate();

        $dataSetArray = $dataSet->getArrayCopy();
        $deliveryAreaEntity->fromArray($dataSetArray);
        $deliveryAreaEntity->setZipCode($dataSetArray[self::COL_ZIP]);
        $deliveryAreaEntity->save();
    }
}