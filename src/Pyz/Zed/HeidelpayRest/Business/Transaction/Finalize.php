<?php
/**
 * Durst - project - Finalize.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.01.20
 * Time: 16:49
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction;

use Generated\Shared\Transfer\HeidelpayRestPaymentTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use heidelpayPHP\Exceptions\HeidelpayApiException;
use heidelpayPHP\Resources\TransactionTypes\Shipment;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\HeidelpayRest\Business\Exception\LogEntryNotFoundException;
use Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface;
use Pyz\Zed\HeidelpayRest\HeidelpayRestConfig;

class Finalize implements FinalizeInterface
{
    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface
     */
    protected $heidelpayRestPayment;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface
     */
    protected $clientWrapper;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface
     */
    protected $transactionLogger;

    /**
     * @var \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface
     */
    protected $moneyUtil;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface
     */
    protected $salesFacade;

    /**
     * Finalize constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface $heidelpayRestPayment
     * @param \Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface $clientWrapper
     * @param \Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface $transactionLogger
     * @param \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig $config
     * @param \Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface $moneyUtil
     * @param \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface $salesFacade
     */
    public function __construct(
        HeidelpayRestPaymentInterface $heidelpayRestPayment,
        ClientWrapperInterface $clientWrapper,
        LoggerInterface $transactionLogger,
        HeidelpayRestConfig $config,
        MoneyUtilInterface $moneyUtil,
        HeidelpayRestToSalesBridgeInterface $salesFacade
    ) {
        $this->heidelpayRestPayment = $heidelpayRestPayment;
        $this->clientWrapper = $clientWrapper;
        $this->transactionLogger = $transactionLogger;
        $this->config = $config;
        $this->moneyUtil = $moneyUtil;
        $this->salesFacade = $salesFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function finalizePaymentForOrder(OrderTransfer $orderTransfer): void
    {
        $this->checkOrderAssertions($orderTransfer);

        $paymentTransfer = $this
            ->heidelpayRestPayment
            ->getHeidelpayRestPaymentByIdSalesOrder($orderTransfer->getIdSalesOrder());

        $this->cancelCancelableAmount($paymentTransfer, $orderTransfer);

        try {
            $shipment = $this
                ->shipPayment($paymentTransfer->getPaymentId(), $orderTransfer->getInvoiceReference(), $orderTransfer);

            $this
                ->transactionLogger
                ->logShipment($shipment, $orderTransfer->getIdSalesOrder());
        } catch (HeidelpayApiException $e) {
            $this
                ->transactionLogger
                ->logError(
                    $e,
                    $orderTransfer->getIdSalesOrder(),
                    null,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_FINALIZE
                );
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function finalizePaymentWithoutCancelForOrder(OrderTransfer $orderTransfer): void
    {
        $this
            ->checkOrderAssertions($orderTransfer);

        $paymentTransfer = $this
            ->heidelpayRestPayment
            ->getHeidelpayRestPaymentByIdSalesOrder(
                $orderTransfer
                    ->getIdSalesOrder()
            );

        $this
            ->salesFacade
            ->incrementSalesOrderRetryCounter(
                $orderTransfer
                    ->getIdSalesOrder()
            );

        try {
            $shipment = $this
                ->shipPayment(
                    $paymentTransfer
                        ->getPaymentId(),
                    $orderTransfer
                        ->getInvoiceReference(),
                    $orderTransfer
                );

            $this
                ->transactionLogger
                ->logShipment(
                    $shipment,
                    $orderTransfer
                        ->getIdSalesOrder()
                );
        } catch (HeidelpayApiException $apiException) {
            $this
                ->transactionLogger
                ->logError(
                    $apiException,
                    $orderTransfer
                        ->getIdSalesOrder(),
                    null,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_FINALIZE
                );
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return bool
     */
    public function isShipmentCompleted(OrderTransfer $orderTransfer): bool
    {
        try {
            $this
                ->transactionLogger
                ->getLogByIdSalesOrderTypeAndStatus(
                    $orderTransfer->getIdSalesOrder(),
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_FINALIZE,
                    [
                        HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS,
                    ]
                );
        } catch (LogEntryNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array $paymentTypes
     * @return bool
     */
    public function isShipmentPendingOrRecoverableErrors(
        OrderTransfer $orderTransfer,
        array $paymentTypes
    ): bool
    {
        try {
            $this
                ->transactionLogger
                ->getLogByIdSalesOrderTypesAndPendingOrRecoverableErrors(
                    $orderTransfer
                        ->getIdSalesOrder(),
                    $paymentTypes,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_ERROR,
                    $this
                        ->config
                        ->getHeidelpayRestRecoverableErrors()
                );
        } catch (LogEntryNotFoundException $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param string $type
     * @return bool
     */
    public function isShipmentApprovedForOrderAndTransactionType(
        OrderTransfer $orderTransfer,
        string $type
    ): bool
    {
        try {
            $this
                ->transactionLogger
                ->getLogByIdSalesOrderTypeAndStatus(
                    $orderTransfer->getIdSalesOrder(),
                    $type,
                    [
                        HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS
                    ]
                );
        } catch (LogEntryNotFoundException $entryNotFoundException) {
            return false;
        }

        $this
            ->salesFacade
            ->resetSalesOrderRetryCounter(
                $orderTransfer
                    ->getIdSalesOrder()
            );

        return true;
    }

    /**
     * @param \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer $paymentTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    protected function cancelCancelableAmount(
        HeidelpayRestPaymentTransfer $paymentTransfer,
        OrderTransfer $orderTransfer
    ): void {
        try {
            $payment = $this
                ->clientWrapper
                ->getHeidelpayClient($orderTransfer)
                ->fetchPayment($paymentTransfer->getPaymentId());

            $cancelableAmount = $this
                ->moneyUtil
                ->getCancelableAmountForOrder($payment->getAmount()->getTotal(), $orderTransfer);

            $cancelableAmount = $this
                ->moneyUtil
                ->getFloatFromInt($cancelableAmount);

            $cancellation = $payment
                ->getChargeByIndex(0)
                ->cancel($cancelableAmount);

            $this
                ->transactionLogger
                ->logCancellation(
                    $cancellation,
                    $orderTransfer->getIdSalesOrder()
                );
        } catch (HeidelpayApiException $e) {
            $this
                ->transactionLogger
                ->logError(
                    $e,
                    $orderTransfer->getIdSalesOrder(),
                    null,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
                );
        }
    }

    /**
     * @param string $paymentId
     * @param string $invoiceReference
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Shipment
     */
    protected function shipPayment(
        string $paymentId,
        string $invoiceReference,
        OrderTransfer $orderTransfer
    ): Shipment {
        return $this
            ->clientWrapper
            ->getHeidelpayClient($orderTransfer)
            ->ship(
                $paymentId,
                $invoiceReference
            );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    protected function checkOrderAssertions(OrderTransfer $orderTransfer): void
    {
        $orderTransfer
            ->requireInvoiceReference();
    }
}
