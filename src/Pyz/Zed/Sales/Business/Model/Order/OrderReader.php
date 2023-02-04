<?php

namespace Pyz\Zed\Sales\Business\Model\Order;

use Generated\Shared\Transfer\OrderTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Business\Model\Order\OrderReader as SprykerOrderReader;

class OrderReader extends SprykerOrderReader implements OrderReaderInterface
{
    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @param string $email
     *
     * @return OrderTransfer|null
     *
     * @throws AmbiguousComparisonException
     */
    public function findOrderWithDurstCustomerReferenceByIdMerchantAndEmail(int $idMerchant, string $email): ?OrderTransfer
    {
        $order = $this
            ->queryContainer
            ->querySalesOrder()
            ->filterByDurstCustomerReference(null, Criteria::ISNOTNULL)
            ->useSpyBranchQuery()
                ->filterByFkMerchant($idMerchant)
            ->endUse()
            ->findOneByEmail($email);

        if ($order === null) {
            return null;
        }

        return $this
            ->orderHydrator
            ->hydrateBaseOrderTransfer($order);
    }
}
