<?php
/**
 * Durst - project - RecalculateCancel.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 13.09.21
 * Time: 09:05
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command;

use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Pyz\Shared\Discount\DiscountConstants;
use Pyz\Zed\Calculation\Business\Exception\GrandTotalIsNegativeException;
use Pyz\Zed\CancelOrder\Communication\CancelOrderCommunicationFactory;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class RecalculateCancel
 * @package Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command
 *
 * @method CancelOrderCommunicationFactory getFactory()
 */
class RecalculateCancel extends AbstractCommand implements CommandByOrderInterface
{
    public const EVENT_ID = 'recalculateCancel';
    public const NAME = 'CancelOrder/RecalculateCancel';
    public const STATE_NAME = 'recalculate cancellation';

    /**
     * {@inheritDoc}
     *
     * @param array $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function run(
        array $orderItems,
        SpySalesOrder $orderEntity,
        ReadOnlyArrayObject $data
    ): array
    {
        $this
            ->getFactory()
            ->getOmsQueryContainer()
            ->getConnection()
            ->beginTransaction();

        try {
            if ($this->hasVoucher($orderEntity) === true) {
                $this
                    ->setNewVoucherAmount(
                        $orderEntity
                    );
            }

            $refundItems = [];

            foreach ($orderEntity->getItems() as $item) {
                $refundQuantity = $item
                    ->getQuantity();

                $refundItems[$item->getIdSalesOrderItem()] = $refundQuantity;
            }

            if (!empty($refundItems) === true) {
                $branchTransfer = $this
                    ->getFactory()
                    ->getMerchantFacade()
                    ->getBranchById(
                        $orderEntity
                            ->getFkBranch()
                    );

                $this
                    ->getFactory()
                    ->getOmsFacade()
                    ->addExpandedItemsRefundsToOrder(
                        $orderEntity,
                        $branchTransfer,
                        $refundItems
                    );
            }

        } catch (GrandTotalIsNegativeException $grandTotalIsNegativeException) {
            $this
                ->getFactory()
                ->getOmsQueryContainer()
                ->getConnection()
                ->rollBack();

            throw $grandTotalIsNegativeException;
        }

        $this
            ->getFactory()
            ->getOmsQueryContainer()
            ->getConnection()
            ->commit();

        return [];
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function hasVoucher(
        SpySalesOrder $order
    ): bool
    {
        $expenses = $order
            ->getExpenses();

        foreach ($expenses as $expense) {
            if ($expense->getType() === DiscountConstants::VOUCHER_CODE_EXPENSE_TYPE) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function setNewVoucherAmount(SpySalesOrder $order): void
    {
        $expense = null;

        // find voucher expense
        foreach ($order->getExpenses() as $currentExpense) {
            if ($currentExpense->getType() === DiscountConstants::VOUCHER_CODE_EXPENSE_TYPE) {
                $expense = $currentExpense;
            }
        }

        if ($expense === null) {
            return;
        }

        $discount = null;

        // find voucher discount
        foreach ($order->getDiscounts() as $currentDiscount) {
            if ($currentDiscount->getFkSalesExpense() === $expense->getIdSalesExpense()) {
                $discount = $currentDiscount;
            }
        }

        if ($discount === null) {
            return;
        }

        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder(
                $order
                    ->getIdSalesOrder()
            );

        if ($orderTransfer === null) {
            return;
        }

        $this
            ->getFactory()
            ->getOmsFacade()
            ->setNewOrderDiscountAmount(
                $orderTransfer,
                $discount
                    ->getIdSalesDiscount(),
                0
            );
    }
}
