<?php
/**
 * Durst - project - InvoiceQueueManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.01.20
 * Time: 16:34
 */

namespace Pyz\Zed\Easybill\Business\Queue;

use Generated\Shared\Transfer\QueueSendMessageTransfer;
use Pyz\Shared\Easybill\EasybillConstants;
use Pyz\Zed\Easybill\Dependency\Client\EasybillToQueueBridgeInterface;

class InvoiceQueueManager implements InvoiceQueueManagerInterface
{
    /**
     * @var \Pyz\Zed\Easybill\Dependency\Client\EasybillToQueueBridgeInterface
     */
    protected $queueClient;

    /**
     * InvoiceQueueManager constructor.
     *
     * @param \Pyz\Zed\Easybill\Dependency\Client\EasybillToQueueBridgeInterface $queueClient
     */
    public function __construct(EasybillToQueueBridgeInterface $queueClient)
    {
        $this->queueClient = $queueClient;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $reference
     */
    public function addReferenceToInvoiceQueue(string $reference): void
    {
        $this
            ->queueClient
            ->sendMessage(
                EasybillConstants::INVOICE_DELAY_QUEUE,
                $this->createQueueSendMessageTransfer($reference)
            );
    }

    /**
     * @param string $body
     *
     * @return \Generated\Shared\Transfer\QueueSendMessageTransfer
     */
    protected function createQueueSendMessageTransfer(string $body): QueueSendMessageTransfer
    {
        return (new QueueSendMessageTransfer())
            ->setBody($body);
    }
}
