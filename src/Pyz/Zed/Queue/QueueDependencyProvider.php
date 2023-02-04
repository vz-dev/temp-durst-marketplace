<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Queue;

use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Shared\Easybill\EasybillConstants;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Queue\CsvTimeSlotExporterQueueProcessorPlugin;
use Pyz\Zed\Easybill\Communication\Plugin\InvoiceDelayQueueProcessorPlugin;
use Spryker\Shared\Event\EventConstants;
use Spryker\Zed\Event\Communication\Plugin\Queue\EventQueueMessageProcessorPlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Queue\Dependency\Plugin\QueueMessageProcessorPluginInterface;
use Spryker\Zed\Queue\QueueDependencyProvider as SprykerDependencyProvider;

class QueueDependencyProvider extends SprykerDependencyProvider
{
    /**
     * @param Container $container
     *
     * @return QueueMessageProcessorPluginInterface[]
     */
    protected function getProcessorMessagePlugins(Container $container)
    {
        return [
            EventConstants::EVENT_QUEUE => new EventQueueMessageProcessorPlugin(),
            EasybillConstants::INVOICE_DELAY_QUEUE => new InvoiceDelayQueueProcessorPlugin(),
            DeliveryAreaConstants::DELIVER_AREA_CSV_TIME_SLOT_EXPORT_QUEUE_NAME => new CsvTimeSlotExporterQueueProcessorPlugin(),
        ];
    }
}
