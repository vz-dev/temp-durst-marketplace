<?php
/**
 * Durst - project - SalesRefundsDeflater.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-06-28
 * Time: 14:16
 */

namespace Pyz\Zed\Sales\Business\Model\OrderDeflater\Deflaters;


use ArrayObject;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;

class SalesRefundsDeflater implements SalesRefundsDeflatorInterface
{
    /**
     * @var array
     */
    protected $newOrderRefunds = [];

    /**
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateSalesRefunds(OrderTransfer $orderTransfer) : OrderTransfer
    {
        foreach ($orderTransfer->getRefunds() as $refund) {
            $refundTransfer = new RefundTransfer();
            $refundTransfer->fromArray($refund->toArray());

            $refundTransfer->setQuantity($this->getCurrentQuantity($refund->getSku()) + $refund->getQuantity());
            $refundTransfer->setAmount($this->getCurrentAmount($refund->getSku()) + $refund->getAmount());

            $this->newOrderRefunds[$refund->getSku()] = $refundTransfer;
        }

        $this->addNewOrderRefundsToOrderTransfer($orderTransfer);

        return $orderTransfer;
    }

    /**
     * @param string $sku
     *
     * @return int
     */
    protected function getCurrentQuantity(string $sku): int
    {
        if (array_key_exists($sku, $this->newOrderRefunds)) {
            return $this->newOrderRefunds[$sku]->getQuantity();
        }
        return 0;
    }

    /**
     * @param string $sku
     *
     * @return int
     */
    protected function getCurrentAmount(string $sku): int
    {
        if (array_key_exists($sku, $this->newOrderRefunds)) {
            return $this->newOrderRefunds[$sku]->getAmount();
        }
        return 0;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function addNewOrderRefundsToOrderTransfer(OrderTransfer $orderTransfer): OrderTransfer
    {
        $orderTransfer->setRefunds(new ArrayObject());
        foreach ($this->newOrderRefunds as $refund) {
            $orderTransfer->addRefund($refund);
        }

        return $orderTransfer;
    }
}