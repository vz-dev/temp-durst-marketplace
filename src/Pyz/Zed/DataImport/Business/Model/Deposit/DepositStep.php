<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 05.02.18
 * Time: 16:29
 */

namespace Pyz\Zed\DataImport\Business\Model\Deposit;

use Orm\Zed\Deposit\Persistence\SpyDepositQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class DepositStep implements DataImportStepInterface
{
    public const COL_NAME = 'name';
    public const COL_CODE = 'code';
    public const COL_WEIGHT = 'weight';

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $depositEntity = SpyDepositQuery::create()
            ->filterByCode($dataSet[self::COL_CODE])
            ->findOneOrCreate();

        $dataSetArray = $dataSet->getArrayCopy();
        $depositEntity->fromArray($dataSetArray);

        if($depositEntity->isModified() || $depositEntity->isNew()){
            $depositEntity->save();
        }
    }
}