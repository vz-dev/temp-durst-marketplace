<?php
/**
 * Durst - project - InvoiceToHeidelpayRestBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.02.20
 * Time: 16:08
 */

namespace Pyz\Zed\Invoice\Dependency\Facade;


use Generated\Shared\Transfer\HeidelpayRestLogTransfer;

interface InvoiceToHeidelpayRestBridgeInterface
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
}
