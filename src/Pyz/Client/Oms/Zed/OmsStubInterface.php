<?php
/**
 * Durst - project - OmsStubInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 23:06
 */

namespace Pyz\Client\Oms\Zed;


use Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer;

interface OmsStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer $dataTransfer
     * @return \Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer
     */
    public function triggerRecalculateEventForOrderItems(OrderRefundReturnDepositFormDataTransfer $dataTransfer): OrderRefundReturnDepositFormDataTransfer;
}
