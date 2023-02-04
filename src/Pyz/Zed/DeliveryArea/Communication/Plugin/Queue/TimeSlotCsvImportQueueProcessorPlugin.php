<?php
/**
 * Durst - project - TimeSlotCsvImportQueueProcessorPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-10-13
 * Time: 20:18
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Queue;


use Exception;
use Generated\Shared\Transfer\QueueReceiveMessageTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Queue\Dependency\Plugin\QueueMessageProcessorPluginInterface;

/**
 * Class TimeSlotCsvImportQueueProcessorPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Queue
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class TimeSlotCsvImportQueueProcessorPlugin extends AbstractPlugin implements QueueMessageProcessorPluginInterface
{
    /**
     * Specification:
     * - This plugin interface is used for message processing for the queues,
     *   by implementing this and adding to QueueDependencyProvider::getProcessorMessagePlugins()
     *   for specific queue, receives messages will pass to this method for processing
     *
     * @param QueueReceiveMessageTransfer[] $queueMessageTransfers
     *
     * @return QueueReceiveMessageTransfer[]
     * @api
     *
     */
    public function processMessages(array $queueMessageTransfers)
    {
        foreach ($queueMessageTransfers as $queueMessageTransfer)
        {
            try {
                $this
                    ->getFacade()
                    ->importTimeSlotsForBranchByCsv(
                            $queueMessageTransfer
                                ->getQueueMessage()
                                ->getBody()
                    );

                $queueMessageTransfer->setAcknowledge(true);
            } catch (Exception $exception) {
                $queueMessageTransfer->getQueueMessage()->setBody($exception->getMessage());
                $queueMessageTransfer->setHasError(true);
                $queueMessageTransfer->setAcknowledge(true);
            }
        }

        return $queueMessageTransfers;
    }

    /**
     * Specification:
     * - Returns the number of messages which need to fetch
     *   from queue
     *
     * @return int
     * @api
     *
     */
    public function getChunkSize()
    {
        return 1;
    }

}
