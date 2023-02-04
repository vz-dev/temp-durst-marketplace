<?php

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Mail\MailConstants;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Shared\Queue\QueueConstants;
use Spryker\Shared\RabbitMq\RabbitMqConstants;

// ---------- Propel
$config[PropelConstants::ZED_DB_DATABASE] = 'DE_development_zed';

// ---------- MailCatcher
$config[MailConstants::MAILCATCHER_GUI] = sprintf('http://%s:1080', $config[ApplicationConstants::HOST_ZED]);

// ---------- Queue
$config[QueueConstants::QUEUE_WORKER_INTERVAL_MILLISECONDS] = 1000;
$config[QueueConstants::QUEUE_WORKER_LOG_ACTIVE] = false;
$config[QueueConstants::QUEUE_WORKER_OUTPUT_FILE_NAME] = 'data/DE/logs/ZED/queue.out';

// ---------- RabbitMQ
$config[RabbitMqConstants::RABBITMQ_USERNAME] = 'DE_development';
$config[RabbitMqConstants::RABBITMQ_VIRTUAL_HOST] = '/DE_development_zed';
