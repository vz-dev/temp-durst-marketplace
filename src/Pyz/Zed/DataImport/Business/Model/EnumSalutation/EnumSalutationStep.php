<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 05.02.18
 * Time: 15:22
 */

namespace Pyz\Zed\DataImport\Business\Model\EnumSalutation;


use Orm\Zed\Merchant\Persistence\SpyEnumSalutationQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class EnumSalutationStep implements DataImportStepInterface
{
    const COL_ENUM_SALUTATION_NAME='name';

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $enumSalutationEntity = SpyEnumSalutationQuery::create()
            ->filterByName($dataSet[self::COL_ENUM_SALUTATION_NAME])
            ->findOneOrCreate();

        $enumSalutationEntity->fromArray($dataSet->getArrayCopy());
        $enumSalutationEntity->save();
    }
}