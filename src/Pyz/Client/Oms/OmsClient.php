<?php
/**
 * Durst - project - OmsClient.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 23:00
 */

namespace Pyz\Client\Oms;

use Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class OmsClient
 * @package Pyz\Client\Oms
 * @method OmsFactory getFactory()
 */
class OmsClient extends AbstractClient implements OmsClientInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer $dataTransfer
     * @return \Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer
     */
    public function triggerRecalculateEventForOrderItems(OrderRefundReturnDepositFormDataTransfer $dataTransfer): OrderRefundReturnDepositFormDataTransfer
    {
        return $this
            ->getFactory()
            ->createOmsStub()
            ->triggerRecalculateEventForOrderItems($dataTransfer);
    }
}
