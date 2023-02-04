<?php
/**
 * Durst - project - CsvTimeSlotExporterQueueProcessorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 01.10.20
 * Time: 12:29
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Queue;

use Exception;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Queue\Dependency\Plugin\QueueMessageProcessorPluginInterface;

/**
 * Class CsvTimeSlotExporterQueueProcessorPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Queue
 * @method \Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface getFacade()
 * @method \Pyz\Zed\DeliveryArea\DeliveryAreaConfig getConfig()
 */
class CsvTimeSlotExporterQueueProcessorPlugin extends AbstractPlugin implements QueueMessageProcessorPluginInterface
{
    protected const CHUNK_SIZE = 1;

    /**
     * @param \Generated\Shared\Transfer\QueueReceiveMessageTransfer[] $queueMessageTransfers
     *
     * @return \Generated\Shared\Transfer\QueueReceiveMessageTransfer[]
     */
    public function processMessages(array $queueMessageTransfers)
    {
        foreach ($queueMessageTransfers as $queueMessageTransfer) {
            try {
                $data = $this->parseMessage($queueMessageTransfer->getQueueMessage()->getBody());

                $this
                    ->getFacade()
                    ->createTimeSlotExportAndSendToEmailForBranch(
                        $data[$this->getConfig()->getQueueKeyIdBranch()],
                        $data[$this->getConfig()->getQueueKeyEmail()],
                        $data[$this->getConfig()->getQueueKeyPage()],
                        $data[$this->getConfig()->getQueueKeyFilename()]
                    );

                $queueMessageTransfer->setAcknowledge(true);
            } catch (Exception $exception) {
                $queueMessageTransfer->getQueueMessage()->setBody(
                    'error: ' . $exception->getTraceAsString()
                );
                $queueMessageTransfer->setHasError(true);
            }
        }

        return $queueMessageTransfers;
    }

    /**
     * @return int
     */
    public function getChunkSize()
    {
        return static::CHUNK_SIZE;
    }

    /**
     * @param string $message
     *
     * @return array
     */
    protected function parseMessage(string $message): array
    {
        return json_decode($message, true);
    }
}
