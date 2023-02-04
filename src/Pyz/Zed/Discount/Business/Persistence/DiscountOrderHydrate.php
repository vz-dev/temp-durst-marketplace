<?php


namespace Pyz\Zed\Discount\Business\Persistence;

use ArrayObject;
use Generated\Shared\Transfer\CalculatedDiscountTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesDiscount;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Discount\Business\Persistence\DiscountOrderHydrate as SprykerDiscountOrderHydrate;

/**
 * Class DiscountOrderHydrate
 * @package Pyz\Zed\Discount\Business\Persistence
 * @see https://github.com/spryker/discount/blob/master/src/Spryker/Zed/Discount/Business/Persistence/DiscountOrderHydrate.php
 * @see fe192f3 on 13 Nov 2018
 */
class DiscountOrderHydrate extends SprykerDiscountOrderHydrate
{
    /**
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     * @throws PropelException
     */
    public function hydrate(OrderTransfer $orderTransfer): OrderTransfer
    {
        $orderTransfer
            ->requireIdSalesOrder();

        $salesOrderDiscounts = $this
            ->getSalesOrderDiscounts($orderTransfer->getIdSalesOrder());

        $groupedDiscounts = [];

        foreach ($salesOrderDiscounts as $salesOrderDiscountEntity) {
            $calculatedDiscountTransfer = $this
                ->hydrateCalculatedDiscountTransfer($salesOrderDiscountEntity);

            $this
                ->addCalculatedDiscount($orderTransfer, $salesOrderDiscountEntity, $calculatedDiscountTransfer);

            if (isset($groupedDiscounts[$salesOrderDiscountEntity->getDisplayName()])) {
                $existingDiscountTransfer = $groupedDiscounts[$salesOrderDiscountEntity->getDisplayName()];

                $calculatedDiscountTransfer
                    ->setQuantity(
                        $calculatedDiscountTransfer->getQuantity() + $existingDiscountTransfer->getQuantity()
                    );

                $calculatedDiscountTransfer
                    ->setSumAmount(
                        $calculatedDiscountTransfer->getSumAmount() + $existingDiscountTransfer->getSumAmount()
                    );
            }

            $groupedDiscounts[$salesOrderDiscountEntity->getDisplayName()] = $calculatedDiscountTransfer;
        }

        $orderTransfer->setCalculatedDiscounts(new ArrayObject($groupedDiscounts));

        return $orderTransfer;
    }

    /**
     * @param SpySalesDiscount $salesOrderDiscountEntity
     * @return CalculatedDiscountTransfer
     * @throws PropelException
     */
    protected function hydrateCalculatedDiscountTransfer(SpySalesDiscount $salesOrderDiscountEntity): CalculatedDiscountTransfer
    {
        $calculatedDiscountTransfer = new CalculatedDiscountTransfer();

        $calculatedDiscountTransfer
            ->setIdDiscount(
                $salesOrderDiscountEntity->getIdSalesDiscount()
            );
        $calculatedDiscountTransfer
            ->fromArray(
                $salesOrderDiscountEntity->toArray(),
                true
            );
        $calculatedDiscountTransfer
            ->setSumAmount(
                $salesOrderDiscountEntity->getAmount()
            );
        $calculatedDiscountTransfer
            ->setQuantity(
                $this->getCalculatedDiscountQuantity($salesOrderDiscountEntity)
            );

        $this
            ->deriveCalculatedDiscountUnitAmounts($calculatedDiscountTransfer, $salesOrderDiscountEntity);

        foreach ($salesOrderDiscountEntity->getDiscountCodes() as $discountCodeEntity) {
            $calculatedDiscountTransfer->setVoucherCode($discountCodeEntity->getCode());
        }

        return $calculatedDiscountTransfer;
    }

    /**
     * @param SpySalesDiscount $salesOrderDiscountEntity
     * @return int
     */
    protected function getCalculatedDiscountQuantity(SpySalesDiscount $salesOrderDiscountEntity): int
    {
        return 1;
        //  original code from github below, but other (not updated) modules expect the old behavior, means, a quantity of 1
        /** @var SpySalesOrderItem|null $salesOrderItemEntity */
        $salesOrderItemEntity = $salesOrderDiscountEntity
            ->getOrderItem();

        if ($salesOrderItemEntity->getIdSalesOrderItem() === null) {
            return 1;
        }

        return $salesOrderItemEntity
            ->getQuantity();
    }

    /**
     * @param CalculatedDiscountTransfer $calculatedDiscountTransfer
     * @param SpySalesDiscount $salesOrderDiscountEntity
     * @throws PropelException
     */
    protected function deriveCalculatedDiscountUnitAmounts(CalculatedDiscountTransfer $calculatedDiscountTransfer, SpySalesDiscount $salesOrderDiscountEntity): void
    {
        $quantity = $this
            ->getCalculatedDiscountQuantity($salesOrderDiscountEntity);

        $calculatedDiscountTransfer->setUnitAmount((int)round($salesOrderDiscountEntity->getAmount() / $quantity));
    }
}