<?php
/**
 * Durst - project - OmsStub.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 23:04
 */

namespace Pyz\Client\Oms\Zed;

use Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class OmsStub implements OmsStubInterface
{
    protected const URL_TRIGGER_RECALCULATE_EVENT_FOR_ORDER_ITEMS = '/oms/gateway/trigger-recalculate-event-for-order-items';

    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * OmsStub constructor.
     *
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedRequestClient
     */
    public function __construct(ZedRequestClientInterface $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer $dataTransfer
     * @return \Generated\Shared\Transfer\OrderRefundReturnDepositFormDataTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function triggerRecalculateEventForOrderItems(OrderRefundReturnDepositFormDataTransfer $dataTransfer): OrderRefundReturnDepositFormDataTransfer
    {
        return $this
            ->zedRequestClient
            ->call(
                self::URL_TRIGGER_RECALCULATE_EVENT_FOR_ORDER_ITEMS,
                $dataTransfer
            );
    }
}
