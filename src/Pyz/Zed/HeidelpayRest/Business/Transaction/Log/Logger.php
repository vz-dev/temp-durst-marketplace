<?php
/**
 * Durst - project - Logger.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 10:19
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction\Log;

use Generated\Shared\Transfer\HeidelpayRestLogTransfer;
use heidelpayPHP\Exceptions\HeidelpayApiException;
use heidelpayPHP\Resources\Metadata;
use heidelpayPHP\Resources\TransactionTypes\Authorization;
use heidelpayPHP\Resources\TransactionTypes\Cancellation;
use heidelpayPHP\Resources\TransactionTypes\Charge;
use heidelpayPHP\Resources\TransactionTypes\Shipment;
use Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLog;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\HeidelpayRest\Business\Exception\LogEntryNotFoundException;
use Pyz\Zed\HeidelpayRest\HeidelpayRestConfig;
use Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface;

class Logger implements LoggerInterface
{
    /**
     * @var \Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig
     */
    protected $config;

    /**
     * Logger constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig $config
     */
    public function __construct(
        HeidelpayRestQueryContainerInterface $queryContainer,
        HeidelpayRestConfig $config
    ) {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @param string $transactionType
     * @param string[] $status
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\LogEntryNotFoundException
     * @return \Generated\Shared\Transfer\HeidelpayRestLogTransfer
     */
    public function getLogByIdSalesOrderTypeAndStatus(
        int $idSalesOrder,
        string $transactionType,
        array $statuses
    ): HeidelpayRestLogTransfer {
        $entity = $this
            ->queryContainer
            ->queryLogByIdSalesOrderTypeAndStatuses(
                $idSalesOrder,
                $transactionType,
                $statuses
            )
            ->findOne();

        if ($entity === null) {
            throw new LogEntryNotFoundException(
                sprintf(
                    LogEntryNotFoundException::MESSAGE,
                    $transactionType,
                    $idSalesOrder
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param string[] $transactionTypes
     * @param string $pending
     * @param string $error
     * @param string[] $recoverableErrors
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\LogEntryNotFoundException
     * @return \Generated\Shared\Transfer\HeidelpayRestLogTransfer
     */
    public function getLogByIdSalesOrderTypesAndPendingOrRecoverableErrors(
        int $idSalesOrder,
        array $transactionTypes,
        string $pending,
        string $error,
        array $recoverableErrors
    ): HeidelpayRestLogTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryLogByIdSalesOrderTypesAndPendingOrRecoverableErrors(
                $idSalesOrder,
                $transactionTypes,
                $pending,
                $error,
                $recoverableErrors
            )
            ->findOne();

        if ($entity === null) {
            throw new LogEntryNotFoundException(
                sprintf(
                    LogEntryNotFoundException::MESSAGE,
                    implode(',', $transactionTypes),
                    $idSalesOrder
                )
            );
        }

        return $this
            ->entityToTransfer(
                $entity
            );
    }

    /**
     * @param \Generated\Shared\Transfer\HeidelpayRestLogTransfer $logTransfer
     *
     * @return void
     */
    public function log(HeidelpayRestLogTransfer $logTransfer): void
    {
        $logEntity = $this
            ->transferToEntity($logTransfer);

        $logEntity
            ->save();
    }

    /**
     * @param \Generated\Shared\Transfer\HeidelpayRestLogTransfer $logTransfer
     *
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLog
     */
    protected function transferToEntity(HeidelpayRestLogTransfer $logTransfer): DstPaymentHeidelpayRestLog
    {
        $this
            ->assertRequirements($logTransfer);

        $logEntity = new DstPaymentHeidelpayRestLog();

        $logEntity->fromArray($logTransfer->toArray());

        return $logEntity;
    }

    /**
     * @param \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLog $entity
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestLogTransfer
     */
    protected function entityToTransfer(DstPaymentHeidelpayRestLog $entity): HeidelpayRestLogTransfer
    {
        return (new HeidelpayRestLogTransfer())
            ->fromArray($entity->toArray(), true);
    }

    /**
     * @param \Generated\Shared\Transfer\HeidelpayRestLogTransfer $logTransfer
     *
     * @return void
     */
    protected function assertRequirements(HeidelpayRestLogTransfer $logTransfer): void
    {
        $logTransfer
            ->requireFkSalesOrder()
            ->requireTransactionType();
    }

    /**
     * {@inheritdoc}
     *
     * @param \heidelpayPHP\Exceptions\HeidelpayApiException $exception
     * @param int $idSalesOrder
     * @param float|null $amount
     * @param string $transactionType
     */
    public function logError(
        HeidelpayApiException $exception,
        int $idSalesOrder,
        ?float $amount,
        string $transactionType
    ): void {
         $logTransfer = (new HeidelpayRestLogTransfer())
            ->setErrorCode($exception->getCode())
            ->setErrorMessage($exception->getMessage())
            ->setErrorMessageClient($exception->getClientMessage())
            ->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_ERROR)
            ->setAmount($amount)
            ->setFkSalesOrder($idSalesOrder)
            ->setTransactionType($transactionType);

         $this->log($logTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \heidelpayPHP\Resources\TransactionTypes\Authorization $authorization
     * @param int $idSalesOrder
     * @param float $amount
     */
    public function logAuthorization(
        Authorization $authorization,
        int $idSalesOrder,
        float $amount
    ): void {
        $logTransfer = (new HeidelpayRestLogTransfer())
            ->setPaymentId($authorization->getPaymentId())
            ->setTransactionType(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_AUTHORIZE)
            ->setFkSalesOrder($idSalesOrder)
            ->setAmount($amount)
            ->setShortId($authorization->getShortId())
            ->setUniqueId($authorization->getUniqueId())
            ->setRedirectUrl($authorization->getRedirectUrl())
            ->setReturnUrl($authorization->getReturnUrl());

        if ($authorization->isSuccess() === true) {
            $logTransfer->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS);
        }
        if ($authorization->isPending() === true) {
            $logTransfer->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING);
        }
        if ($authorization->isError() === true) {
            $logTransfer->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_ERROR);
            $logTransfer->setErrorMessageClient($this->config->getFallbackErrorMessage());
        }

        $this->log($logTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \heidelpayPHP\Resources\TransactionTypes\Authorization $authorization
     * @param int $idSalesOrder
     * @param float $amount
     */
    public function logAuthorizationCheck(
        Authorization $authorization,
        int $idSalesOrder,
        float $amount
    ): void {
        $logTransfer = (new HeidelpayRestLogTransfer())
            ->setPaymentId($authorization->getPaymentId())
            ->setTransactionType(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_AUTHORIZE_CHECK)
            ->setFkSalesOrder($idSalesOrder)
            ->setAmount($amount)
            ->setShortId($authorization->getShortId())
            ->setUniqueId($authorization->getUniqueId())
            ->setRedirectUrl($authorization->getRedirectUrl())
            ->setReturnUrl($authorization->getReturnUrl());

        if ($authorization->isSuccess() === true) {
            $logTransfer->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS);
        }
        if ($authorization->isPending() === true) {
            $logTransfer->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING);
        }

        $this->log($logTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \heidelpayPHP\Resources\TransactionTypes\Cancellation $cancellation
     * @param int $idSalesOrder
     */
    public function logCancellation(
        Cancellation $cancellation,
        int $idSalesOrder
    ): void {
        $logTransfer = (new HeidelpayRestLogTransfer())
            ->setTransactionType(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CANCELLATION)
            ->setFkSalesOrder($idSalesOrder)
            ->setAmount($cancellation->getAmount())
            ->setPaymentId($cancellation->getPaymentId())
            ->setShortId($cancellation->getShortId())
            ->setUniqueId($cancellation->getUniqueId());

        if ($cancellation->isSuccess() === true) {
            $logTransfer
                ->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS);
        }

        $this->log($logTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \heidelpayPHP\Resources\TransactionTypes\Charge $charge
     * @param float $amount
     * @param int $idSalesOrder
     */
    public function logCharge(
        Charge $charge,
        float $amount,
        int $idSalesOrder
    ): void {
        $logTransfer = (new HeidelpayRestLogTransfer())
            ->setTransactionType(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CHARGE)
            ->setChargeId($charge->getId())
            ->setUniqueId($charge->getUniqueId())
            ->setShortId($charge->getShortId())
            ->setPaymentId($charge->getPaymentId())
            ->setAmount($amount)
            ->setFkSalesOrder($idSalesOrder);

        if ($charge->isSuccess() === true) {
            $logTransfer->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS);
        }
        if ($charge->isPending() === true) {
            $logTransfer->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING);
        }

        $this->log($logTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \heidelpayPHP\Resources\TransactionTypes\Shipment $shipment
     * @param int $idSalesOrder
     */
    public function logShipment(
        Shipment $shipment,
        int $idSalesOrder
    ): void {
        $logTransfer = (new HeidelpayRestLogTransfer())
            ->setTransactionType(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_FINALIZE)
            ->setUniqueId($shipment->getUniqueId())
            ->setShortId($shipment->getShortId())
            ->setPaymentId($shipment->getPaymentId())
            ->setAmount($shipment->getAmount())
            ->setFkSalesOrder($idSalesOrder);

        if ($shipment->isSuccess() === true) {
            $logTransfer->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS);
        }
        if ($shipment->isPending() === true) {
            $logTransfer->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING);
        }

        $this->log($logTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param \heidelpayPHP\Resources\Metadata $metadata
     * @param $idSalesOrder
     *
     * @return void
     */
    public function logMetaData(
        Metadata $metadata,
        $idSalesOrder
    ): void {
        $logTransfer = (new HeidelpayRestLogTransfer())
            ->setTransactionType(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_CREATE)
            ->setFkSalesOrder($idSalesOrder)
            ->setResourceId($metadata->getId())
            ->setStatus(HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS);

        $this->log($logTransfer);
    }
}
