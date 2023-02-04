<?php

namespace Pyz\Zed\Sales\Business\Model\Order;

use Generated\Shared\Transfer\OrderTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;
use Spryker\Zed\Sales\Business\Model\Order\OrderHydratorInterface as SprykerOrderHydratorInterface;

interface OrderHydratorInterface extends SprykerOrderHydratorInterface
{
    /**
     * @param array $orderReferences
     *
     * @return OrderTransfer[]|array
     *
     * @throws PropelException
     */
    public function hydrateMultipleOrderTransfersWithTotalsByOrderReferences(array $orderReferences): array;

    /**
     * @param string $orderReference
     *
     * @return OrderTransfer
     *
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function hydrateOrderTransferFromPersistenceByOrderReference(string $orderReference): OrderTransfer;
}
