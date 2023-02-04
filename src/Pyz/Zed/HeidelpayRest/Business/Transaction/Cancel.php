<?php
/**
 * Durst - project - Cancel.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 10.08.20
 * Time: 09:56
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction;

use Generated\Shared\Transfer\HeidelpayRestLogTransfer;
use Generated\Shared\Transfer\HeidelpayRestPaymentTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use heidelpayPHP\Exceptions\HeidelpayApiException;
use heidelpayPHP\Resources\TransactionTypes\Charge;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface;

class Cancel implements CancelInterface
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
     * @var \Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface
     */
    protected $moneyUtil;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface
     */
    protected $salesFacade;

    /**
     * Cancel constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface $heidelpayRestPayment
     * @param \Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface $clientWrapper
     * @param \Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface $transactionLogger
     * @param \Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface $moneyUtil
     * @param \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface $salesFacade
     */
    public function __construct(
        HeidelpayRestPaymentInterface $heidelpayRestPayment,
        ClientWrapperInterface $clientWrapper,
        LoggerInterface $transactionLogger,
        MoneyUtilInterface $moneyUtil,
        HeidelpayRestToSalesBridgeInterface $salesFacade
    ) {
        $this->heidelpayRestPayment = $heidelpayRestPayment;
        $this->clientWrapper = $clientWrapper;
        $this->transactionLogger = $transactionLogger;
        $this->moneyUtil = $moneyUtil;
        $this->salesFacade = $salesFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return array
     */
    public function cancelPaymentForOrder(OrderTransfer $orderTransfer): array
    {
        $charge = $this
            ->getChargeForOrder(
                $orderTransfer
            );

        try {
            $cancellation = $charge
                ->cancel(
                    $charge
                        ->getAmount()
                );

            $this
                ->transactionLogger
                ->logCancellation(
                    $cancellation,
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
                    $charge
                        ->getAmount(),
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
                );
        }

        return [];
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    public function cancelRemainingAmountForOrder(OrderTransfer $orderTransfer): void
    {
        $paymentTransfer = $this
            ->heidelpayRestPayment
            ->getHeidelpayRestPaymentByIdSalesOrder(
                $orderTransfer
                    ->getIdSalesOrder()
            );

        $this
            ->cancelRemainingAmount(
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
     * @return void
     */
    public function cancelCancelableAmountForOrder(OrderTransfer $orderTransfer): void
    {
        $paymentTransfer = $this
            ->heidelpayRestPayment
            ->getHeidelpayRestPaymentByIdSalesOrder(
                $orderTransfer
                    ->getIdSalesOrder()
            );

        $this
            ->cancelCancelableAmount(
                $paymentTransfer,
                $orderTransfer
            );

        $this
            ->salesFacade
            ->incrementSalesOrderRetryCounter(
                $orderTransfer
                    ->getIdSalesOrder()
            );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Charge
     */
    protected function getChargeForOrder(OrderTransfer $orderTransfer): Charge
    {
        $logTransfer = $this
            ->transactionLogger
            ->getLogByIdSalesOrderTypeAndStatus(
                $orderTransfer->getIdSalesOrder(),
                HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE,
                [
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING,
                ]
            );

        $logTransfer
            ->requireChargeId();

        return $this
            ->getChargeByIdAndPaymentId(
                $logTransfer
                    ->getChargeId(),
                $logTransfer
                    ->getPaymentId(),
                $orderTransfer
            );
    }

    /**
     * @param string $chargeId
     * @param string $paymentId
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Charge
     */
    protected function getChargeByIdAndPaymentId(string $chargeId, string $paymentId, OrderTransfer $orderTransfer): Charge
    {
        return $this
            ->clientWrapper
            ->getHeidelpayClient(
                $orderTransfer
            )
            ->fetchChargeById(
                $paymentId,
                $chargeId
            );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer $heidelpayRestPaymentTransfer
     *
     * @return void
     */
    protected function cancelRemainingAmount(
        OrderTransfer $orderTransfer,
        HeidelpayRestPaymentTransfer $heidelpayRestPaymentTransfer
    ): void {
        if ($heidelpayRestPaymentTransfer->getPaymentId() === null) {
            return;
        }

        $remainingAmount = $this
            ->getRemainingAmountFromOrderAndPayment(
                $orderTransfer,
                $heidelpayRestPaymentTransfer
            );

        if ($remainingAmount === 0) {
            $this
                ->logZeroAmountCancellation($orderTransfer->getIdSalesOrder());

            return;
        }

        try {
            $cancellation = $this
                ->clientWrapper
                ->getHeidelpayClient(
                    $orderTransfer
                )
                ->cancelAuthorizationByPayment(
                    $heidelpayRestPaymentTransfer
                        ->getPaymentId()
                );

            $this
                ->transactionLogger
                ->logCancellation(
                    $cancellation,
                    $orderTransfer
                        ->getIdSalesOrder()
                );
        } catch (HeidelpayApiException $exception) {
            $this
                ->transactionLogger
                ->logError(
                    $exception,
                    $orderTransfer
                        ->getIdSalesOrder(),
                    null,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
                );
        }
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
                ->getHeidelpayClient(
                    $orderTransfer
                )
                ->fetchPayment(
                    $paymentTransfer
                        ->getPaymentId()
                );

            $cancelableAmount = $this
                ->moneyUtil
                ->getCancelableAmountForOrder(
                    $payment
                        ->getAmount()
                        ->getTotal(),
                    $orderTransfer
                );

            if ($cancelableAmount === 0) {
                $this
                    ->logZeroAmountCancellation($orderTransfer->getIdSalesOrder());

                return;
            }

            $cancelableAmount = $this
                ->moneyUtil
                ->getFloatFromInt(
                    $cancelableAmount
                );

            $cancellation = $payment
                ->getChargeByIndex(
                    0
                )
                ->cancel(
                    $cancelableAmount
                );

            $this
                ->transactionLogger
                ->logCancellation(
                    $cancellation,
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
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
                );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer $paymentTransfer
     *
     * @return int
     */
    protected function getRemainingAmountFromOrderAndPayment(OrderTransfer $orderTransfer, HeidelpayRestPaymentTransfer $paymentTransfer)
    {
        $payment = $this
            ->clientWrapper
            ->getHeidelpayClient(
                $orderTransfer
            )
            ->fetchPayment(
                $paymentTransfer
                    ->getPaymentId()
            );

        return $this
            ->moneyUtil
            ->getCancelableAmountForOrder(
                $payment
                    ->getAmount()
                    ->getTotal(),
                $orderTransfer
            );
    }

    /**
     * @param int $idSalesOrder
     *
     * @return void
     */
    protected function logZeroAmountCancellation(int $idSalesOrder): void
    {
        $logTransfer = (new HeidelpayRestLogTransfer())
            ->setAmount(0)
            ->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS)
            ->setTransactionType(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION)
            ->setFkSalesOrder($idSalesOrder);

        $this
            ->transactionLogger
            ->log($logTransfer);
    }
}
