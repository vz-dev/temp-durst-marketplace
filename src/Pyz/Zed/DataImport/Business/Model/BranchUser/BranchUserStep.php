<?php

namespace Pyz\Zed\DataImport\Business\Model\BranchUser;

use Orm\Zed\Merchant\Persistence\DstBranchUserQuery;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class BranchUserStep implements DataImportStepInterface
{
    public const COL_EMAIL = 'email';

    /**
     * @param DataSetInterface $dataSet
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $branchUserEntity = DstBranchUserQuery::create()
            ->filterByEmail($dataSet[self::COL_EMAIL])
            ->findOneOrCreate();

        $branchUserEntity->fromArray($dataSet->getArrayCopy());

        if ($branchUserEntity->isNew() || $branchUserEntity->isModified()) {
            $branchUserEntity->save();
        }
    }
}
