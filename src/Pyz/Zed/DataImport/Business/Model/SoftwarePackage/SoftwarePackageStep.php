<?php
/**
 * Durst - project - SoftwarePackageStep.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 12:43
 */

namespace Pyz\Zed\DataImport\Business\Model\SoftwarePackage;


use Orm\Zed\Merchant\Persistence\Base\SpyPaymentMethod;
use Orm\Zed\Merchant\Persistence\SpyPaymentMethodQuery;
use Orm\Zed\Sales\Persistence\DstSoftwarePackage;
use Orm\Zed\Sales\Persistence\DstSoftwarePackageQuery;
use Orm\Zed\Sales\Persistence\DstSoftwarePackageToPaymentMethodQuery;
use Pyz\Zed\DataImport\Business\Exception\PaymentMethodByCodeNotFound;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class SoftwarePackageStep implements DataImportStepInterface
{
    public const COL_NAME = 'name';
    public const COL_CODE = 'code';
    public const COL_QUOTA_DELIVERY_AREA = 'quota_delivery_area';
    public const COL_QUOTA_BRANCH = 'quota_branch';
    public const COL_QUOTA_ORDER = 'quota_order';
    public const COL_QUOTA_PRODUCT_CONCRETE = 'quota_product_concrete';
    public const COL_PAYMENT_METHODS = 'payment_methods';

    public const DELIMITER_PAYMENT_METHODS = ',';

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $entity = DstSoftwarePackageQuery::create()
            ->filterByCode($dataSet[static::COL_CODE])
            ->findOneOrCreate();

        $entity->setName($dataSet[static::COL_NAME]);
        $entity->setQuotaBranch($dataSet[static::COL_QUOTA_BRANCH]);
        $entity->setQuotaDeliveryArea($dataSet[static::COL_QUOTA_DELIVERY_AREA]);
        $entity->setQuotaOrder($dataSet[static::COL_QUOTA_ORDER]);
        $entity->setQuotaProductConcrete($dataSet[static::COL_QUOTA_PRODUCT_CONCRETE]);

        if($entity->isNew() || $entity->isModified()){
            $entity->save();
        }

        $this->addPaymentMethods($dataSet, $entity);
    }

    /**
     * @param DataSetInterface $dataSet
     * @param DstSoftwarePackage $softwarePackageEntity
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function addPaymentMethods(DataSetInterface $dataSet, DstSoftwarePackage $softwarePackageEntity)
    {
        if(!$dataSet[static::COL_PAYMENT_METHODS]){
            return;
        }

        foreach ($this->getPaymentMethodCodeArrayFromString($dataSet[static::COL_PAYMENT_METHODS]) as $paymentMethodCode) {
            /** @noinspection PhpParamsInspection */
            $entity = DstSoftwarePackageToPaymentMethodQuery::create()
                ->filterByDstSoftwarePackage($softwarePackageEntity)
                ->filterBySpyPaymentMethod(
                    $this->getPaymentMethodByCode($paymentMethodCode)
                )
                ->findOneOrCreate();

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