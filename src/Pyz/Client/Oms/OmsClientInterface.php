<?php
/**
 * Durst - project - OmsClientInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 23:00
 */

namespace Pyz\Client\Oms;


use Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer;

interface OmsClientInterface
{
    /**
     * Specification:
     *  - triggers the oms command "recalculate" for the order items given by the data transfer
     *  - items must be set
     *  - originOrderItems must be set
     *  - branch id must be set
     *  - returnedDeposits must be set
     *
     * @param \Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer $dataTransfer
     * @return OrderRefundReturnDepositFormDataTransfer
     */
    public function triggerRecalculateEventForOrderItems(OrderRefundReturnDepositFormDataTransfer $dataTransfer): OrderRefundReturnDepositFormDataTransfer;
}
