<?php
/**
 * Durst - project - DiscountManager.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 25.03.21
 * Time: 11:12
 */

namespace Pyz\Zed\Oms\Business\Model\Order;


use Generated\Shared\Transfer\CalculatedDiscountTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Pyz\Zed\Oms\Business\Exception\DiscountExcelOriginalException;
use Pyz\Zed\Oms\Business\Exception\DiscountNegativeException;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;

class DiscountManager implements DiscountManagerInterface
{
    /**
     * @var \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var \Pyz\Zed\Discount\Business\DiscountFacadeInterface
     */
    protected $discountFacade;

    /**
     * DiscountManager constructor.
     * @param \Pyz\Zed\Sales\Business\SalesFacadeInterface $salesFacade
     * @param \Pyz\Zed\Discount\Business\DiscountFacadeInterface $discountFacade
     */
    public function __construct(
        SalesFacadeInterface $salesFacade,
        DiscountFacadeInterface $discountFacade
    )
    {
        $this->salesFacade = $salesFacade;
        $this->discountFacade = $discountFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idSalesDiscount
     * @param int $newAmount
     * @return void
     * @throws \Pyz\Zed\Oms\Business\Exception\DiscountExcelOriginalException
     * @throws \Pyz\Zed\Oms\Business\Exception\DiscountNegativeException
     */
    public function setNewOrderDiscountAmount(
        OrderTransfer $orderTransfer,
        int $idSalesDiscount,
        int $newAmount
    ): void
    {
        if ($newAmount < 0) {
            throw new DiscountNegativeException(
                DiscountNegativeException::MESSAGE
            );
        }

        $orderTransfer = $this
            ->changeDiscountAmount(
                $orderTransfer,
                $idSalesDiscount,
                $newAmount
            );

        $this
            ->salesFacade
            ->updateOrder(
                $orderTransfer,
                $orderTransfer
                    ->getIdSalesOrder()
            );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idSalesDiscount
     * @param int $newAmount
     * @return \Generated\Shared\Transfer\OrderTransfer
     * @throws \Pyz\Zed\Oms\Business\Exception\DiscountExcelOriginalException
     */
    protected function changeDiscountAmount(
        OrderTransfer $orderTransfer,
        int $idSalesDiscount,
        int $newAmount
    ): OrderTransfer
    {
        $expenseTransfer = $this
            ->getExpenseTransfer(
                $orderTransfer,
                $idSalesDiscount
            );

        if ($expenseTransfer !== null) {
            $calculatedDiscount = $this
                ->getCalculatedDiscount(
                    $expenseTransfer,
                    $idSalesDiscount
                );

            if ($calculatedDiscount !== null) {
                $quantity = $calculatedDiscount
                    ->getQuantity();

                if ($calculatedDiscount->getUnitAmount() < $newAmount) {
                    throw new DiscountExcelOriginalException(
                        DiscountExcelOriginalException::MESSAGE
                    );
                }

                $calculatedDiscount
                    ->setUnitAmount($newAmount)
                    ->setSumAmount($quantity * $newAmount);

                $expenseTransfer
                    ->setUnitPriceToPayAggregation($newAmount * -1)
                    ->setSumPriceToPayAggregation($newAmount * -$quantity);

                $this
                    ->updateSalesDiscount(
                        $calculatedDiscount
                    );
            }
        }

        return $orderTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idSalesDiscount
     * @return \Generated\Shared\Transfer\ExpenseTransfer|null
     */
    protected function getExpenseTransfer(
        OrderTransfer $orderTransfer,
        int $idSalesDiscount
    ): ?ExpenseTransfer
    {
        foreach ($orderTransfer->getExpenses() as $expenseTransfer) {
            if ($expenseTransfer->getCalculatedDiscounts()->count() < 1) {
                continue;
            }

            $calculatedDiscount = $this
                ->getCalculatedDiscount(
                    $expenseTransfer,
                    $idSalesDiscount
                );

            if ($calculatedDiscount !== null) {
                return $expenseTransfer;
            }
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @param int $idSalesDiscount
     * @return \Generated\Shared\Transfer\CalculatedDiscountTransfer|null
     */
    protected function getCalculatedDiscount(
        ExpenseTransfer $expenseTransfer,
        int $idSalesDiscount
    ): ?CalculatedDiscountTransfer
    {
        foreach ($expenseTransfer->getCalculatedDiscounts() as $calculatedDiscount) {
            if ($calculatedDiscount->getIdDiscount() !== $idSalesDiscount) {
                continue;
            }

            return $calculatedDiscount;
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\CalculatedDiscountTransfer $calculatedDiscountTransfer
     * @return void
     */
    protected function updateSalesDiscount(CalculatedDiscountTransfer $calculatedDiscountTransfer): void
    {
        $transfer = $this
            ->discountFacade
            ->getSalesDiscountById($calculatedDiscountTransfer->getIdDiscount());

        $transfer
            ->setAmount(
                $calculatedDiscountTransfer
                    ->getUnitAmount()
            );

        $this
            ->discountFacade
            ->updateSalesDiscount(
                $transfer
            );
    }
}
