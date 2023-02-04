<?php

namespace Pyz\Zed\Sales\Business\Model\Item;

use Propel\Runtime\Exception\PropelException;

interface ItemUpdaterInterface
{
    /**
     * @param array $idSalesOrderItems
     * @param bool $value
     */
    public function setOrderItemsStuck(array $idSalesOrderItems, bool $value): void;

    /**
     * @param int $idSalesOrderItem
     * @param string $value
     * @throws PropelException
     */
    public function setOrderItemDeliveryStatus(int $idSalesOrderItem, string $value): void;
}
