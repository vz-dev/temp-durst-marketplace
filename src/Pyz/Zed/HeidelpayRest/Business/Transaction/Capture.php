<?php
/**
 * Durst - project - Capture.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 16:36
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction;

use Generated\Shared\Transfer\HeidelpayRestLogTransfer;
use Generated\Shared\Transfer\HeidelpayRestPaymentTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use heidelpayPHP\Exceptions\HeidelpayApiException;
use heidelpayPHP\Resources\Customer;
use heidelpayPHP\Resources\Metadata;
use heidelpayPHP\Resources\TransactionTypes\Charge;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\HeidelpayRest\Business\Exception\LogEntryNotFoundException;
use Pyz\Zed\HeidelpayRest\Business\Exception\PaymentWithoutChargesException;
use Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\MetaData\BillingPeriodInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Resource\CustomerInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface;
use Pyz\Zed\HeidelpayRest\HeidelpayRestConfig;

class Capture implements CaptureInterface
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
     * @var \Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface
     */
    protected $moneyUtil;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface
     */
    protected $transactionLogger;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface
     */
    protected $salesFacade;

    /**
     * @var \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Transaction\Resource\CustomerInterface
     */
    protected $customerResource;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Transaction\MetaData\BillingPeriodInterface
     */
    protected $billingPeriodMetaData;

    /**
     * Capture constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface $heidelpayRestPayment
     * @param \Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface $clientWrapper
     * @param \Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface $moneyUtil
     * @param \Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface $transactionLogger
     * @param \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface $salesFacade
     * @param \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig $config
     * @param \Pyz\Zed\HeidelpayRest\Business\Transaction\Resource\CustomerInterface $customerResource
     * @param \Pyz\Zed\HeidelpayRest\Business\Transaction\MetaData\BillingPeriodInterface $billingPeriodMetaData
     */
    public function __construct(
        HeidelpayRestPaymentInterface $heidelpayRestPayment,
        ClientWrapperInterface $clientWrapper,
        MoneyUtilInterface $moneyUtil,
        LoggerInterface $transactionLogger,
        HeidelpayRestToSalesBridgeInterface $salesFacade,
        HeidelpayRestConfig $config,
        CustomerInterface $customerResource,
        BillingPeriodInterface $billingPeriodMetaData
    ) {
        $this->heidelpayRestPayment = $heidelpayRestPayment;
        $this->clientWrapper = $clientWrapper;
        $this->moneyUtil = $moneyUtil;
        $this->transactionLogger = $transactionLogger;
        $this->salesFacade = $salesFacade;
        $this->config = $config;
        $this->customerResource = $customerResource;
        $this->billingPeriodMetaData = $billingPeriodMetaData;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     */
    public function capturePaymentForOrder(OrderTransfer $order): void
    {
        $paymentTransfer = $this
            ->heidelpayRestPayment
            ->getHeidelpayRestPaymentByIdSalesOrder($order->getIdSalesOrder());

        $this->chargePayableAmount($order, $paymentTransfer);

        $this->cancelRemainingAmount($order, $paymentTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function captureChargeWithoutCancelForOrder(OrderTransfer $orderTransfer): void
    {
        $paymentTransfer = $this
            ->heidelpayRestPayment
            ->getHeidelpayRestPaymentByIdSalesOrder(
                $orderTransfer
                    ->getIdSalesOrder()
            );

        $this
            ->chargePayableAmount(
                $orderTransfer,
                $paymentTransfer
            );

        $this
            ->salesFacade
            ->incrementSalesOrderRetryCounter(
                $orderTransfer
                    ->getIdSalesOrder()
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function capturePaymentWithoutCancelForOrder(OrderTransfer $orderTransfer): array
    {
        $paymentTransfer = $this
            ->heidelpayRestPayment
            ->getHeidelpayRestPaymentByIdSalesOrder($orderTransfer->getIdSalesOrder());

        $charge = $this->chargePayableAmount($orderTransfer, $paymentTransfer);

        if ($charge !== null) {
            $paymentTransfer->setPaymentId($charge->getPaymentId());
            $this
                ->heidelpayRestPayment
                ->updateHeidelpayRestPayment($paymentTransfer);
        }

        return $this
            ->getProcessingInformationIfPresent($charge);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     *
     * @return array
     */
    public function getProcessingInformationForOrder(int $idSalesOrder): array
    {
        $orderTransfer = $this
                    ->salesFacade
                    ->getOrderByIdSalesOrder($idSalesOrder);

        $paymentTransfer = $this
            ->heidelpayRestPayment
            ->getHeidelpayRestPaymentByIdSalesOrder($idSalesOrder);

        $charge = $this->fetchFirstChargeOfPayment($paymentTransfer->getPaymentId(), $orderTransfer);

        return $this
            ->getProcessingInformationIfPresent($charge);
    }

    /**
     * @param string $paymentId
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Charge
     */
    protected function fetchFirstChargeOfPayment(string $paymentId, OrderTransfer $orderTransfer): Charge
    {
        $charges = $this
            ->clientWrapper
            ->getHeidelpayClient($orderTransfer)
            ->fetchPayment($paymentId)
            ->getCharges();

        if (count($charges) < 1) {
            throw PaymentWithoutChargesException::build($paymentId);
        }

        return $this
            ->clientWrapper
            ->getHeidelpayClient($orderTransfer)
            ->fetchCharge($charges[0]);
    }

    /**
     * @param \heidelpayPHP\Resources\TransactionTypes\Charge|null $charge|null
     *
     * @return array
     */
    protected function getProcessingInformationIfPresent(?Charge $charge): array
    {
        if ($charge === null ||
            $charge->getBic() === null ||
            $charge->getIban() === null ||
            $charge->getDescriptor() == null ||
            $charge->getHolder() === null) {
            return [];
        }

        return [
            HeidelpayRestConstants::HEIDELPAY_REST_INVOICE_KEY_IBAN => $charge->getIban(),
            HeidelpayRestConstants::HEIDELPAY_REST_INVOICE_KEY_BIC => $charge->getBic(),
            HeidelpayRestConstants::HEIDELPAY_REST_INVOICE_KEY_HOLDER => $charge->getHolder(),
            HeidelpayRestConstants::HEIDELPAY_REST_INVOICE_KEY_DESCRIPTOR => $charge->getDescriptor(),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return bool
     */
    public function isCaptureApproved(OrderTransfer $orderTransfer): bool
    {
        try {
            $this
                ->transactionLogger
                ->getLogByIdSalesOrderTypeAndStatus(
                    $orderTransfer->getIdSalesOrder(),
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE,
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
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return bool
     */
    public function isCapturePendingOrSuccess(OrderTransfer $orderTransfer): bool
    {
        try {
            $this
                ->transactionLogger
                ->getLogByIdSalesOrderTypeAndStatus(
                    $orderTransfer->getIdSalesOrder(),
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE,
                    [
                        HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING,
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
    public function isCapturePendingOrRecoverableErrors(
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
    public function isCaptureApprovedForOrderAndTransactionType(
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
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param float $amount
     * @param \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer $paymentTransfer
     * @param \heidelpayPHP\Resources\Customer|null $customer
     * @param \heidelpayPHP\Resources\Metadata|null $metadata
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Charge
     */
    protected function charge(
        OrderTransfer $orderTransfer,
        float $amount,
        HeidelpayRestPaymentTransfer $paymentTransfer,
        ?Customer $customer = null,
        ?Metadata $metadata = null
    ): Charge {
        if ($paymentTransfer->getPaymentId() === null) {
            return $this
                ->clientWrapper
                ->getHeidelpayClient($orderTransfer)
                ->charge(
                    $amount,
                    HeidelpayRestConstants::HEIDELPAY_REST_CURRENCY_EUR,
                    $paymentTransfer->getPaymentTypeId(),
                    $paymentTransfer->getReturnUrl(),
                    $customer,
                    null,
                    $metadata
                );
        }

        return $this
            ->clientWrapper
            ->getHeidelpayClient($orderTransfer)
            ->chargeAuthorization(
                $paymentTransfer->getPaymentId(),
                $amount
            );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer $paymentTransfer
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Charge|null
     */
    protected function chargePayableAmount(
        OrderTransfer $orderTransfer,
        HeidelpayRestPaymentTransfer $paymentTransfer
    ): ?Charge {

        if ($this->moneyUtil->getGrandTotalForOrder($orderTransfer) === 0) {
            $this->logZeroAmountCharge($orderTransfer->getIdSalesOrder());
            return null;
        }

        $amount = $this->moneyUtil->getDecimalGrandTotalForOrder($orderTransfer);
        $customer = $this->customerResource->getCustomer($orderTransfer);
        $metaData = $this->billingPeriodMetaData->getBillingPeriodMetaData($orderTransfer);

        try {
            $charge = $this
                ->charge(
                    $orderTransfer,
                    $amount,
                    $paymentTransfer,
                    $customer,
                    $metaData
                );

            $this
                ->transactionLogger
                ->logCharge($charge, $amount, $orderTransfer->getIdSalesOrder());

            return $charge;
        } catch (HeidelpayApiException $e) {
            $this
                ->transactionLogger
                ->logError(
                    $e,
                    $orderTransfer->getIdSalesOrder(),
                    $amount,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE
                );

            return null;
        }
    }

    /**
     * @param int $idSalesOrder
     *
     * @return void
     */
    protected function logZeroAmountCharge(int $idSalesOrder): void
    {
        $logTransfer = (new HeidelpayRestLogTransfer())
            ->setAmount(0)
            ->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS)
            ->setTransactionType(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE)
            ->setFkSalesOrder($idSalesOrder);

        $this
            ->transactionLogger
            ->log($logTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     * @param \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer $paymentTransfer
     *
     * @return void
     */
    protected function cancelRemainingAmount(
        OrderTransfer $order,
        HeidelpayRestPaymentTransfer $paymentTransfer
    ): void {
        if ($paymentTransfer->getPaymentId() === null) {
            return;
        }

        try {
            $cancellation = $this
                ->clientWrapper
                ->getHeidelpayClient($order)
                ->cancelAuthorizationByPayment(
                    $paymentTransfer->getPaymentId()
                );

            $this
                ->transactionLogger
                ->logCancellation($cancellation, $order->getIdSalesOrder());
        } catch (HeidelpayApiException $e) {
            $this
                ->transactionLogger
                ->logError(
                    $e,
                    $order->getIdSalesOrder(),
                    null,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
                );
        }
    }
}
