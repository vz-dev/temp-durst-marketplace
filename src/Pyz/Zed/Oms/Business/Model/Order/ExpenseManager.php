<?php
/**
 * Durst - project - ExpenseManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 17:15
 */

namespace Pyz\Zed\Oms\Business\Model\Order;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\DepositSkuTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Pyz\Shared\Deposit\DepositConstants;
use Pyz\Shared\Sales\SalesConstants;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Spryker\Zed\Calculation\Business\CalculationFacadeInterface;
use Spryker\Zed\Refund\Business\RefundFacadeInterface;
use Spryker\Zed\Tax\Business\TaxFacadeInterface;

class ExpenseManager
{
    public const DEPOSIT_RETURN_TYPE_POSTFIX = '_%d_%s';

    public const QUANTITY_DEPOSIT_ID = 'depositId';
    public const QUANTITY_COMPLETE = 'deposit';
    public const QUANTITY_CASES = 'cases';
    public const QUANTITY_BOTTLES = 'bottles';


    public const DEPOSIT_TYPE_LABELS = [
        self::QUANTITY_COMPLETE => 'Gebinde',
        self::QUANTITY_CASES => 'Rahmen',
        self::QUANTITY_BOTTLES => 'einzelne Flasche(n)'
    ];

    public const QUANTITY_TYPES_ARRAY = [self::QUANTITY_COMPLETE, self::QUANTITY_CASES, self::QUANTITY_BOTTLES];

    /**
     * @var \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var \Spryker\Zed\Calculation\Business\CalculationFacadeInterface
     */
    protected $calculationFacade;

    /**
     * @var \Spryker\Zed\Refund\Business\RefundFacadeInterface
     */
    protected $refundFacade;

    /**
     * @var \Spryker\Zed\Tax\Business\TaxFacadeInterface
     */
    protected $taxFacade;

    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var DepositSkuTransfer
     */
    protected $depositSkuTransfer;

    /**
     * ExpenseManager constructor.
     * @param SalesFacadeInterface $salesFacade
     * @param CalculationFacadeInterface $calculationFacade
     * @param RefundFacadeInterface $refundFacade
     * @param TaxFacadeInterface $taxFacade
     * @param \Pyz\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     */
    public function __construct(
        SalesFacadeInterface $salesFacade,
        CalculationFacadeInterface $calculationFacade,
        RefundFacadeInterface $refundFacade,
        TaxFacadeInterface $taxFacade,
        MerchantFacadeInterface $merchantFacade
    ) {
        $this->salesFacade = $salesFacade;
        $this->calculationFacade = $calculationFacade;
        $this->refundFacade = $refundFacade;
        $this->taxFacade = $taxFacade;
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * @param OrderTransfer $order
     * @param BranchTransfer $branchTransfer
     * @param array $depositQuantities
     * @param int $refund
     * @param string $refundComment
     * @return bool
     */
    public function addReturnDepositRefundExpenseToOrder(
        OrderTransfer $order,
        BranchTransfer $branchTransfer,
        array $depositQuantities,
        int $refund,
        string $refundComment
    ): bool {

        $isExternalB2b = $this->isExternalB2bOrder($order);

        /** @var \Generated\Shared\Transfer\ReturnDepositTransfer $item */
        foreach ($depositQuantities as $item) {

            $depositQuantityItem = $item->toArray();
            $this->depositSkuTransfer = $this->getDepositSkuByDepositIdForBranch($branchTransfer->getIdBranch(), $item->getDepositId());

            foreach (self::QUANTITY_TYPES_ARRAY as $quantityType){

                $typePostfix = sprintf(self::DEPOSIT_RETURN_TYPE_POSTFIX,
                    $this->depositSkuTransfer->getIdDeposit(),
                    strtoupper($quantityType)
                );

                if(intval($depositQuantityItem[$quantityType]) > 0){
                    $expense = $this->createDepositExpense(
                        $this->getDepositValueByType($quantityType, $isExternalB2b),
                        $depositQuantityItem[$quantityType],
                        $order,
                        $quantityType,
                        $typePostfix
                    );

                    $this->addExpenseToOrder(
                        $order,
                        $expense,
                        DepositConstants::DEPOSIT_RETURN_EXPENSE_TYPE . $typePostfix
                    );
                }
            }
        }

        if ($refund > 0) {
            $refundTransfer = $this->createRefundTransfer($order->getIdSalesOrder(), $refund, $refundComment);
            $this->refundFacade->saveRefund($refundTransfer);

            $expense = $this->createRefundExpense($refund, $order);
            $this->addExpenseToOrder($order, $expense, SalesConstants::REFUND_EXPENSE_TYPE);
        }

        $order = $this->calculationFacade->recalculateOrder($order);

        return $this->salesFacade->updateOrder($order, $order->getIdSalesOrder());
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     * @param string $expenseType
     *
     * @return void
     */
    protected function addExpenseToOrder(
        OrderTransfer $orderTransfer,
        ExpenseTransfer $expenseTransfer,
        string $expenseType
    ) {
        foreach ($orderTransfer->getExpenses() as $expense) {
            if ($expense->getType() === $expenseType) {
                return;
            }
        }

        $orderTransfer->addExpense($expenseTransfer);
    }

    /**
     * @param int $idOrder
     * @param int $amount
     * @param string $comment
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    protected function createRefundTransfer(int $idOrder, int $amount, string $comment): RefundTransfer
    {
        $refundTransfer = new RefundTransfer();
        $refundTransfer->setFkSalesOrder($idOrder);
        $refundTransfer->setAmount($amount);
        $refundTransfer->setComment($comment);

        return $refundTransfer;
    }

    /**
     * @param int $amount
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\ExpenseTransfer
     */
    protected function createRefundExpense(int $amount, OrderTransfer $orderTransfer): ExpenseTransfer
    {
        $expense = $this->getRefundExpense($orderTransfer);
        $expense->setQuantity(1)
            ->setUnitGrossPrice($amount)
            ->setSumGrossPrice($amount)
            ->setUnitPrice($amount)
            ->setSumPrice($amount)
            ->setIsNegative(true);

        return $expense;
    }

    /**
     * @param int $depositValue
     * @param int $quantity
     * @param OrderTransfer $orderTransfer
     * @param string $type
     * @param string $typePostfix
     *
     * @return ExpenseTransfer
     */
    protected function createDepositExpense(int $depositValue, int $quantity, OrderTransfer $orderTransfer, string $type, string $typePostfix): ExpenseTransfer
    {
        $expense = $this->getReturnDepositExpense($orderTransfer, $type, $typePostfix);
        $expense->setQuantity($quantity)
            ->setSumPrice($depositValue)
            ->setUnitGrossPrice($depositValue)
            ->setSumGrossPrice($depositValue)
            ->setUnitPrice($depositValue)
            ->setIsNegative(true);

        return $expense;
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param string $type
     * @param string $typePostfix
     * @return ExpenseTransfer
     */
    protected function getReturnDepositExpense(OrderTransfer $orderTransfer, string $type, string $typePostfix): ExpenseTransfer
    {
        foreach($orderTransfer->getExpenses() as $expense){

            if($expense->getType() === DepositConstants::DEPOSIT_RETURN_EXPENSE_TYPE . $typePostfix) {
                return $expense;
            }
        }

        return (new ExpenseTransfer())
            ->setType(DepositConstants::DEPOSIT_RETURN_EXPENSE_TYPE . $typePostfix)
            ->setName($this->getReturnDepositDisplayName($type))
            ->setMerchantSku($this->getDepositSkuByType($type))
            ->setTaxRate($this->getTaxRate());
    }

    /**
     * @return float
     */
    protected function getTaxRate(): float
    {
        return $this
            ->taxFacade
            ->getDefaultTaxRate();
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\ExpenseTransfer
     */
    protected function getRefundExpense(OrderTransfer $orderTransfer): ExpenseTransfer
    {
        foreach ($orderTransfer->getExpenses() as $expense) {
            if ($expense->getType() === SalesConstants::REFUND_EXPENSE_TYPE) {
                return $expense;
            }
        }

        return (new ExpenseTransfer())
            ->setType(SalesConstants::REFUND_EXPENSE_TYPE)
            ->setName(SalesConstants::REFUND_EXPENSE_DISPLAY_NAME)
            ->setTaxRate($this->getTaxRate());
    }

    /**
     * @param string $type
     * @param bool $isExternalB2b
     * @return int
     */
    protected function getDepositValueByType(string $type, bool $isExternalB2b) : int
    {
        $depositValue = 0;

        switch ($type) {
            case self::QUANTITY_COMPLETE:
                $depositValue = $this->depositSkuTransfer->getDepositValue();
                break;
            case self::QUANTITY_CASES:
                $depositValue =  $this->depositSkuTransfer->getDepositCase();
                break;
            case self::QUANTITY_BOTTLES:
                $depositValue = $this->depositSkuTransfer->getDepositBottle();
                break;
        }

        if($isExternalB2b === true){
            return $this->getB2BDepositValue($depositValue);
        }

        return $depositValue;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getDepositTypeLabelByType(string $type) : string
    {
        return self::DEPOSIT_TYPE_LABELS[$type];
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getReturnDepositDisplayName(string $type) : string
    {
        return sprintf('%s - %s - %s',
            DepositConstants::DEPOSIT_RETURN_EXPENSE_DISPLAY_NAME,
            $this->depositSkuTransfer->getDepositName(),
            $this->getDepositTypeLabelByType($type)
        );
    }

    /**
     * @param int $idBranch
     * @param int $idDeposit
     *
     * @return DepositSkuTransfer
     */
    protected function getDepositSkuByDepositIdForBranch(int $idBranch, int $idDeposit): DepositSkuTransfer
    {
        return $this
            ->merchantFacade
            ->getDepositSkuByDepositIdForBranch($idBranch, $idDeposit);
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getDepositSkuByType(string $type) : string
    {
        switch ($type) {
            case self::QUANTITY_COMPLETE:
                return $this->depositSkuTransfer->getSku();
            case self::QUANTITY_CASES:
                return $this->depositSkuTransfer->getSkuCase();
            case self::QUANTITY_BOTTLES:
                return $this->depositSkuTransfer->getSkuBottle();
            default:
                return '';
        }
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @return bool
     */
    protected function isExternalB2bOrder(OrderTransfer $orderTransfer) : bool
    {
        return ($orderTransfer->getIsExternal() === true && $orderTransfer->getIsPrivate() === false);
    }

    /**
     * @param int $depositValue
     * @return int
     */
    protected function getB2BDepositValue(int $depositValue) : int
    {
        $taxRate = (100 + $this->getTaxRate()) / 100;

        return round($depositValue * $taxRate);
    }
}
