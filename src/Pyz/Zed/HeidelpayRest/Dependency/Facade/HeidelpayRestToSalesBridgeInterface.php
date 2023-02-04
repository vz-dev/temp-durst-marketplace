<?php
/**
 * Durst - project - HeidelpayRestToSalesBridgeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 31.01.19
 * Time: 14:36
 */

namespace Pyz\Zed\HeidelpayRest\Dependency\Facade;

use Generated\Shared\Transfer\OrderDetailsCommentsTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface HeidelpayRestToSalesBridgeInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function getOrderByIdSalesOrder(int $idSalesOrder): OrderTransfer;

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function getDeflatedOrderByIdSalesOrder(int $idSalesOrder): OrderTransfer;

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderDetailsCommentsTransfer
     */
    public function getCustomerOrderCommentsByIdSalesOrder(int $idSalesOrder): OrderDetailsCommentsTransfer;

    /**
     * @param int $idSalesOrder
     * @return bool
     */
    public function incrementSalesOrderRetryCounter(int $idSalesOrder): bool;

    /**
     * @param int $idSalesOrder
     * @return bool
     */
    public function resetSalesOrderRetryCounter(int $idSalesOrder): bool;

    /**
     * @param int $idSalesOrder
     * @return string[]
     */
    public function getDistinctOrderStates(int $idSalesOrder): array;

    /**
     * @param int $idSalesOrder
     * @param bool $state
     * @return bool
     */
    public function updateHeidelpayCustomerState(
        int $idSalesOrder,
        bool $state
    ): bool;
}
