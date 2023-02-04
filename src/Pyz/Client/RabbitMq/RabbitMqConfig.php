<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Client\RabbitMq;

use ArrayObject;
use Generated\Shared\Transfer\RabbitMqOptionTransfer;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Shared\Easybill\EasybillConstants;
use Spryker\Client\RabbitMq\Model\Connection\Connection;
use Spryker\Client\RabbitMq\RabbitMqConfig as SprykerRabbitMqConfig;
use Spryker\Shared\Event\EventConstants;
use Spryker\Shared\Log\LogConstants;

class RabbitMqConfig extends SprykerRabbitMqConfig
{
    /**
     * @return ArrayObject
     */
    protected function getQueueOptions()
    {
        $queueOptionCollection = new ArrayObject();
        $queueOptionCollection->append(
            $this->createQueueOption(
                EventConstants::EVENT_QUEUE,
                EventConstants::EVENT_QUEUE_ERROR
            )
        );
        $queueOptionCollection->append(
            $this->createQueueOption(
                $this->get(LogConstants::LOG_QUEUE_NAME),
                $this->get(LogConstants::LOG_ERROR_QUEUE_NAME)
            )
        );
        $queueOptionCollection->append(
            $this->createQueueOption(
                $this->get(EasybillConstants::INVOICE_DELAY_QUEUE),
                $this->get(EasybillConstants::INVOICE_DELAY_ERROR_QUEUE)
            )
        );
        $queueOptionCollection->append(
            $this->createQueueOption(
                DeliveryAreaConstants::DELIVER_AREA_CSV_TIME_SLOT_EXPORT_QUEUE_NAME,
                DeliveryAreaConstants::DELIVER_AREA_CSV_TIME_SLOT_EXPORT_QUEUE_NAME_ERROR
            )
        );

        $queueOptionCollection->append(
            $this->createQueueOption(
                DeliveryAreaConstants::DELIVERY_AREA_CSV_TIME_SLOT_IMPORT_QUEUE_NAME,
                DeliveryAreaConstants::DELIVERY_AREA_CSV_TIME_SLOT_IMPORT_QUEUE_NAME_ERROR
            )
        );

        return $queueOptionCollection;
    }

    /**
     * @param string $queueName
     * @param string $errorQueueName
     * @param string $routingKey
     *
     * @return RabbitMqOptionTransfer
     */
    protected function createQueueOption($queueName, $errorQueueName, $routingKey = 'error')
    {
        $queueOptionTransfer = new RabbitMqOptionTransfer();
        $queueOptionTransfer
            ->setQueueName($queueName)
            ->setDurable(true)
            ->setType('direct')
            ->setDeclarationType(Connection::RABBIT_MQ_EXCHANGE)
            ->addBindingQueueItem($this->createQueueBinding($queueName))
            ->addBindingQueueItem($this->createErrorQueueBinding($errorQueueName, $routingKey));

        return $queueOptionTransfer;
    }

    /**
     * @param string $queueName
     *
     * @return RabbitMqOptionTransfer
     */
    protected function createQueueBinding($queueName)
    {
        $queueOptionTransfer = new RabbitMqOptionTransfer();
        $queueOptionTransfer
            ->setQueueName($queueName)
            ->setDurable(true)
            ->addRoutingKey('');

        return $queueOptionTransfer;
    }

    /**
     * @param string $errorQueueName
     * @param string $routingKey
     *
     * @return RabbitMqOptionTransfer
     */
    protected function createErrorQueueBinding($errorQueueName, $routingKey)
    {
        $queueOptionTransfer = new RabbitMqOptionTransfer();
        $queueOptionTransfer
            ->setQueueName($errorQueueName)
            ->setDurable(true)
            ->addRoutingKey($routingKey);

        return $queueOptionTransfer;
    }
}
