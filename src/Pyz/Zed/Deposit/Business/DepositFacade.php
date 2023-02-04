<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 20.10.17
 * Time: 13:24
 */

namespace Pyz\Zed\Deposit\Business;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\DepositTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class DepositFacade
 * @package Pyz\Zed\Deposit\Business
 * @method \Pyz\Zed\Deposit\Business\DepositBusinessFactory getFactory()
 */
class DepositFacade extends AbstractFacade implements DepositFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\DepositTransfer $depositTransfer
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function updateDeposit(DepositTransfer $depositTransfer)
    {
        return $this
            ->getFactory()
            ->createDepositModel()
            ->save($depositTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idDeposit
     * @return void
     */
    public function removeDeposit($idDeposit)
    {
        $this
            ->getFactory()
            ->createDepositModel()
            ->remove($idDeposit);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Generated\Shared\Transfer\DepositTransfer[]
     */
    public function getDeposits()
    {
        return $this
            ->getFactory()
            ->createDepositModel()
            ->getDeposits();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idDeposit
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function getDepositById($idDeposit)
    {
        return $this
            ->getFactory()
            ->createDepositModel()
            ->getDepositById($idDeposit);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\DepositTransfer $depositTransfer
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function addDeposit(DepositTransfer $depositTransfer)
    {
        return $this
            ->getFactory()
            ->createDepositModel()
            ->addDeposit($depositTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idDeposit
     * @return bool
     */
    public function hasDeposit($idDeposit)
    {
        return $this
            ->getFactory()
            ->createDepositModel()
            ->hasDeposit($idDeposit);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function depositsAreImported()
    {
        return $this
            ->getFactory()
            ->createDepositModel()
            ->depositsAreImported();
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     */
    public function calculateItemDeposit(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFactory()
            ->createCalculator()
            ->recalculate($calculableObjectTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function addDepositToItem(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer
    {
        return $this
            ->getFactory()
            ->createDepositManager()
            ->addDepositToItems($cartChangeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function calculateDepositTotal(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFactory()
            ->createTotalCalculator()
            ->recalculate($calculableObjectTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return mixed|void
     */
    public function calculateDepositTaxRate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFactory()
            ->createDepositTaxRateCalculator()
            ->recalculate($calculableObjectTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     */
    public function saveOrderDeposit(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        $this
            ->getFactory()
            ->createDepositOrderSaver()
            ->saveOrderDeposit($quoteTransfer, $saveOrderTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function calculateDisplayTotal(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFactory()
            ->createTotalCalculator()
            ->recalculateDisplayTotal($calculableObjectTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function calculateWeightTotal(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFactory()
            ->createTotalCalculator()
            ->recalculateWeightTotal($calculableObjectTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $sku
     * @throws Exception\DepositMissingException
     */
    public function getDepositByProductSku(string $sku)
    {
        return $this
            ->getFactory()
            ->createDepositModel()
            ->getDepositForProductBySku($sku);
    }

    /**
     * {@inheritdoc}
     *
     * @param AppApiRequestTransfer $apiRequestTransfer
     * @return int
     * @throws Exception\DepositMissingException
     */
    public function getWeightForApiRequestItems(AppApiRequestTransfer $apiRequestTransfer) : int
    {
        return $this
            ->getFactory()
            ->createDepositModel()
            ->getWeightForApiRequestItems($apiRequestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $sku
     * @return int
     * @throws Exception\DepositMissingException
     */
    public function getWeightForDepositBySku(string $sku): int
    {
        return $this
            ->getFactory()
            ->createDepositModel()
            ->getWeightForDepositBySku($sku);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @return bool
     */
    public function hasOrderDepositReturns(int $idSalesOrder): bool
    {
        return $this
            ->getFactory()
            ->createOrderChecker()
            ->hasOrderDepositReturns($idSalesOrder);
    }

    /**
     * @param int $idSalesOrder
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hasOrderRefunds(int $idSalesOrder): bool
    {
        return $this
            ->getFactory()
            ->createRefundChecker()
            ->hasOrderRefunds($idSalesOrder);
    }

    /**
     * {@inheritdoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function hydrateDepositAmountOrderItem(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createDepositAmountOrderItemHydrator()
            ->hydrateDepositAmountOrderItem($orderTransfer);
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return QuoteTransfer
     */
    public function expandDepositSalesExpenses(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $this
            ->getFactory()
            ->createDepositSalesExpenseExpander()
            ->expandDepositSaleExpense($quoteTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateDepositSalesExpenses(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createSalesExpenseDepositDeflator()
            ->deflateSalesExpenses($orderTransfer);
    }
}
