<?php
/**
 * Durst - project - OrderUpdater.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-16
 * Time: 09:52
 */

namespace Pyz\Zed\Sales\Business\Model\Order;

use DateTime;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\GraphhopperTourTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesExpense;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderTotals;
use Orm\Zed\Tax\Persistence\SpySalesOrderTaxRateTotal;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Discount\DiscountConstants;
use Spryker\Zed\Sales\Business\Model\Order\OrderUpdater as SprykerOrderUpdater;

class OrderUpdater extends SprykerOrderUpdater
{
    /**
     * @param OrderTransfer $orderTransfer
     * @param int $idSalesOrder
     *
     * @return bool
     *
     * @throws PropelException
     */
    public function update(OrderTransfer $orderTransfer, $idSalesOrder)
    {
        $orderEntity = $this->queryContainer
            ->querySalesOrderById($idSalesOrder)
            ->findOne();

        return $this->updateEntity($orderTransfer, $orderEntity);
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param string $orderReference
     *
     * @return bool
     *
     * @throws PropelException
     */
    public function updateByOrderReference(OrderTransfer $orderTransfer, string $orderReference): bool
    {
        $orderEntity = $this->queryContainer
            ->querySalesOrder()
            ->findOneByOrderReference($orderReference);

        return $this->updateEntity($orderTransfer, $orderEntity);
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param SpySalesOrder|null $orderEntity
     *
     * @return bool
     *
     * @throws PropelException
     */
    protected function updateEntity(OrderTransfer $orderTransfer, SpySalesOrder $orderEntity = null)
    {
        if (empty($orderEntity)) {
            return false;
        }

        $this->hydrateEntityFromOrderTransfer($orderTransfer, $orderEntity);
        $orderEntity->setSignedAt($this->getSignedAtDateTime($orderTransfer->getSignedAt()));
        $orderEntity->save();

        $this->updateOrderItems($orderTransfer, $orderEntity);
        $this->updateOrderExpenses($orderTransfer, $orderEntity);
        $this->createOrderTotals($orderTransfer, $orderEntity);

        return true;
    }

    /**
     * @param int $idSalesOrder
     * @param string $issuer
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updateCancelIssuer(
        int $idSalesOrder,
        string $issuer
    ): bool
    {
        $orderEntity = $this
            ->queryContainer
            ->querySalesOrderById(
                $idSalesOrder
            )
            ->findOne();

        if ($orderEntity === null) {
            return false;
        }

        $orderEntity
            ->setCancelIssuer(
                $issuer
            );

        $orderEntity
            ->save();

        return true;
    }

    /**
     * @param int $idSalesOrder
     * @param string|null $message
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updateCancelMessage(
        int $idSalesOrder,
        ?string $message = null
    ): bool
    {
        $orderEntity = $this
            ->queryContainer
            ->querySalesOrderById(
                $idSalesOrder
            )
            ->findOne();

        if ($orderEntity === null) {
            return false;
        }

        $orderEntity
            ->setCancelMessage(
                $message
            );

        $orderEntity
            ->save();

        return true;
    }

    /**
     * @param int $idSalesOrder
     * @param int $fkDriver
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updateDriver(
        int $idSalesOrder,
        int $fkDriver
    ): bool
    {
        $orderEntity = $this
            ->queryContainer
            ->querySalesOrderById(
                $idSalesOrder
            )
            ->findOne();

        if ($orderEntity === null) {
            return false;
        }

        $orderEntity
            ->setFkDriver(
                $fkDriver
            );

        $orderEntity
            ->save();

        return true;
    }

    /**
     * @param int $idSalesOrder
     * @param bool $state
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updateHeidelpayCustomerState(
        int $idSalesOrder,
        bool $state
    ): bool
    {
        $orderEntity = $this
            ->queryContainer
            ->querySalesOrderById(
                $idSalesOrder
            )
            ->findOne();

        if ($orderEntity === null) {
            return false;
        }

        $orderEntity
            ->setIsHeidelpayCustomerValid(
                $state
            )
            ->setIsHeidelpayCustomerRequested(
                true
            );

        $orderEntity
            ->save();

        return true;
    }

    /**
     * @param $signedAt
     * @return DateTime|null
     */
    protected function getSignedAtDateTime($signedAt): ?DateTime
    {
        if($signedAt === null){
            return null;
        }
        if(is_numeric($signedAt) && $signedAt > 0){
            return (new DateTime())
                ->setTimestamp($signedAt);
        }
        if($signedAt instanceof DateTime){
            return $signedAt;
        }
        return null;
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param SpySalesOrder $orderEntity
     *
     * @return void
     */
    protected function createOrderTotals(OrderTransfer $orderTransfer, SpySalesOrder $orderEntity)
    {
        $taxTotal = 0;
        if ($orderTransfer->getTotals()->getTaxTotal()) {
            $taxTotal = $orderTransfer->getTotals()->getTaxTotal()->getAmount();
        }

        $salesOrderTotalsEntity = new SpySalesOrderTotals();
        $salesOrderTotalsEntity->setFkSalesOrder($orderEntity->getIdSalesOrder());
        $salesOrderTotalsEntity->fromArray($orderTransfer->getTotals()->toArray());
        $salesOrderTotalsEntity->setTaxTotal($taxTotal);
        $salesOrderTotalsEntity->setCanceledTotal($orderTransfer->getTotals()->getCanceledTotal());
        $salesOrderTotalsEntity->setOrderExpenseTotal($orderTransfer->getTotals()->getExpenseTotal());
        $salesOrderTotalsEntity->save();

        $this->saveTaxRateTotals($orderTransfer, $salesOrderTotalsEntity->getIdSalesOrderTotals());
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param int $idSalesOrderTotals
     *
     * @return void
     */
    protected function saveTaxRateTotals(OrderTransfer $orderTransfer, int $idSalesOrderTotals): void
    {
        foreach ($orderTransfer->getTotals()->getTaxRateTotals() as $taxRateTotalTransfer) {
            $taxRateTotal = new SpySalesOrderTaxRateTotal();
            $taxRateTotal->setFkSalesOrderTotals($idSalesOrderTotals);
            $taxRateTotal->setTaxRate($taxRateTotalTransfer->getRate());
            $taxRateTotal->setTaxTotal($taxRateTotalTransfer->getAmount());
            $taxRateTotal->save();
        }
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param SpySalesOrder $orderEntity
     *
     * @return void
     */
    protected function updateOrderExpenses(OrderTransfer $orderTransfer, SpySalesOrder $orderEntity)
    {
        foreach ($orderEntity->getExpenses() as $expenseEntity) {
            foreach ($orderTransfer->getExpenses() as $expenseTransfer) {
                if ($expenseTransfer->getIdSalesExpense() !== $expenseEntity->getIdSalesExpense()) {
                    continue;
                }

                $expenseEntity->setCanceledAmount($expenseTransfer->getCanceledAmount());
                $expenseEntity->setRefundableAmount($expenseTransfer->getRefundableAmount());

                $expenseEntity->save();
            }
        }

        foreach ($orderTransfer->getExpenses() as $expense) {
            if ($expense->getIdSalesExpense() === null) {
                $expenseEnt = new SpySalesExpense();
                $expenseEnt->fromArray($expense->toArray());
                $expenseEnt->setFkSalesOrder($orderTransfer->getIdSalesOrder());

                $expenseEnt->setType($expense->getType());
                $expenseEnt->setName($expense->getName());
                $expenseEnt->setQuantity($expense->getQuantity());
                $expenseEnt->setTaxRate($expense->getTaxRate());
                if ($this->isDepositReturnExpense($expense)) {
                    $expenseEnt->setPrice($expense->getUnitPrice());
                    $expenseEnt->setTaxAmount($expense->getUnitTaxAmount());
                    $expenseEnt->setGrossPrice($expense->getUnitGrossPrice());
                } else {
                    $expenseEnt->setPrice($expense->getSumPrice());
                    $expenseEnt->setTaxAmount($expense->getSumTaxAmount());
                    $expenseEnt->setGrossPrice($expense->getSumGrossPrice());
                }
                $expenseEnt->setNetPrice($expenseEnt->getGrossPrice() - $expenseEnt->getTaxAmount());
                $expenseEnt->save();

                $orderEntity->addExpense($expenseEnt);
            } else {
                if ($expense->getType() !== DiscountConstants::VOUCHER_CODE_EXPENSE_TYPE) {
                    continue;
                }
                foreach ($orderEntity->getExpenses() as $expenseEntity) {
                    if ($expenseEntity->getIdSalesExpense() !== $expense->getIdSalesExpense()) {
                        continue;
                    }

                    $expenseEntity
                        ->setDiscountAmountAggregation(
                            $expense
                                ->getUnitDiscountAmountAggregation()
                        )
                        ->setPriceToPayAggregation(
                            $expense
                                ->getUnitPriceToPayAggregation()
                        );

                    $expenseEntity
                        ->save();
                }
            }
        }
    }

    /**
     * @param ExpenseTransfer $expenseTransfer
     *
     * @return bool
     */
    protected function isDepositReturnExpense(ExpenseTransfer $expenseTransfer) : bool
    {
        return $expenseTransfer->getMerchantSku() !== null;
    }

    /**
     * @param GraphhopperTourTransfer $graphhopperTourTransfer
     *
     * @return void
     */
    public function updateTourOrdersWithDeliveryOrder(GraphhopperTourTransfer $graphhopperTourTransfer)
    {
        $deliveryOrder = 1;
        foreach ($graphhopperTourTransfer->getStops() as $stop) {
            $orderEntity = $this->queryContainer
                ->querySalesOrderById($stop->getId())
                ->findOne();

            $orderEntity->setDeliveryOrder($deliveryOrder);
            $orderEntity->save();

            $deliveryOrder += 1;
        }
    }

    /**
     * @param int $idSalesOrder
     * @return bool
     * @throws PropelException
     */
    public function incrementSalesOrderRetryCounter(int $idSalesOrder): bool
    {
        $orderEntity = $this
            ->queryContainer
            ->querySalesOrderById(
                $idSalesOrder
            )
            ->findOne();

        if ($orderEntity === null) {
            return false;
        }

        $currentCounter = $orderEntity
            ->getOmsRetryCounter();

        $currentCounter++;

        $orderEntity
            ->setOmsRetryCounter(
                $currentCounter
            );

        $rows = $orderEntity
            ->save();

        return ($rows > 0);
    }

    /**
     * @param int $idSalesOrder
     * @return bool
     * @throws PropelException
     */
    public function resetSalesOrderTryCounter(int $idSalesOrder): bool
    {
        $orderEntity = $this
            ->queryContainer
            ->querySalesOrderById(
                $idSalesOrder
            )
            ->findOne();

        if ($orderEntity === null) {
            return false;
        }

        $orderEntity
            ->setOmsRetryCounter(0);

        $rows = $orderEntity
            ->save();

        return ($rows > 0);
    }

    /**
     * @param int $idSalesOrder
     * @param DateTime|null $confirmationDate
     * @return bool
     * @throws PropelException
     */
    public function updateConfirmationDate(
        int $idSalesOrder,
        ?DateTime $confirmationDate = null
    ): bool
    {
        $orderEntity = $this
            ->queryContainer
            ->querySalesOrderById(
                $idSalesOrder
            )
            ->findOne();

        if ($orderEntity === null) {
            return false;
        }

        $orderEntity
            ->setConfirmedAt(
                $confirmationDate
            );

        $orderEntity
            ->save();

        return true;
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param SpySalesOrder $orderEntity
     *
     * @return void
     */
    protected function updateOrderItems(OrderTransfer $orderTransfer, SpySalesOrder $orderEntity)
    {
        foreach ($orderEntity->getItems() as $salesOrderItemEntity) {
            foreach ($orderTransfer->getItems() as $itemTransfer) {
                if ($salesOrderItemEntity->getIdSalesOrderItem() !== $itemTransfer->getIdSalesOrderItem()) {
                    continue;
                }

                $salesOrderItemEntity->setIntegraPositionDid($itemTransfer->getIntegraPositionDid());
                $salesOrderItemEntity->setCanceledAmount($itemTransfer->getCanceledAmount());
                $salesOrderItemEntity->setRefundableAmount($itemTransfer->getRefundableAmount());

                $salesOrderItemEntity->save();
            }
        }
    }
}
