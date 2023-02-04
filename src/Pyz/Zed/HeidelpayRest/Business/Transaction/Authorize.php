<?php
/**
 * Durst - project - Authorize.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 16:35
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction;

use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer;
use Generated\Shared\Transfer\HeidelpayRestPaymentTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use heidelpayPHP\Exceptions\HeidelpayApiException;
use heidelpayPHP\Resources\PaymentTypes\BasePaymentType;
use heidelpayPHP\Resources\TransactionTypes\Authorization;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\HeidelpayRest\Business\Exception\LogEntryNotFoundException;
use Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface;
use Pyz\Zed\HeidelpayRest\Business\PaymentType\PaymentTypeInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\MetaData\BillingPeriodInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Resource\CustomerInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\CompleteAuthorization;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToOmsBridgeInterface;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface;
use Pyz\Zed\HeidelpayRest\HeidelpayRestConfig;

class Authorize implements AuthorizeInterface
{
    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface
     */
    protected $clientWrapper;

    /**
     * @var \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface
     */
    protected $moneyUtil;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\PaymentType\PaymentTypeInterface
     */
    protected $paymentType;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface
     */
    protected $heidelpayRestPayment;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface
     */
    protected $transactionLogger;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToOmsBridgeInterface
     */
    protected $omsFacade;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface
     */
    protected $salesFacade;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Transaction\Resource\CustomerInterface
     */
    protected $customerResource;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Transaction\MetaData\BillingPeriodInterface
     */
    protected $billingPeriodMetaData;

    /**
     * Authorize constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface $clientWrapper
     * @param \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig $config
     * @param \Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface $moneyUtil
     * @param \Pyz\Zed\HeidelpayRest\Business\PaymentType\PaymentTypeInterface $paymentType
     * @param \Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface $heidelpayRestPayment
     * @param \Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface $transactionLogger
     * @param \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToOmsBridgeInterface $omsFacade
     * @param \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface $salesFacade
     * @param \Pyz\Zed\HeidelpayRest\Business\Transaction\Resource\CustomerInterface $customerResource
     * @param \Pyz\Zed\HeidelpayRest\Business\Transaction\MetaData\BillingPeriodInterface $billingPeriodMetaData
     */
    public function __construct(
        ClientWrapperInterface $clientWrapper,
        HeidelpayRestConfig $config,
        MoneyUtilInterface $moneyUtil,
        PaymentTypeInterface $paymentType,
        HeidelpayRestPaymentInterface $heidelpayRestPayment,
        LoggerInterface $transactionLogger,
        HeidelpayRestToOmsBridgeInterface $omsFacade,
        HeidelpayRestToSalesBridgeInterface $salesFacade,
        CustomerInterface $customerResource,
        BillingPeriodInterface $billingPeriodMetaData
    ) {
        $this->clientWrapper = $clientWrapper;
        $this->config = $config;
        $this->moneyUtil = $moneyUtil;
        $this->paymentType = $paymentType;
        $this->heidelpayRestPayment = $heidelpayRestPayment;
        $this->transactionLogger = $transactionLogger;
        $this->omsFacade = $omsFacade;
        $this->salesFacade = $salesFacade;
        $this->customerResource = $customerResource;
        $this->billingPeriodMetaData = $billingPeriodMetaData;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     */
    public function authorizeOrder(OrderTransfer $orderTransfer): void
    {
        $paymentTransfer = $this
            ->heidelpayRestPayment
            ->getHeidelpayRestPaymentByIdSalesOrder($orderTransfer->getIdSalesOrder());

        $paymentType = $this
            ->fetchPaymentType($paymentTransfer->getPaymentTypeId(), $orderTransfer);

        $customer = $this
            ->customerResource
            ->getCustomer($orderTransfer);

        $metaData = $this
            ->billingPeriodMetaData
            ->getBillingPeriodMetaData($orderTransfer);

        $amount = $this->moneyUtil->getDecimalGrandTotalForOrder($orderTransfer);

        try {
            $authorization = $this
                ->paymentType
                ->authorize(
                    $paymentType,
                    $amount,
                    $paymentTransfer->getReturnUrl(),
                    $customer,
                    $metaData
                );

            $this
                ->transactionLogger
                ->logAuthorization($authorization, $orderTransfer->getIdSalesOrder(), $amount);

            $paymentTransfer
                ->setPaymentId($authorization->getPaymentId());

            $this
                ->heidelpayRestPayment
                ->updateHeidelpayRestPayment($paymentTransfer);
        } catch (HeidelpayApiException $e) {
            $this
                ->transactionLogger
                ->logError(
                    $e,
                    $orderTransfer->getIdSalesOrder(),
                    $amount,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_AUTHORIZE
                );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return bool
     */
    public function isAuthorizationCompleted(OrderTransfer $orderTransfer): bool
    {
        $status = $this->getStatusOfAuthorization($orderTransfer->getIdSalesOrder());

        return ($status === HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function cancelAuthorization(int $idSalesOrder): void
    {
        $payment = $this
            ->heidelpayRestPayment
            ->getHeidelpayRestPaymentByIdSalesOrder($idSalesOrder);

        if ($payment->getPaymentId() === null) {
            return;
        }

        $orderTransfer = $this
            ->salesFacade
            ->getOrderByIdSalesOrder($idSalesOrder);

        try {
            $cancellation = $this
                ->fetchAuthorization($payment->getPaymentId(), $orderTransfer)
                ->cancel();

            $this
                ->transactionLogger
                ->logCancellation($cancellation, $idSalesOrder);
        } catch (HeidelpayApiException $e) {
            $this
                ->transactionLogger
                ->logError($e, $idSalesOrder, null, HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function postSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer
    {
        if ($quoteTransfer->getPayment()->getHeidelpayRestPayment() === null) {
            return $checkoutResponseTransfer;
        }

        $idSalesOrder = $checkoutResponseTransfer
            ->requireSaveOrder()->getSaveOrder()
            ->requireIdSalesOrder()->getIdSalesOrder();

        try {
            $logTransfer = $this
                ->transactionLogger
                ->getLogByIdSalesOrderTypeAndStatus(
                    $idSalesOrder,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_AUTHORIZE,
                    [
                        HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING,
                        HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_ERROR,
                    ]
                );

            switch ($logTransfer->getStatus()) {
                case HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING:
                    $checkoutResponseTransfer->setIsPending(true);
                    $checkoutResponseTransfer->setReturnUrl($logTransfer->getReturnUrl());
                    $checkoutResponseTransfer->setRedirectUrl($logTransfer->getRedirectUrl());
                    $checkoutResponseTransfer->setPaymentId($logTransfer->getPaymentId());

                    break;
                default:
                    $checkoutResponseTransfer->setIsPending(false);
                    $checkoutResponseTransfer->setIsSuccess(false);
                    $error = (new CheckoutErrorTransfer())
                        ->setErrorCode(HeidelpayRestConfig::ERROR_CODE)
                        ->setMessage($logTransfer->getErrorMessageClient());
                    $checkoutResponseTransfer->addError($error);
            }
        } catch (LogEntryNotFoundException $e) {
            $checkoutResponseTransfer->setIsPending(false);
        }

        return $checkoutResponseTransfer;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return string
     */
    protected function getStatusOfAuthorization(int $idSalesOrder): string
    {

        $statuus = [
            HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS,
            HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING,
            HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_ERROR,
        ];

        foreach ($statuus as $status) {
            try {
                $logTransfer = $this
                    ->transactionLogger
                    ->getLogByIdSalesOrderTypeAndStatus(
                        $idSalesOrder,
                        HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_AUTHORIZE,
                        [
                            $status,
                        ]
                    );
                return $logTransfer->getStatus();
            } catch (LogEntryNotFoundException $e) {
            }
        }

        return HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_ERROR;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $paymentId
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    public function getAuthorizationStatusByPaymentId(string $paymentId): HeidelpayRestAuthorizationTransfer
    {
        $payment = $this
            ->heidelpayRestPayment
            ->getHeidelpayRestPaymentByPaymentId($paymentId);

        return $this->getAuthorizationStatus($payment);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $orderRef
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    public function getAuthorizationStatusBySalesOrderRef(string $orderRef): HeidelpayRestAuthorizationTransfer
    {
        $payment = $this
            ->heidelpayRestPayment
            ->getHeidelpayRestPaymentBySalesOrderRef($orderRef);

        return $this->getAuthorizationStatus($payment);
    }

    /**
     * @param \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer $payment
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    protected function getAuthorizationStatus(HeidelpayRestPaymentTransfer $payment)
    {
        try {
            $orderTransfer = $this
                ->salesFacade
                ->getOrderByIdSalesOrder($payment->getFkSalesOrder());

            $authorization = $this
                ->fetchAuthorization($payment->getPaymentId(), $orderTransfer);

            if ($authorization->isPending()) {
                $this
                    ->transactionLogger
                    ->logAuthorizationCheck(
                        $authorization,
                        $orderTransfer->getIdSalesOrder(),
                        $authorization->getAmount()
                    );
            } else {
                $this
                    ->transactionLogger
                    ->logAuthorization(
                        $authorization,
                        $orderTransfer->getIdSalesOrder(),
                        $authorization->getAmount()
                    );
            }

            $this->triggerCompleteAuthorizationCommand($orderTransfer);

            return $this
                ->authorizationObjectToTransfer($authorization);
        } catch (HeidelpayApiException $e) {
            $this
                ->transactionLogger
                ->logError(
                    $e,
                    $payment->getFkSalesOrder(),
                    null,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_AUTHORIZE
                );

            return (new HeidelpayRestAuthorizationTransfer())
                ->setIsPending(false)
                ->setIsSuccess(false)
                ->setIsError(true)
                ->setErrorMessage($e->getClientMessage());
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    protected function triggerCompleteAuthorizationCommand(OrderTransfer $orderTransfer): void
    {
        $orderItemIds = [];
        foreach ($orderTransfer->getItems() as $item) {
            $orderItemIds[] = $item->getIdOrderItem();
        }

        $this
            ->omsFacade
            ->triggerEventForOrderItems(CompleteAuthorization::NAME, $orderItemIds);
    }

    /**
     * @param \heidelpayPHP\Resources\TransactionTypes\Authorization $object
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    protected function authorizationObjectToTransfer(
        Authorization $object
    ): HeidelpayRestAuthorizationTransfer {
        return (new HeidelpayRestAuthorizationTransfer())
            ->setIsPending($object->isPending())
            ->setIsSuccess($object->isSuccess())
            ->setIsError($object->isError())
            ->setPaymentId($object->getPaymentId())
            ->setReturnUrl($object->getReturnUrl())
            ->setRedirectUrl($object->getRedirectUrl());
    }

    /**
     * @param string $paymentId
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Authorization
     */
    protected function fetchAuthorization(string $paymentId, OrderTransfer $orderTransfer): Authorization
    {
        return $this
            ->clientWrapper
            ->getHeidelpayClient($orderTransfer)
            ->fetchAuthorization($paymentId);
    }

    /**
     * @param string $paymentTypeId
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Resources\PaymentTypes\BasePaymentType
     */
    protected function fetchPaymentType(string $paymentTypeId, OrderTransfer $orderTransfer): BasePaymentType
    {
        return $this
            ->clientWrapper
            ->getHeidelpayClient($orderTransfer)
            ->fetchPaymentType($paymentTypeId);
    }
}
