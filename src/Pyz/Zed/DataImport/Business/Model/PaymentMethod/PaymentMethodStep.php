<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 05.02.18
 * Time: 16:36
 */

namespace Pyz\Zed\DataImport\Business\Model\PaymentMethod;

use Orm\Zed\Merchant\Persistence\SpyPaymentMethodQuery;
use Pyz\Shared\Merchant\MerchantConstants;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\TouchAwareStep;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class PaymentMethodStep extends TouchAwareStep implements DataImportStepInterface
{
    const COL_NAME = 'name';
    const COL_CODE = 'code';

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet)
    {
        $paymentMethodEntity = SpyPaymentMethodQuery::create()
            ->filterByCode($dataSet[self::COL_CODE])
            ->findOneOrCreate();

        $paymentMethodEntity->fromArray($dataSet->getArrayCopy());

        if ($paymentMethodEntity->isNew()) {
            $paymentMethodEntity->save();
            $this->addMainTouchable(MerchantConstants::RESOURCE_TYPE_PAYMENT_PROVIDER, $paymentMethodEntity->getIdPaymentMethod());
        }
    }
}
