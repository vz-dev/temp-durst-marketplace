<?php
/**
 * Durst - project - HeidelpayRestQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 10:27
 */

namespace Pyz\Zed\HeidelpayRest\Persistence;

use Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery;
use Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestQuery;

interface HeidelpayRestQueryContainerInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestQuery
     */
    public function queryPaymentByIdSalesOrder(int $idSalesOrder): DstPaymentHeidelpayRestQuery;

    /**
     * @param string $orderRef
     *
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestQuery
     */
    public function queryPaymentBySalesOrderRef(string $orderRef): DstPaymentHeidelpayRestQuery;

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery
     */
    public function queryLogByIdSalesOrder(int $idSalesOrder): DstPaymentHeidelpayRestLogQuery;

    /**
     * @param int $idSlesOrder
     * @param string $type
     *
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery
     */
    public function queryLogByIdSalesOrderAndType(int $idSlesOrder, string $type): DstPaymentHeidelpayRestLogQuery;

    /**
     * @param int $idPaymentHeidelpayRest
     *
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestQuery
     */
    public function queryPaymentByIdPaymentHeidelpayRest(int $idPaymentHeidelpayRest): DstPaymentHeidelpayRestQuery;

    /**
     * @param int $idSalesOrder
     * @param string $transactionType
     * @param array $status
     *
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery
     */
    public function queryLogByIdSalesOrderTypeAndStatuses(
        int $idSalesOrder,
        string $transactionType,
        array $status
    ): DstPaymentHeidelpayRestLogQuery;

    /**
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery
     */
    public function queryLog(): DstPaymentHeidelpayRestLogQuery;

    /**
     * @param string $paymentId
     *
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestQuery
     */
    public function queryPaymentByPaymentId(string $paymentId): DstPaymentHeidelpayRestQuery;

    /**
     * @param int $idSalesOrder
     * @param string[] $transactionTypes
     * @param string $pending
     * @param string $error
     * @param array $recoverableErrors
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery
     */
    public function queryLogByIdSalesOrderTypesAndPendingOrRecoverableErrors(
        int $idSalesOrder,
        array $transactionTypes,
        string $pending,
        string $error,
        array $recoverableErrors
    ): DstPaymentHeidelpayRestLogQuery;
}
