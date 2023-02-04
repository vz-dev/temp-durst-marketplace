<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 20.10.17
 * Time: 13:23
 */

namespace Pyz\Zed\Deposit\Business;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\DepositTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;

/**
 * Interface DepositFacadeInterface
 * @package Pyz\Zed\Deposit\Business
 */
interface DepositFacadeInterface
{
    /**
     * Updates a deposit in the database, so it matches the given transfer object.
     * The updated transfer object will be returned.
     *
     * @param \Generated\Shared\Transfer\DepositTransfer $depositTransfer
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function updateDeposit(DepositTransfer $depositTransfer);

    /**
     * Removes the deposit matching the given id from the database
     * if existent
     *
     * @param int $idDeposit
     *
     * @return void
     */
    public function removeDeposit($idDeposit);

    /**
     * Returns an array of transfer objects representing all deposits in the database
     *
     * @return \Generated\Shared\Transfer\DepositTransfer[]
     */
    public function getDeposits();

    /**
     * Returns a fully hydrated deposit transfer object matching the given id.
     *
     * @param int $idDeposit
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function getDepositById($idDeposit);

    /**
     * Adds the given deposit transfer to the database. The id of the transfer object must be empty.
     * A fully hydrated transfer object matching the data in the database will be returned.
     *
     * @param \Generated\Shared\Transfer\DepositTransfer $depositTransfer
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function addDeposit(DepositTransfer $depositTransfer);

    /**
     * Returns true if there is a deposit in the database with the given id.
     *
     * @param int $idDeposit
     *
     * @return bool
     */
    public function hasDeposit($idDeposit);

    /**
     * Checks whether there are already deposit data sets in the database.
     *
     * @return bool
     */
    public function depositsAreImported();

    /**
     * Calculates the item deposit and deposit sum for all items.
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function calculateItemDeposit(CalculableObjectTransfer $calculableObjectTransfer);

    /**
     * Adds unit item price to cart items
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function addDepositToItem(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer;

    /**
     * Calculates the sum of the deposit values of all items
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function calculateDepositTotal(CalculableObjectTransfer $calculableObjectTransfer);

    /**
     * Sets the tax rate of the delivery cost expense in the expense object. For now this is
     * simply the default tax rate.
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return mixed
     */
    public function calculateDepositTaxRate(CalculableObjectTransfer $calculableObjectTransfer);

    /**
     * Adds a display total representing the grand total minus all deposit expenses. This
     * is the price we want the customer to see.
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function calculateDisplayTotal(CalculableObjectTransfer $calculableObjectTransfer);

    /**
     * Adds a weight total representing the sum of all deposit weights.
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function calculateWeightTotal(CalculableObjectTransfer $calculableObjectTransfer);

    /**
     * Specification:
     * - Adds deposit sales expense to sales order.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrderDeposit(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer);

    /**
     * Returns a fully hydrated DepositTransfer for the deposit that matches the product with the
     * given sku
     *
     * @param string $sku
     *
     * @return \Generated\Shared\Transfer\DepositTransfer
     */
    public function getDepositByProductSku(string $sku);

    /**
     * Returns the total weight of the Items included in the apiRequestTransfer
     *
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $apiRequestTransfer
     *
     * @return int
     */
    public function getWeightForApiRequestItems(AppApiRequestTransfer $apiRequestTransfer): int;

    /**
     * Returns the deposit weight for the deposit item with the provided sku
     *
     * @param string $sku
     *
     * @return int
     */
    public function getWeightForDepositBySku(string $sku): int;

    /**
     * Checks whether for the order matching the given id exist any negative
     * expenses where the type starts with @see \Pyz\Shared\Deposit\DepositConstants::DEPOSIT_EXPENSE_TYPE
     *
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function hasOrderDepositReturns(int $idSalesOrder): bool;

    /**
     *
     * Checks whether for the order matching the given id exist any negative
     * expenses where the type starts with @see \Pyz\Shared\Sales\SalesConstants::REFUND_EXPENSE_TYPE
     *
     * @param int $idSalesOrder
     * @return bool
     */
    public function hasOrderRefunds(int $idSalesOrder): bool;

    /**
     * hydrates the passed OrderTransfer with with the deposit amounts for the individual
     * order items
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateDepositAmountOrderItem(OrderTransfer $orderTransfer): OrderTransfer;

    /**
     * Expands deposit sales expenses with n quantity to multiple expenses with quantity 1 based
     * on merchant sku
     *
     * @param QuoteTransfer $quoteTransfer
     * @return QuoteTransfer
     */
    public function expandDepositSalesExpenses(QuoteTransfer $quoteTransfer): QuoteTransfer;

    /**
     * Deflates multiple deposit sales expenses with quantity of 1 to grouped expenses with quantity n based
     * on merchant sku
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateDepositSalesExpenses(OrderTransfer $orderTransfer): OrderTransfer;
}
