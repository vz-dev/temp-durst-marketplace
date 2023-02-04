<?php
/**
 * Durst - project - DepositSku.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.03.19
 * Time: 14:41
 */

namespace Pyz\Zed\DataImport\Business\Model\Deposit;

use Orm\Zed\Merchant\Persistence\DstBranchToDepositQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class DepositSkuStep implements DataImportStepInterface
{
    protected const COL_FK_BRANCH = 'fk_branch';
    protected const COL_FK_DEPOSIT = 'fk_deposit';

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet)
    {
        $entity = DstBranchToDepositQuery::create()
            ->filterByFkBranch($dataSet[static::COL_FK_BRANCH])
            ->filterByFkDeposit($dataSet[static::COL_FK_DEPOSIT])
            ->findOneOrCreate();

        $entity->fromArray($dataSet->getArrayCopy());

        if ($entity->isNew() || $entity->isModified()) {
            $entity->save();
        }
    }
}
