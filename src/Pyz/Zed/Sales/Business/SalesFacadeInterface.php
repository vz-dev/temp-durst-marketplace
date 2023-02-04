<?php
/**
 * Durst - project - SalesFacadeInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 08.10.18
 * Time: 15:16
 */

namespace Pyz\Zed\Sales\Business;

use DateTime;
use DateTimeInterface;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\GraphhopperTourTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderDetailsCommentsTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\SpySalesOrderItemEntityTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;
use Spryker\Zed\Sales\Business\SalesFacadeInterface as SprykerSalesFacadeInterface;

interface SalesFacadeInterface extends SprykerSalesFacadeInterface
{
    /**
     * Returns all comments that are of the type 'customer' for the given order id
     *
     * @param int $idSalesOrder
     *
     * @return OrderDetailsCommentsTransfer
     * @api
     *
     */
    public function getCustomerOrderCommentsByIdSalesOrder($idSalesOrder) : OrderDetailsCommentsTransfer;

    /**
     * Returns all comments that are of the type 'merchant' for the given order id
     *
     * @param int $idSalesOrder
     *
     * @return OrderDetailsCommentsTransfer
     */
    public function getMerchantOrderCommentsByIdSalesOrder($idSalesOrder) : OrderDetailsCommentsTransfer;

    /**
     * Fill the GTINs for a given order item
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateItemGtin(OrderTransfer $orderTransfer): OrderTransfer;

    /**
     * Specification:
     *  - Saves the passed base 64 string in a location defined by the config.
     *  - A unique filename will be generated randomly.
     *  - If the passed string is not validly base 64 coded an exception will be thrown
     *    @see \Pyz\Zed\Sales\Business\Exception\InvalidBase64StringException
     *  - Returns the complete file path of the saved file
     *
     * @param string $data
     * @return string
     */
    public function storeBase64StringAsFile(string $data): string;

    /**
     * Specification:
     *  - groups all passed sales order items with quantity 1 to new order items with quantity n based on
     *    product item sku
     * - returns new OrderTransfer with the grouped order items
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateSalesOrderItems(OrderTransfer $orderTransfer): OrderTransfer;

    /**
     * Specification:
     *  - groups all passed order items with quantity 1 to new sales expense with quantity n based on
     *    product item sku
     * - returns new OrderTransfer with the grouped order expenses
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateSalesExpenses(OrderTransfer $orderTransfer): OrderTransfer;

    /**
     * Specification:
     *  - groups all passed order refunds with quantity 1 to new refunds with quantity n based on
     *    product item sku
     * - returns new OrderTransfer with the grouped order refunds
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateSalesRefunds(OrderTransfer $orderTransfer): OrderTransfer;

    /**
     * Specification:
     *  - returns Expense Transfer for the Expense with the passed id
     * - throws a Exception if there is no Exception with the passed id
     *
     * @param int $idExpense
     * @return ExpenseTransfer
     */
    public function getSalesExpenseById(int $idExpense): ExpenseTransfer;

    /**
     * Returns the order for the given sales order id. Added Branch Id to ensure Merchants can only retrieve
     * orders assigned to branches belonging to them. the orders are deflate meaning all order items, expenses and
     * refunds are grouped together for display purposes
     *
     * @param int $idSalesOrder
     * @param int $branchId
     * @return OrderTransfer
     * @throws PropelException
     * @throws AmbiguousComparisonException
     * @throws InvalidSalesOrderException
     */
    public function getDeflatedOrderByIdSalesOrderAndBranchId(int $idSalesOrder, int $branchId): OrderTransfer;

    /**
     * Returns the order for the given sales order id. The orders are deflated meaning all order items, expenses and
     * refunds are grouped together for display purposes
     *
     * @param int $idSalesOrder
     * @return OrderTransfer
     * @throws PropelException
     * @throws AmbiguousComparisonException
     * @throws InvalidSalesOrderException
     */
    public function getDeflatedOrderByIdSalesOrder(int $idSalesOrder): OrderTransfer;

    /**
     * @param GraphhopperTourTransfer $graphhopperTourTransfer
     * @return void
     * @throws PropelException
     */
    public function updateOrdersByGraphhopperOrder(GraphhopperTourTransfer $graphhopperTourTransfer);

    /**
     * @param string $startDate
     * @param string $endDate
     * @param int $branchId
     * @return OrderTransfer[]
     */
    public function getOrdersByInvoiceDateBetweenStartAndEndDateForBranchId(string $startDate, string $endDate, int $branchId): array;

    /**
     * Get a list of all order items in certain states for a given branch and in the given range
     *
     * @param int $idBranch
     * @param array $states
     * @param DateTime $start
     * @param DateTime $end
     * @return SpySalesOrderItemEntityTransfer[]
     */
    public function getOrderItemsByBranchAndStateAndDateRange(
        int $idBranch,
        array $states,
        DateTime $start,
        DateTime $end
    ): array;

    /**
     * Get a list of order item state ids by the names of the states
     *
     * @param array $stateNames
     * @return int[]
     */
    public function getStateIdsByStateNames(array $stateNames): array;

    /**
     * Increment the fail counter for a specific sales order by its id
     *
     * @param int $idSalesOrder
     * @return bool
     */
    public function incrementSalesOrderRetryCounter(int $idSalesOrder): bool;

    /**
     * Resets the fail counter for a specific sales order by its id
     *
     * @param int $idSalesOrder
     * @return bool
     */
    public function resetSalesOrderRetryCounter(int $idSalesOrder): bool;

    /**
     * Set the confirmation date on a specific sales order
     * identified by its id
     * Confirmation date could be NULL in case the confirmation failed
     *
     * @param int $idSalesOrder
     * @param DateTime|null $confirmationDate
     * @return bool
     */
    public function updateConfirmationDate(
        int $idSalesOrder,
        ?DateTime $confirmationDate
    ): bool;

    /**
     * Save the Integra customer id to the given order
     *
     * @param SaveOrderTransfer $orderTransfer
     * @param string|null $customerId
     * @return void
     */
    public function saveIntegraCustomerId(
        SaveOrderTransfer $orderTransfer,
        ?string $customerId
    ): void;

    /**
     * Returns the first order which has a Durst customer reference for the given merchant ID and e-mail
     *
     * @param int $idMerchant
     * @param string $email
     * @return OrderTransfer|null
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     */
    public function findOrderWithDurstCustomerReferenceByIdMerchantAndEmail(int $idMerchant, string $email): ?OrderTransfer;

    /**
     * Update the issuer for the cancellation on the given sales order identified by its ID
     *
     * @param int $idSalesOrder
     * @param string $issuer
     * @return bool
     */
    public function updateCancelIssuer(
        int $idSalesOrder,
        string $issuer
    ): bool;

    /**
     * Update the message for the cancellation on the given sales order identified by its ID
     *
     * @param int $idSalesOrder
     * @param string|null $message
     * @return bool
     */
    public function updateCancelMessage(
        int $idSalesOrder,
        ?string $message = null
    ): bool;

    /**
     * @param int $idSalesOrder
     * @param int $idDriver
     * @return bool
     */
    public function updateDriverForOrder(
        int $idSalesOrder,
        int $idDriver
    ): bool;

    /**
     * Returns order items with their OMS state histories for time slots after
     * the specified start date, optionally filtered by order process and
     * order item state names
     *
     * @param DateTimeInterface $startDate
     * @param array $processNames
     * @param array $stateNames
     * @return array|ItemTransfer[]
     * @throws AmbiguousComparisonException
     */
    public function getOrderItemsByProcessesAndStates(
        DateTimeInterface $startDate,
        array $processNames = [],
        array $stateNames = []
    ): array;

    /**
     * Sets stuck flag of the order items with the specified IDs
     *
     * @param array $idSalesOrderItems
     * @param bool $value
     */
    public function setOrderItemsStuck(array $idSalesOrderItems, bool $value): void;

    /**
     * Returns orders with the specified IDs
     *
     * @param array $idSalesOrders
     * @return array|OrderTransfer[]
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function getMultipleOrdersByIdSalesOrders(array $idSalesOrders): array;

    /**
     * Sets the delivery status value of the item with the specified ID
     *
     * @param int $idSalesOrderItem
     * @param string $value
     * @throws PropelException
     */
    public function setOrderItemDeliveryStatus(int $idSalesOrderItem, string $value): void;

    /**
     * Sets, if the customer returned by Heidelpay is true or false
     * Also sets the flag for is customer requested to true
     *
     * @param int $idSalesOrder
     * @param bool $state
     * @return bool
     */
    public function updateHeidelpayCustomerState(
        int $idSalesOrder,
        bool $state
    ): bool;

    /**
     * Specification:
     *  - Returns persisted information for multiple orders and their totals stored into OrderTransfers
     *  - Hydrates orders by calling HydrateOrderPlugins registered in project dependency provider
     *
     * @param array $orderReferences
     * @return OrderTransfer[]|array
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function getMultipleOrdersWithTotalsByOrderReferences(array $orderReferences): array;

    /**
     * Specification:
     *  - Returns persisted information for order with given reference stored into OrderTransfer
     *  - Hydrates order by calling HydrateOrderPlugin's registered in project dependency provider
     *
     * @param string $orderReference
     *
     * @return OrderTransfer
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function getOrderByReference(string $orderReference): OrderTransfer;

    /**
     * Specification
     * - Updates sales order with given reference with data from order transfer
     * - Returns true if order was successfully updated
     *
     * @param OrderTransfer $orderTransfer
     * @param string $orderReference
     *
     * @return bool
     *
     * @throws PropelException
     */
    public function updateOrderByReference(OrderTransfer $orderTransfer, string $orderReference): bool;
}
