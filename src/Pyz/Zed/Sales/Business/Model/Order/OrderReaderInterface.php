<?php

namespace Pyz\Zed\Sales\Business\Model\Order;

use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Business\Model\Order\OrderReaderInterface as SprykerOrderReaderInterface;

interface OrderReaderInterface extends SprykerOrderReaderInterface
{
    /**
     * Returns the first order which has a Durst customer reference for the given merchant ID and e-mail
     *
     * @param int $idMerchant
     * @param string $email
     *
     * @return OrderTransfer|null
     *
     * @throws AmbiguousComparisonException
     */
    public function findOrderWithDurstCustomerReferenceByIdMerchantAndEmail(int $idMerchant, string $email): ?OrderTransfer;
}
