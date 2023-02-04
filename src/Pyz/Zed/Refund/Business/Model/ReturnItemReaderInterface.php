<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-20
 * Time: 13:45
 */

namespace Pyz\Zed\Refund\Business\Model;


use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface ReturnItemReaderInterface
{
    /**
     * Set hasReturnItem flag on OrderTransfer if the given order has at least one refund entry
     * identified by the sales order id
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateSalesOrderReturnItemFlag(OrderTransfer $orderTransfer): OrderTransfer;

    /**
     * Set hasOtherThanMissingReturnItem flag on OrderTransfer if the given order has at least one refund entry
     * identified by the sales order id with a status other than missing
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     * @throws AmbiguousComparisonException
     */
    public function hydrateSalesOrderOtherThanMissingReturnItemFlag(OrderTransfer $orderTransfer): OrderTransfer;
}
