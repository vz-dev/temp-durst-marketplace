<?php
/**
 * Durst - project - HeidelpayRestPaymentLogInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 21.01.20
 * Time: 11:38
 */

namespace Pyz\Zed\HeidelpayRest\Business\Model;


use Generated\Shared\Transfer\HeidelpayRestLogTransfer;

interface HeidelpayRestPaymentLogInterface
{
    /**
     * @param int $idSalesOrder
     * @param string $transactionType
     * @return \Generated\Shared\Transfer\HeidelpayRestLogTransfer|null
     */
    public function getHeidelpayRestLogByIdSalesOrderAndTransactionType(
        int $idSalesOrder,
        string $transactionType
    ): ?HeidelpayRestLogTransfer;

    /**
     * @param int $idSalesOrder
     * @return HeidelpayRestLogTransfer[]
     */
    public function getHeidelpayRestLogsByIdSalesOrder(
        int $idSalesOrder
    ): array;
}
