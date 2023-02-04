<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 05.02.18
 * Time: 15:36
 */

namespace Pyz\Zed\DataImport\Business\Model\Branch;


use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Orm\Zed\Merchant\Persistence\SpyBranchQuery;
use Orm\Zed\Merchant\Persistence\SpyBranchToPaymentMethodQuery;
use Orm\Zed\Merchant\Persistence\SpyPaymentMethod;
use Orm\Zed\Merchant\Persistence\SpyPaymentMethodQuery;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\DataImport\Business\Exception\PaymentMethodByCodeNotFound;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class BranchStep implements DataImportStepInterface
{
    protected const COL_NAME = 'name';
    protected const COL_PAYMENT_METHODS = 'payment_methods';

    protected const DELIMITER_PAYMENT_METHODS = ',';

    /**
     * @param DataSetInterface $dataSet
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $branchEntity = SpyBranchQuery::create()
            ->filterByName($dataSet[self::COL_NAME])
            ->findOneOrCreate();

        $branchEntity->fromArray($dataSet->getArrayCopy());
        $branchEntity->setStatus(SpyBranchTableMap::COL_STATUS_ACTIVE);

        if($branchEntity->isNew() || $branchEntity->isModified()){
            $branchEntity->save();
        }

        $this->addPaymentMethods($dataSet, $branchEntity);
    }

    /**
     * @param DataSetInterface $dataSet
     * @param SpyBranch $branchEntity
     * @throws PropelException
     */
    protected function addPaymentMethods(DataSetInterface $dataSet, SpyBranch $branchEntity)
    {
        if(!$dataSet[static::COL_PAYMENT_METHODS]){
            return;
        }

        foreach ($this->getPaymentMethodCodeArrayFromString($dataSet[static::COL_PAYMENT_METHODS]) as $paymentMethodCode) {
            /** @noinspection PhpParamsInspection */
            $entity = SpyBranchToPaymentMethodQuery::create()
                ->filterBySpyBranch($branchEntity)
                ->filterBySpyPaymentMethod(
                    $this->getPaymentMethodByCode($paymentMethodCode)
                )
                ->findOneOrCreate();

            $entity->setB2c(true)
                ->setB2b(true);

            if($entity->isNew() || $entity->isModified()) {
                $entity->save();
            }

        }
    }

    /**
     * @param string $paymentMethodCodes
     * @return array
     */
    protected function getPaymentMethodCodeArrayFromString(string $paymentMethodCodes) : array
    {
        return explode(static::DELIMITER_PAYMENT_METHODS, $paymentMethodCodes);
    }

    /**
     * @param string $code
     * @return SpyPaymentMethod
     */
    protected function getPaymentMethodByCode(string $code) : SpyPaymentMethod
    {
        $entity = SpyPaymentMethodQuery::create()
            ->findOneByCode($code);

        if($entity === null){
            throw new PaymentMethodByCodeNotFound();
        }

        return $entity;
    }
}
