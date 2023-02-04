<?php

namespace Pyz\Zed\DataImport\Business\Model\MerchantUser;

use Orm\Zed\Merchant\Persistence\DstBranchUserQuery;
use Orm\Zed\Merchant\Persistence\DstMerchantUserQuery;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class MerchantUserStep implements DataImportStepInterface
{
    public const COL_EMAIL = 'email';

    /**
     * @param DataSetInterface $dataSet
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $merchantUserEntity = DstMerchantUserQuery::create()
            ->filterByEmail($dataSet[self::COL_EMAIL])
            ->findOneOrCreate();

        $merchantUserEntity->fromArray($dataSet->getArrayCopy());

        if ($merchantUserEntity->isNew() || $merchantUserEntity->isModified()) {
            $merchantUserEntity->save();
        }
    }
}
