<?php
/**
 * Durst - project - InvoiceDelayQueueProcessorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.01.20
 * Time: 15:03
 */

namespace Pyz\Zed\Easybill\Communication\Plugin;

use Exception;
use Pyz\Zed\Easybill\Business\Exception\TooManyRequestException;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Queue\Dependency\Plugin\QueueMessageProcessorPluginInterface;

/**
 * Class InvoiceDelayQueueProcessorPlugin
 * @package Pyz\Zed\Easybill\Communication\Plugin
 * @method \Pyz\Zed\Easybill\EasybillConfig getConfig()
 * @method \Pyz\Zed\Easybill\Business\EasybillFacade getFacade()
 */
class InvoiceDelayQueueProcessorPlugin extends AbstractPlugin implements QueueMessageProcessorPluginInterface
{
    /**
     * @inheritDoc
     */
    public function processMessages(array $queueMessageTransfers)
    {
        foreach ($queueMessageTransfers as $queueMessageTransfer) {
            try {
                //TODO pass reference to facade
                $reference = (int) $queueMessageTransfer->getQueueMessage()->getBody();
                $this
                    ->getFacade()
                    ->createInvoice();
                $queueMessageTransfer->setAcknowledge(true);
            } catch (TooManyRequestException $exception) {
                $queueMessageTransfer->setAcknowledge(false);
            } catch (Exception $exception) {
                $queueMessageTransfer->setHasError(true);
            }
        }

        return $queueMessageTransfers;
    }

    /**
     * @inheritDoc
     */
    public function getChunkSize()
    {
        return $this
            ->getConfig()
            ->getInvoiceDelayQueueChunkSize();
    }
}
