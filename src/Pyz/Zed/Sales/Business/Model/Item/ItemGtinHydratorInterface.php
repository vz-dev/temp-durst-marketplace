<?php


namespace Pyz\Zed\Sales\Business\Model\Item;


use Generated\Shared\Transfer\OrderTransfer;

interface ItemGtinHydratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrateItemGtin(OrderTransfer $orderTransfer): OrderTransfer;
}