<?php
/**
 * Durst - project - RefundManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 22:20
 */

namespace Pyz\Zed\Oms\Business\Model\Order;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Oms\Business\Exception\RefundNegativeQuantityException;
use Pyz\Zed\Oms\Business\Exception\RefundQuantityGreaterThanOrderQuantityException;
use Pyz\Zed\Product\Business\ProductFacadeInterface;
use Pyz\Zed\Refund\Business\RefundFacadeInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;

class RefundManager
{
    public const REFUND_COMMENT_STRING_FORMAT = '%d x %s';

    /**
     * @var \Pyz\Zed\Refund\Business\RefundFacadeInterface
     */
    protected $refundFacade;

    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var \Pyz\Zed\Product\Business\ProductFacadeInterface
     */
    protected $productFacade;

    /**
     * RefundManager constructor.
     *
     * @param \Pyz\Zed\Sales\Business\SalesFacadeInterface $salesFacade
     * @param \Pyz\Zed\Refund\Business\RefundFacadeInterface $refundFacade
     * @param \Pyz\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     * @param \Pyz\Zed\Product\Business\ProductFacadeInterface $productFacade
     */
    public function __construct(SalesFacadeInterface $salesFacade, RefundFacadeInterface $refundFacade, MerchantFacadeInterface $merchantFacade, ProductFacadeInterface $productFacade)
    {
        $this->refundFacade = $refundFacade;
        $this->merchantFacade = $merchantFacade;
        $this->salesFacade = $salesFacade;
        $this->productFacade = $productFacade;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @param array $refundQuantities
     *
     * @throws \Pyz\Zed\Oms\Business\Exception\RefundQuantityGreaterThanOrderQuantityException
     * @throws \Pyz\Zed\Oms\Business\Exception\RefundNegativeQuantityException
     *
     * @return void
     */
    public function addRefundsToOrder(SpySalesOrder $order, BranchTransfer $branchTransfer, array $refundQuantities)
    {
        foreach ($refundQuantities as $refundQtyItemId => $refundQty) {
            foreach ($order->getItems() as $item) {
                if ($item->getIdSalesOrderItem() == $refundQtyItemId) {
                    if ($refundQty > $item->getQuantity()) {
                        throw new RefundQuantityGreaterThanOrderQuantityException(
                            print RefundQuantityGreaterThanOrderQuantityException::MESSAGE
                        );
                    }
                    if ($refundQty < 0) {
                        throw new RefundNegativeQuantityException(
                            print RefundNegativeQuantityException::MESSAGE
                        );
                    }

                    $refundTransfer = $this->refundFacade->calculateRefund([$item], $order);
                    $refundTransfer->setComment(
                        $this->getProductNameFromProductSku($item->getSku())
                    );
                    $refundTransfer->setQuantity($refundQty);
                    $refundTransfer->setAmount($this->getRefundAmountForQuantity($item, $item->getRefundableAmount(), $refundQty));
                    $refundTransfer->setSku($item->getSku());
                    $refundTransfer->setMerchantSku($item->getMerchantSku());

                    foreach ($refundTransfer->getItems() as $refundItem) {
                        $refundItem->setCanceledAmount($this->getRefundAmountForQuantity($item, $item->getRefundableAmount(), $refundQty));
                    }
                    $this->refundFacade->saveRefund($refundTransfer);

                    $this->addDepositExpenseRefund($order, $item, $refundQty);
                }
            }
        }
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $item
     * @param int $quantity
     *
     * @return void
     */
    protected function addDepositExpenseRefund(SpySalesOrder $order, SpySalesOrderItem $item, int $quantity)
    {
        foreach ($order->getExpenses() as $expense) {
            $depositType = sprintf('deposit-%s', $item->getSku());
            if ($expense->getPrice() > 0 && $expense->getType() == $depositType) {
                $expenseTransfer = $this->getSalesExpenseTransfer($order, $expense->getIdSalesExpense());

                if ($expenseTransfer !== null) {
                    $expenseTransfer->setCanceledAmount($expenseTransfer->getRefundableAmount() * $quantity);
                    $refundTransfer = $this->createRefundTransfer();
                    $refundTransfer->setFkSalesOrder($order->getIdSalesOrder());
                    $refundTransfer->setComment(sprintf(static::REFUND_COMMENT_STRING_FORMAT, $quantity, $expense->getName()));
                    $refundTransfer->setAmount($expenseTransfer->getCanceledAmount());
                    $refundTransfer->setQuantity($quantity);
                    $refundTransfer->addExpense($expenseTransfer);
                    $this->refundFacade->saveRefund($refundTransfer);
                }
            }
        }
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $item
     * @param int $refundTotal
     * @param int $quantity
     *
     * @return int
     */
    protected function getRefundAmountForQuantity(SpySalesOrderItem $item, int $refundTotal, int $quantity): int
    {
        return ($refundTotal / $item->getQuantity()) * $quantity;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrder
     * @param int $expenseId
     *
     * @return \Generated\Shared\Transfer\ExpenseTransfer|null
     */
    protected function getSalesExpenseTransfer(SpySalesOrder $salesOrder, int $expenseId): ?ExpenseTransfer
    {
        $orderTransfer = $this->salesFacade->getOrderByIdSalesOrder($salesOrder->getIdSalesOrder());

        foreach ($orderTransfer->getExpenses() as $expense) {
            if ($expense->getIdSalesExpense() === $expenseId) {
                return $expense;
            }
        }

        return null;
    }

    /**
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    protected function createRefundTransfer(): RefundTransfer
    {
        return new RefundTransfer();
    }

    /**
     * @param string $sku
     *
     * @return string
     */
    protected function getProductNameFromProductSku(string $sku): string
    {
        $product = $this->productFacade->getProductConcrete($sku);
        $productAttrs = $product->getAttributes();

        return $productAttrs['name'];
    }
}
