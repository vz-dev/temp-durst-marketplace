<?php
/**
 * Durst - project - RefundExpandedItemsManager.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-09-17
 * Time: 12:41
 */

namespace Pyz\Zed\Oms\Business\Model\Order;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Oms\Business\Exception\RefundNegativeQuantityException;
use Pyz\Zed\Oms\Business\Exception\RefundQuantityGreaterThanOrderQuantityException;
use Pyz\Zed\Product\Business\ProductFacadeInterface;
use Pyz\Zed\Refund\Business\RefundFacadeInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Spryker\Zed\Product\Business\Exception\MissingProductException;

class RefundExpandedItemsManager
{
    public const REFUND_COMMENT_STRING_FORMAT = '%d x %s';

    public const RETURN_QUANTITIES_KEY_ID = 'id';
    public const RETURN_QUANTITIES_KEY_QUANTITY = 'quantity';
    public const RETURN_QUANTITIES_KEY_TYPE = 'type';

    /**
     * @var RefundFacadeInterface
     */
    protected $refundFacade;

    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var ProductFacadeInterface
     */
    protected $productFacade;

    /**
     * RefundManager constructor.
     *
     * @param SalesFacadeInterface $salesFacade
     * @param RefundFacadeInterface $refundFacade
     * @param \Pyz\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     * @param ProductFacadeInterface $productFacade
     */
    public function __construct(SalesFacadeInterface $salesFacade, RefundFacadeInterface $refundFacade, MerchantFacadeInterface $merchantFacade, ProductFacadeInterface $productFacade)
    {
        $this->refundFacade = $refundFacade;
        $this->merchantFacade = $merchantFacade;
        $this->salesFacade = $salesFacade;
        $this->productFacade = $productFacade;
    }

    /**
     * @param SpySalesOrder $order
     * @param BranchTransfer $branchTransfer
     * @param array $refundQuantities
     * @throws RefundNegativeQuantityException
     * @throws RefundQuantityGreaterThanOrderQuantityException
     * @throws PropelException
     * @throws MissingProductException
     */
    public function addRefundsToOrder(SpySalesOrder $order, BranchTransfer $branchTransfer, array $refundQuantities)
    {
        $refundQuantities = $this->mapRefundsToItemsById($refundQuantities, $order);
        $addedRefundIds = [];

        foreach ($order->getItems() as $item) {
            if (array_key_exists($item->getIdSalesOrderItem(), $refundQuantities) === true && !in_array($item->getIdSalesOrderItem(), $addedRefundIds) === true) {
                if ($refundQuantities[$item->getIdSalesOrderItem()][self::RETURN_QUANTITIES_KEY_QUANTITY] > $item->getQuantity()) {
                    throw new RefundQuantityGreaterThanOrderQuantityException(
                        RefundQuantityGreaterThanOrderQuantityException::MESSAGE
                    );
                }
                if ($refundQuantities[$item->getIdSalesOrderItem()][self::RETURN_QUANTITIES_KEY_QUANTITY] < 0) {
                    throw new RefundNegativeQuantityException(
                        RefundNegativeQuantityException::MESSAGE
                    );
                }

                $refundTransfer = $this->refundFacade->calculateRefund([$item], $order);
                $refundTransfer->setMerchantSku($item->getMerchantSku());
                $refundTransfer->setComment(
                    $this->getProductNameFromItemOrByProductSku($item->getName(), $item->getSku())
                );
                $refundTransfer->setQuantity($refundQuantities[$item->getIdSalesOrderItem()][self::RETURN_QUANTITIES_KEY_QUANTITY]);
                $refundTransfer->setAmount($this->getRefundAmountForQuantity($item, $item->getRefundableAmount(), $refundQuantities[$item->getIdSalesOrderItem()][self::RETURN_QUANTITIES_KEY_QUANTITY]));
                $refundTransfer->setSku($item->getSku());

                foreach ($refundTransfer->getItems() as $refundItem) {
                    $refundItem->setCanceledAmount($this->getRefundAmountForQuantity($item, $item->getRefundableAmount(), $refundQuantities[$item->getIdSalesOrderItem()][self::RETURN_QUANTITIES_KEY_QUANTITY]));
                }

                $refundTransfer->setFkSalesOrderItem($item->getIdSalesOrderItem());

                $this->refundFacade->saveRefund($refundTransfer);

                $this->addDepositExpenseRefund($order, $item, $refundQuantities[$item->getIdSalesOrderItem()][self::RETURN_QUANTITIES_KEY_QUANTITY], $refundQuantities[$item->getIdSalesOrderItem()][self::RETURN_QUANTITIES_KEY_TYPE]);

                $addedRefundIds[] = $item->getIdSalesOrderItem();
            }
        }
    }

    /**
     * @param SpySalesOrder $order
     * @param SpySalesOrderItem $item
     * @param int $quantity
     * @param string $type
     * @throws PropelException
     */
    protected function addDepositExpenseRefund(SpySalesOrder $order, SpySalesOrderItem $item, int $quantity, string $type)
    {
        $expenses = $this->mapExpenseTypeToExpenses($order->getExpenses());
        $expense = $expenses[$type];

        if ($expense->getPrice() > 0) {
            $expenseTransfer = $this->getSalesExpenseTransfer($expense->getIdSalesExpense());

            if ($expenseTransfer !== null) {
                $expenseTransfer->setCanceledAmount($expenseTransfer->getRefundableAmount());
                $refundTransfer = $this->createRefundTransfer();
                $refundTransfer->setFkSalesOrder($order->getIdSalesOrder());
                $refundTransfer->setSku($this->getSkuFromDepositType($type));
                $refundTransfer->setComment($expense->getName());
                $refundTransfer->setAmount($expenseTransfer->getCanceledAmount());
                $refundTransfer->setQuantity($quantity);
                $refundTransfer->addExpense($expenseTransfer);
                $refundTransfer->setFkSalesExpense($expenseTransfer->getIdSalesExpense());

                $this->refundFacade->saveRefund($refundTransfer);
            }
        }
    }

    /**
     * @param SpySalesOrderItem $item
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
     * @param int $expenseId
     * @return ExpenseTransfer
     */
    protected function getSalesExpenseTransfer(int $expenseId): ExpenseTransfer
    {
        return $this->salesFacade->getSalesExpenseById($expenseId);
    }

    /**
     * @return RefundTransfer
     */
    protected function createRefundTransfer(): RefundTransfer
    {
        return new RefundTransfer();
    }

    /**
     * @param string|null $itemName
     * @param string $sku
     * @return string
     * @throws MissingProductException
     */
    protected function getProductNameFromItemOrByProductSku(?string $itemName, string $sku): string
    {
        if($itemName !== null && $itemName !== '')
        {
            return $itemName;
        }

        $product = $this->productFacade->getProductConcrete($sku);
        $productAttrs = $product->getAttributes();

        return $productAttrs['name'];
    }

    /**
     * @param array $refundQuantities
     * @param SpySalesOrder $order
     * @return array
     * @throws PropelException
     */
    protected function mapRefundsToItemsById(array $refundQuantities, SpySalesOrder $order)
    {
        $orderMerchantSkuToIdsMap = [];

        foreach($order->getItems() as $item)
        {
            if(array_key_exists($item->getIdSalesOrderItem(), $refundQuantities) === true)
            {
                if(!array_key_exists($item->getSku(), $orderMerchantSkuToIdsMap) === true)
                {
                    $orderMerchantSkuToIdsMap[$item->getSku()] = [];
                }

                $orderMerchantSkuToIdsMap[$item->getSku()][] = $item->getIdSalesOrderItem();
            }
        }

        $newRefundQuantities = [];

        foreach ($orderMerchantSkuToIdsMap as $sku => $mapItemIdArray) {
            foreach ($mapItemIdArray as $itemId){
                if (array_key_exists($itemId, $refundQuantities) === true) {
                    $newRefundQuantities[$itemId][self::RETURN_QUANTITIES_KEY_ID] = $itemId;
                    $newRefundQuantities[$itemId][self::RETURN_QUANTITIES_KEY_QUANTITY] = $refundQuantities[$itemId];
                    $newRefundQuantities[$itemId][self::RETURN_QUANTITIES_KEY_TYPE] = 'deposit-' . $sku . '-' . (array_search($itemId, $orderMerchantSkuToIdsMap[$sku]) + 1);
                }
            }
        }

        return $newRefundQuantities;
    }

    /**
     * @param $orderExpenses
     * @return array
     */
    protected function mapExpenseTypeToExpenses($orderExpenses) : array
    {
        $expenseMap = [];
        foreach($orderExpenses as $orderExpense)
        {
            $expenseMap[$orderExpense->getType()] = $orderExpense;
        }

        return $expenseMap;
    }

    /**
     * @param string $depositType
     * @return string
     */
    protected function getSkuFromDepositType(string $depositType) : string
    {
        return substr($depositType, 0, strrpos( $depositType, '-'));
    }
}
