<?php
/**
 * Durst - project - SalesOrderItemDeflater.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-06-11
 * Time: 11:09
 */

namespace Pyz\Zed\Sales\Business\Model\OrderDeflater\Deflaters;


use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;

class SalesOrderItemDeflater implements SalesOrderItemDeflaterInterface
{
    /**
     * @var array
     */
    protected $newOrderItems = [];

    /**
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateSalesOrderItems(OrderTransfer $orderTransfer) : OrderTransfer
    {
        $this->newOrderItems = [];

        foreach ($orderTransfer->getItems() as $item) {
            $itemTransfer = new ItemTransfer();
            $itemTransfer->fromArray($item->toArray());
            $itemTransfer->setQuantity($this->getCurrentQuantity($item->getSku()) + 1);
            $this->prepareUnitPrices($itemTransfer);
            $itemTransfer = $this->setSumPrices($itemTransfer);

            if($item->getCalculatedDiscounts()->count() > 0){
                $newDiscounts = [];

                foreach ($itemTransfer->getCalculatedDiscounts() as $discount)
                {
                    $discount->setSumAmount($discount->getUnitAmount() * $itemTransfer->getQuantity());
                    $discount->setQuantity($itemTransfer->getQuantity());
                    $newDiscounts[$discount->getDiscountName()] = $discount;
                }
                $itemTransfer->setCalculatedDiscounts(new ArrayObject($newDiscounts));
            }

            $this->newOrderItems[$item->getSku()] = $itemTransfer;
        }

        return $this->addNewOrderItemsToOrderTransfer($orderTransfer);
    }

    /**
     * @param ItemTransfer $itemTransfer
     * @return ItemTransfer
     */
    protected function setSumPrices(ItemTransfer $itemTransfer): ItemTransfer
    {
        return $itemTransfer
            ->setSumPrice($itemTransfer->getUnitPrice() * $itemTransfer->getQuantity())
            ->setSumPriceToPayAggregation($itemTransfer->getUnitPriceToPayAggregation() * $itemTransfer->getQuantity())
            ->setSumGrossPrice($itemTransfer->getUnitGrossPrice() * $itemTransfer->getQuantity())
            ->setSumNetPrice($itemTransfer->getUnitNetPrice() * $itemTransfer->getQuantity())
            ->setSumDeposit($itemTransfer->getUnitDeposit() * $itemTransfer->getQuantity());
    }

    /**
     * @param ItemTransfer $itemTransfer
     */
    protected function prepareUnitPrices(ItemTransfer $itemTransfer): void
    {
        if($itemTransfer->getUnitPrice() === null){
            $itemTransfer->setUnitPrice(0);
        }
        if($itemTransfer->getUnitPriceToPayAggregation() === null){
            $itemTransfer->setUnitPriceToPayAggregation(0);
        }
        if($itemTransfer->getUnitGrossPrice() === null){
            $itemTransfer->setUnitGrossPrice(0);
        }
        if($itemTransfer->getUnitNetPrice() === null){
            $itemTransfer->setUnitNetPrice(0);
        }
        if($itemTransfer->getUnitDeposit() === null){
            $itemTransfer->setUnitDeposit(0);
        }
    }

    /**
     * @param string $sku
     *
     * @return int
     */
    protected function getCurrentQuantity(string $sku): int
    {
        if (array_key_exists($sku, $this->newOrderItems) === true) {
            return $this->newOrderItems[$sku]->getQuantity();
        }
        return 0;
    }

    /**
     * @param string $sku
     * @return int
     */
    protected function getCurrentSumPriceToPayAggregation(string $sku): int
    {
        if (array_key_exists($sku, $this->newOrderItems) === true) {
            return $this->newOrderItems[$sku]->getSumPriceToPayAggregation();
        }
        return 0;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function addNewOrderItemsToOrderTransfer(OrderTransfer $orderTransfer): OrderTransfer
    {
        $orderTransfer->setItems(new ArrayObject($this->newOrderItems));

        return $orderTransfer;
    }
}
