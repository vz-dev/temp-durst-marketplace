<?php
/**
 * Durst - project - LoggerInterface.php.
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

interface LoggerInterface
{
    /**
     * @param \Generated\Shared\Transfer\HeidelpayRestLogTransfer $logTransfer
     *
     * @return void
     */
    public function log(HeidelpayRestLogTransfer $logTransfer): void;

    /**
     * @param int $idSalesOrder
     * @param string $transactionType
     * @param string[] $statuses
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestLogTransfer
     */
    public function getLogByIdSalesOrderTypeAndStatus(
        int $idSalesOrder,
        string $transactionType,
        array $statuses
    ): HeidelpayRestLogTransfer;

    /**
     * @param int $idSalesOrder
     * @param string[] $transactionTypes
     * @param string $pending
     * @param string $error
     * @param string[] $recoverableErrors
     * @return \Generated\Shared\Transfer\HeidelpayRestLogTransfer
     */
    public function getLogByIdSalesOrderTypesAndPendingOrRecoverableErrors(
        int $idSalesOrder,
        array $transactionTypes,
        string $pending,
        string $error,
        array $recoverableErrors
    ): HeidelpayRestLogTransfer;

    /**
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
    ): void;

    /**
     * @param \heidelpayPHP\Resources\TransactionTypes\Authorization $authorization
     * @param int $idSalesOrder
     * @param float $amount
     */
    public function logAuthorization(
        Authorization $authorization,
        int $idSalesOrder,
        float $amount
    ): void;

    /**
     * @param \heidelpayPHP\Resources\TransactionTypes\Cancellation $cancellation
     * @param int $idSalesOrder
     */
    public function logCancellation(
        Cancellation $cancellation,
        int $idSalesOrder
    ): void;

    /**
     * @param \heidelpayPHP\Resources\TransactionTypes\Charge $charge
     * @param float $amount
     * @param int $idSalesOrder
     */
    public function logCharge(
        Charge $charge,
        float $amount,
        int $idSalesOrder
    ): void;

    /**
     * @param \heidelpayPHP\Resources\TransactionTypes\Authorization $authorization
     * @param int $idSalesOrder
     * @param float $amount
     */
    public function logAuthorizationCheck(
        Authorization $authorization,
        int $idSalesOrder,
        float $amount
    ): void;

    /**
     * @param \heidelpayPHP\Resources\TransactionTypes\Shipment $shipment
     * @param int $idSalesOrder
     */
    public function logShipment(
        Shipment $shipment,
        int $idSalesOrder
    ): void;

    /**
     * @param \heidelpayPHP\Resources\Metadata $metadata
     * @param $idSalesOrder
     *
     * @return void
     */
    public function logMetaData(
        Metadata $metadata,
        $idSalesOrder
    ): void;
}
