<?php
/**
 * Durst - project - Refund.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 16:36
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction;

use ArrayObject;
use Generated\Shared\Transfer\MailRecipientTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use heidelpayPHP\Exceptions\HeidelpayApiException;
use heidelpayPHP\Resources\TransactionTypes\Charge;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\HeidelpayRest\Business\Exception\LogEntryNotFoundException;
use Pyz\Zed\HeidelpayRest\Business\Exception\NegativeAmountException;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface;
use Pyz\Zed\HeidelpayRest\HeidelpayRestConfig;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantWholesaleOrderFailStateMailTypePlugin;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Mail\Business\MailFacadeInterface;

class Refund implements RefundInterface
{
    protected const URL_DETAIL = '/sales/detail/index';
    protected const PARAM_ID_SALES_ORDER = 'id-sales-order';

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
     * @var \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface
     */
    protected $salesFacade;

    /**
     * @var \Spryker\Zed\Mail\Business\MailFacadeInterface
     */
    protected $mailFacade;

    /**
     * Refund constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface $clientWrapper
     * @param \Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface $transactionLogger
     * @param \Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface $moneyUtil
     * @param \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig $config
     * @param \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface $salesFacade
     * @param \Spryker\Zed\Mail\Business\MailFacadeInterface $mailFacade
     */
    public function __construct(
        ClientWrapperInterface $clientWrapper,
        LoggerInterface $transactionLogger,
        MoneyUtilInterface $moneyUtil,
        HeidelpayRestConfig $config,
        HeidelpayRestToSalesBridgeInterface $salesFacade,
        MailFacadeInterface $mailFacade
    ) {
        $this->clientWrapper = $clientWrapper;
        $this->transactionLogger = $transactionLogger;
        $this->moneyUtil = $moneyUtil;
        $this->config = $config;
        $this->salesFacade = $salesFacade;
        $this->mailFacade = $mailFacade;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     * @return array
     */
    public function refundPaymentForOrder(OrderTransfer $orderTransfer): array
    {
        try {
            $charge = $this
                ->getChargeForOrder($orderTransfer);
        } catch (HeidelpayApiException $e) {
            $this
                ->transactionLogger
                ->logError(
                    $e,
                    $orderTransfer->getIdSalesOrder(),
                    0,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
                );

            $this
                ->sendMail(
                    $orderTransfer,
                    MerchantWholesaleOrderFailStateMailTypePlugin::MAIL_TYPE
                );
        }

        $refundableAmount = $this->getRefundableAmount($orderTransfer, $charge->getAmount());

        try {
            $cancellation = $charge
                ->cancel($refundableAmount);

            $this
                ->transactionLogger
                ->logCancellation($cancellation, $orderTransfer->getIdSalesOrder());
        } catch (HeidelpayApiException $e) {
            $this
                ->transactionLogger
                ->logError(
                    $e,
                    $orderTransfer->getIdSalesOrder(),
                    $refundableAmount,
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION
                );
        }

        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isRefundCompleted(OrderTransfer $orderTransfer): bool
    {
        try {
            $this
                ->transactionLogger
                ->getLogByIdSalesOrderTypeAndStatus(
                    $orderTransfer->getIdSalesOrder(),
                    HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION,
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
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param float $chargedAmount
     *
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\NegativeAmountException
     *
     * @return float
     */
    protected function getRefundableAmount(OrderTransfer $orderTransfer, float $chargedAmount): float
    {
        $intAmountToPay = $this
            ->moneyUtil
            ->getGrandTotalForOrder($orderTransfer);

        $intChargedAmount = $this
            ->moneyUtil
            ->getIntFromFloat($chargedAmount);

        $intRefundableAmount = $intChargedAmount - $intAmountToPay;

        if ($intRefundableAmount < 0) {
            throw new NegativeAmountException(
                sprintf(
                    NegativeAmountException::REFUND_MESSAGE,
                    $intRefundableAmount
                )
            );
        }

        return $this
            ->moneyUtil
            ->getFloatFromInt($intRefundableAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Charge
     * @throws \heidelpayPHP\Exceptions\HeidelpayApiException
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
                ]
            );

        $logTransfer->requireChargeId();

        return $this
            ->getChargeByIdAndPaymentId($logTransfer->getChargeId(), $logTransfer->getPaymentId(), $orderTransfer);
    }

    /**
     * @param string $chargeId
     * @param string $paymentId
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Charge
     * @throws \heidelpayPHP\Exceptions\HeidelpayApiException
     */
    protected function getChargeByIdAndPaymentId(string $chargeId, string $paymentId, OrderTransfer $orderTransfer): Charge
    {
        return $this
            ->clientWrapper
            ->getHeidelpayClient($orderTransfer)
            ->fetchChargeById($paymentId, $chargeId);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param string $mailType
     */
    protected function sendMail(
        OrderTransfer $orderTransfer,
        string $mailType
    ): void
    {
        $mailTransfer = new MailTransfer();

        $orderStates = $this
            ->salesFacade
            ->getDistinctOrderStates(
                $orderTransfer
                    ->getIdSalesOrder()
            );

        $subject = sprintf(
            $this
                ->config
                ->getOmsErrorMailSubject(),
            $orderTransfer
                ->getIdSalesOrder(),
            implode(',', $orderStates)
        );

        /* @var $firstItem \Generated\Shared\Transfer\ItemTransfer */
        $firstItem = $orderTransfer
            ->getItems()
            ->offsetGet(0);

        $mailTransfer
            ->setFridgeUrl($this->createFridgeUrl($orderTransfer))
            ->setOrder($orderTransfer)
            ->setProcessName($firstItem->getState()->getName())
            ->setRecipients($this->createMailRecipients())
            ->setSubject($subject)
            ->setType($mailType);

        $this
            ->mailFacade
            ->handleMail(
                $mailTransfer
            );
    }

    /**
     * @return \ArrayObject
     */
    protected function createMailRecipients(): ArrayObject
    {
        $recipients = new ArrayObject();

        foreach ($this->config->getOmsErrorMailRecipients() as $omsErrorMailRecipientEmail => $omsErrorMailRecipientName) {
            $recipient = (new MailRecipientTransfer())
                ->setEmail($omsErrorMailRecipientEmail)
                ->setName($omsErrorMailRecipientName);

            $recipients
                ->append(
                    $recipient
                );
        }

        return $recipients;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     * @return string
     */
    protected function createFridgeUrl(OrderTransfer $order): string
    {
        $queryPath = Url::generate(
            static::URL_DETAIL,
            [
                static::PARAM_ID_SALES_ORDER => $order->getIdSalesOrder()
            ]
        );

        return sprintf(
            '%s%s',
            $this
                ->config
                ->getHostName(),
            $queryPath
        );
    }
}
