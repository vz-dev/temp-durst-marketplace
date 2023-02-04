<?php
/**
 * Durst - project - SalesFacade.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 08.10.18
 * Time: 15:14
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
use Spryker\Zed\Sales\Business\SalesFacade as SprykerSalesFacade;

/**
 * @method SalesBusinessFactory getFactory()
 */
class SalesFacade extends SprykerSalesFacade implements SalesFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @return OrderDetailsCommentsTransfer
     */
    public function getCustomerOrderCommentsByIdSalesOrder($idSalesOrder): OrderDetailsCommentsTransfer
    {
        return $this->getFactory()
            ->createOrderCommentReader()
            ->getCustomerCommentsByIdSalesOrder($idSalesOrder);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @return OrderDetailsCommentsTransfer
     */
    public function getMerchantOrderCommentsByIdSalesOrder($idSalesOrder): OrderDetailsCommentsTransfer
    {
        return $this->getFactory()
            ->createOrderCommentReader()
            ->getMerchantCommentsByIdSalesOrder($idSalesOrder);
    }

    /**
     * @inheritDoc
     */
    public function hydrateItemGtin(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createItemGtinHydrator()
            ->hydrateItemGtin($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $data
     * @return string
     */
    public function storeBase64StringAsFile(string $data): string
    {
        return $this
            ->getFactory()
            ->createBase64FileHelper()
            ->storeStringAsFile($data);
    }

    /**
     * {@inheritDoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateOrderItems(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createOrderItemDeflator()
            ->deflateOrderItems($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateSalesOrderItems(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createSalesOrderItemDeflater()
            ->deflateSalesOrderItems($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateSalesExpenses(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createSalesExpenseDeflater()
            ->deflateSalesExpenses($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function deflateSalesRefunds(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $this
            ->getFactory()
            ->createSalesRefundsDeflater()
            ->deflateSalesRefunds($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idExpense
     * @return ExpenseTransfer
     * @throws Exceptions\ExpenseWithIdNotFoundException
     */
    public function getSalesExpenseById(int $idExpense): ExpenseTransfer
    {
        return $this
            ->getFactory()
            ->createExpenseReader()
            ->getExpenseById($idExpense);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param int $branchId
     * @return OrderTransfer
     * @throws PropelException
     * @throws AmbiguousComparisonException
     * @throws InvalidSalesOrderException
     */
    public function getDeflatedOrderByIdSalesOrderAndBranchId(int $idSalesOrder, int $branchId): OrderTransfer
    {
        return $this->getFactory()
            ->createOrderHydrator()
            ->hydrateDeflatedOrderTransferFromPersistenceByIdSalesOrderAndBranchId($idSalesOrder, $branchId);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return OrderTransfer
     * @throws ContainerKeyNotFoundException
     */
    public function getDeflatedOrderByIdSalesOrder(int $idSalesOrder): OrderTransfer
    {
        return $this->getFactory()
            ->createOrderHydrator()
            ->hydrateDeflatedOrderTransferFromPersistenceByIdSalesOrder($idSalesOrder);
    }

    /**
     * @param GraphhopperTourTransfer $graphhopperTourTransfer
     * @return void
     * @throws PropelException
     */
    public function updateOrdersByGraphhopperOrder(GraphhopperTourTransfer $graphhopperTourTransfer)
    {
        return $this
            ->getFactory()
            ->createOrderUpdater()
            ->updateTourOrdersWithDeliveryOrder($graphhopperTourTransfer);
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param int $branchId
     * @return OrderTransfer[]
     * @throws PropelException
     * @throws ContainerKeyNotFoundException
     * @throws AmbiguousComparisonException
     */
    public function getOrdersByInvoiceDateBetweenStartAndEndDateForBranchId(string $startDate, string $endDate, int $branchId): array
    {
        return $this->getFactory()
            ->createOrderHydrator()
            ->hydrateOrderTransfersFromPersistenceByInvoiceDateBetweenStartAndEndDateForBranchId($startDate, $endDate, $branchId);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param array $states
     * @param DateTime $start
     * @param DateTime $end
     * @return SpySalesOrderItemEntityTransfer[]
     */
    public function getOrderItemsByBranchAndStateAndDateRange(int $idBranch, array $states, DateTime $start, DateTime $end): array
    {
        return $this
            ->getFactory()
            ->createItemReader()
            ->getOrderItemsByBranchAndStateAndDateRange(
                $idBranch,
                $states,
                $start,
                $end
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param array $stateNames
     * @return int[]
     * @throws ContainerKeyNotFoundException
     */
    public function getStateIdsByStateNames(array $stateNames): array
    {
        return $this
            ->getFactory()
            ->createSalesOrderItemStateReader()
            ->getStateIdsByStateNames($stateNames);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return bool
     * @throws PropelException
     */
    public function incrementSalesOrderRetryCounter(int $idSalesOrder): bool
    {
        return $this
            ->getFactory()
            ->createOrderUpdater()
            ->incrementSalesOrderRetryCounter(
                $idSalesOrder
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return bool
     * @throws PropelException
     */
    public function resetSalesOrderRetryCounter(int $idSalesOrder): bool
    {
        return $this
            ->getFactory()
            ->createOrderUpdater()
            ->resetSalesOrderTryCounter(
                $idSalesOrder
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param DateTime|null $confirmationDate
     * @return bool
     * @throws PropelException
     */
    public function updateConfirmationDate(
        int $idSalesOrder,
        ?DateTime $confirmationDate
    ): bool
    {
        return $this
            ->getFactory()
            ->createOrderUpdater()
            ->updateConfirmationDate(
                $idSalesOrder,
                $confirmationDate
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param SaveOrderTransfer $orderTransfer
     * @param string|null $customerId
     * @return void
     * @throws ContainerKeyNotFoundException
     */
    public function saveIntegraCustomerId(SaveOrderTransfer $orderTransfer, ?string $customerId): void
    {
        $this
            ->getFactory()
            ->createIntegraCustomer()
            ->saveIntegraCustomerId(
                $orderTransfer,
                $customerId
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @param string $email
     * @return OrderTransfer|null
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     */
    public function findOrderWithDurstCustomerReferenceByIdMerchantAndEmail(int $idMerchant, string $email): ?OrderTransfer
    {
        return $this
            ->getFactory()
            ->createOrderReader()
            ->findOrderWithDurstCustomerReferenceByIdMerchantAndEmail($idMerchant, $email);
    }

    /**
     * {@inheritDoc}
     *
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
        return $this
            ->getFactory()
            ->createOrderUpdater()
            ->updateCancelIssuer(
                $idSalesOrder,
                $issuer
            );
    }

    /**
     * {@inheritDoc}
     *
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
        return $this
            ->getFactory()
            ->createOrderUpdater()
            ->updateCancelMessage(
                $idSalesOrder,
                $message
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param int $idDriver
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updateDriverForOrder(
        int $idSalesOrder,
        int $idDriver
    ): bool
    {
        return $this
            ->getFactory()
            ->createOrderUpdater()
            ->updateDriver(
                $idSalesOrder,
                $idDriver
            );
    }

    /**
     * {@inheritDoc}
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
    ): array  {
        return $this
            ->getFactory()
            ->createItemReader()
            ->getOrderItemsByProcessesAndStates($startDate, $processNames, $stateNames);
    }

    /**
     * @param array $idSalesOrderItems
     * @param bool $value
     */
    public function setOrderItemsStuck(array $idSalesOrderItems, bool $value): void
    {
        $this
            ->getFactory()
            ->createItemUpdater()
            ->setOrderItemsStuck($idSalesOrderItems, $value);
    }

    /**
     * @param array $idSalesOrders
     * @return array|OrderTransfer[]
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function getMultipleOrdersByIdSalesOrders(array $idSalesOrders): array
    {
        return $this
            ->getFactory()
            ->createOrderHydrator()
            ->hydrateMultipleOrderTransfersByIdSalesOrders($idSalesOrders);
    }

    /**
     * @param int $idSalesOrderItem
     * @param string $value
     * @throws PropelException
     */
    public function setOrderItemDeliveryStatus(int $idSalesOrderItem, string $value): void
    {
        $this
            ->getFactory()
            ->createItemUpdater()
            ->setOrderItemDeliveryStatus($idSalesOrderItem, $value);
    }

    /**
     * {@inheritDoc}
     *
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
        return $this
            ->getFactory()
            ->createOrderUpdater()
            ->updateHeidelpayCustomerState(
                $idSalesOrder,
                $state
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param array $orderReferences
     * @return OrderTransfer[]|array
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function getMultipleOrdersWithTotalsByOrderReferences(array $orderReferences): array
    {
        return $this
            ->getFactory()
            ->createOrderHydrator()
            ->hydrateMultipleOrderTransfersWithTotalsByOrderReferences($orderReferences);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $orderReference
     * @return OrderTransfer
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function getOrderByReference(string $orderReference): OrderTransfer
    {
        return $this->getFactory()
            ->createOrderHydrator()
            ->hydrateOrderTransferFromPersistenceByOrderReference($orderReference);
    }

    /**
     * {@inheritDoc}
     *
     * @param OrderTransfer $orderTransfer
     * @param string $orderReference
     *
     * @return bool
     *
     * @throws PropelException
     */
    public function updateOrderByReference(OrderTransfer $orderTransfer, string $orderReference): bool
    {
        return $this
            ->getFactory()
            ->createOrderUpdater()
            ->updateByOrderReference($orderTransfer, $orderReference);
    }
}
