<?php
/**
 * Durst - project - EasybillToQueueBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.01.20
 * Time: 16:25
 */

namespace Pyz\Zed\Easybill\Dependency\Client;


use Generated\Shared\Transfer\QueueSendMessageTransfer;

class EasybillToQueueBridge implements EasybillToQueueBridgeInterface
{
    /**
     * @var \Spryker\Client\Queue\QueueClientInterface
     */
    protected $queueClient;

    /**
     * EasybillToQueueBridge constructor.
     * @param \Spryker\Client\Queue\QueueClientInterface $queueClient
     */
    public function __construct(\Spryker\Client\Queue\QueueClientInterface $queueClient)
    {
        $this->queueClient = $queueClient;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $queueName
     * @param \Generated\Shared\Transfer\QueueSendMessageTransfer $queueSendMessageTransfer
     */
    public function sendMessage(
        string $queueName,
        QueueSendMessageTransfer $queueSendMessageTransfer
    ): void
    {
        $this
            ->queueClient
            ->sendMessage($queueName, $queueSendMessageTransfer);
    }
}
