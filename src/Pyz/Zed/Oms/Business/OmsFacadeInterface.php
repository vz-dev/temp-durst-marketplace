<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 24.01.18
 * Time: 10:53
 */

namespace Pyz\Zed\Oms\Business;

use DateTime;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\DurstCompanyTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use iio\libmergepdf\Driver\DriverInterface;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Oms\Business\OmsFacadeInterface as SprykerOmsFacadeInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface OmsFacadeInterface extends SprykerOmsFacadeInterface
{
    /**
     * Creates an invoice for the order defined by the given id. Sends the invoice
     * via Mail to the customer. Saves the invoice reference as well as the
     * time stamp of invoice creation in the order table.
     *
     * @param int $idSalesOrder
     *
     * @return bool
     */
    public function createInvoice(int $idSalesOrder): bool;

    /**
     * Sends an invoice email to the customer of the order with the given id.
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @param string $mailType
     *
     * @param \Generated\Shared\Transfer\MailTransfer|null $mailTransfer
     * @return void
     */
    public function sendInvoiceMail(OrderTransfer $orderTransfer, BranchTransfer $branchTransfer, string $mailType, ?MailTransfer $mailTransfer = null);

    /**
     * Returns a DurstCompanyTransfer with the DurstCompany details taken from Config file
     * to be translated by corresponding glossary entries.
     *
     * @return \Generated\Shared\Transfer\DurstCompanyTransfer
     */
    public function createDurstCompanyTransfer(): DurstCompanyTransfer;

    /**
     * Sends an email to the merchant should the order contain any refund items
     * so that the dispatchers can add refunds/returns manually.
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     *
     * @return void
     */
    public function sendRefundMail(OrderTransfer $orderTransfer, BranchTransfer $branchTransfer, DriverTransfer $tourTransfer);

    /**
     * A function that adds refunds to a order. E.g. Drivers can open a detail page and add refunds and rebates
     * by entering a value and comment in a form field. A RefundTransfer is created and the order is updated
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @param array $depositQuantities
     * @param int $refund
     * @param string $refundComment
     *
     * @return bool
     */
    public function addExpensesToOrder(OrderTransfer $order, BranchTransfer $branchTransfer, array $depositQuantities, int $refund, string $refundComment): bool;

    /**
     * @param SpySalesOrder $order
     * @param BranchTransfer $branchTransfer
     * @param array $refundQuantities
     * @return void
     */
    public function addRefundsToOrder(SpySalesOrder $order, BranchTransfer $branchTransfer, array $refundQuantities);

    /**
     * adds refunds to order for expanded sales items. Sku is used to group order Items and get correct expenses etc.
     *
     * @param SpySalesOrder $order
     * @param BranchTransfer $branchTransfer
     * @param array $refundQuantities
     * @return void
     */
    public function addExpandedItemsRefundsToOrder(SpySalesOrder $order, BranchTransfer $branchTransfer, array $refundQuantities);

    /**
     * Specification:
     *  - creates a file of the base 64 coded signature string
     *  - stores the file
     *  - saves the file path in the order
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param string $signature
     */
    public function addSignatureToOrder(
        OrderTransfer $orderTransfer,
        string $signature
    ): void;

    /**
     * Specification:
     *  - save the given driver in the order
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idDriver
     */
    public function addDriverToOrder(
        OrderTransfer $orderTransfer,
        int $idDriver
    ): void;

    /**
     * Find the first transition log entry for the given sales order id
     * Where the source state is 'ready for delivery' and is_error is false
     * And return the createdAt field (if available)
     *
     * @param int $idSalesOrder
     * @return \DateTime|null
     */
    public function getDeliveryTimeFromTransitionLogByIdSalesOrder(
        int $idSalesOrder
    ): ?DateTime;

    /**
     * Sends an e-mail to the merchant, notifying them about an updated bill.
     *
     * @param BranchTransfer $branchTransfer
     * @param BillingPeriodTransfer $billingPeriodTransfer
     */
    public function sendBillingMail(BranchTransfer $branchTransfer, BillingPeriodTransfer $billingPeriodTransfer): void;

    /**
     * Find the discount identified by its id
     * for a specific sales order and set the amount to the given amount
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idSalesDiscount
     * @param int $newAmount
     */
    public function setNewOrderDiscountAmount(
        OrderTransfer $orderTransfer,
        int $idSalesDiscount,
        int $newAmount
    ): void;

    /**
     * returns an array of all order items and expenses split by type ie.
     * deposits, refunds, discounts etc. for a given ordertransfer
     *
     *
     * @param OrderTransfer $orderTransfer
     * @return array
     */
    public function getSplitExpensesRefundsReturnDepositsFromOrder(OrderTransfer $orderTransfer) : array;

    /**
     * Detects orders which are stuck in certain states and sends a notification e-mail
     *
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function detectStuckOrders(): void;

    /**
     * Specification:
     *  - Reads all order states
     *  - Counts orders in each state and puts into corresponding state
     *  - Return matrix
     *
     * @return array
     */
    public function getOrderItemMatrix(): array;
}
