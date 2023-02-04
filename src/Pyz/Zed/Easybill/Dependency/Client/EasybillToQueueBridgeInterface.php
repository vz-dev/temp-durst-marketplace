<?php
/**
 * Durst - project - EasybillToQueueBridgeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.01.20
 * Time: 16:25
 */

namespace Pyz\Zed\Easybill\Dependency\Client;


use Generated\Shared\Transfer\QueueSendMessageTransfer;

interface EasybillToQueueBridgeInterface
{
    /**
     * @param string $queueName
     * @param \Generated\Shared\Transfer\QueueSendMessageTransfer $queueSendMessageTransfer
     */
    public function sendMessage(
        string $queueName,
        QueueSendMessageTransfer $queueSendMessageTransfer
    ): void;
}
